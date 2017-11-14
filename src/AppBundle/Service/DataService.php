<?php

namespace AppBundle\Service;

use AppBundle\Model\Element;
use AppBundle\Model\Player;
use AppBundle\Model\Team;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Type;
use Phpml\Regression\LeastSquares;
use Phpml\Regression\SVR;
use Phpml\SupportVectorMachine\Kernel;

class DataService
{
    private $nextEvent = 12;

    private $db;

    /**
     * DataService constructor.
     * @param Connection $db
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    /**
     * Current state of the game
     * @var string
     */
    public static $fplCurrentStateFeedUrl = 'https://fantasy.premierleague.com/drf/bootstrap-static';

    /**
     * @return Player[] array
     */
    public function getAllPlayers()
    {
        $players = [];
        $content = file_get_contents(static::$fplCurrentStateFeedUrl);
        $jsonContent = json_decode($content);

        $playerList = $jsonContent->{'elements'};
        foreach ($playerList as $playerData) {
            $playerId = $playerData->{'id'};
            $teamId = $playerData->{'team'};

            $players[$playerId] = new Player([
                'id' => $playerId,
                'teamId' => $teamId,
                'elementType' => $playerData->{'element_type'},
                'firstName' => $playerData->{'first_name'},
                'secondName' => $playerData->{'second_name'},
                'photo' => $playerData->{'photo'},
                'form' => $playerData->{'form'},
                'nowCost' => $playerData->{'now_cost'},
                'totalPoints' => $playerData->{'total_points'},
                'pointsPerGame' => $playerData->{'points_per_game'},
                'ictIndex' => $playerData->{'ict_index'},
                'influence' => $playerData->{'influence'},
                'creativity' => $playerData->{'creativity'},
                'threat' => $playerData->{'threat'},
                'chanceOfPlayingNextRound' => $playerData->{'chance_of_playing_next_round'},
            ]);
        }

        return $players;
    }

    /**
     * @return array
     */
    public function loadAll()
    {
        $query = $this->db->createQueryBuilder()
            ->select('
                id as id,
                team_id as teamId,
                type as type,
                first_name as firstName,
                second_name as secondName,
                photo as photo,
                form as form,
                now_cost as nowCost,
                total_points as totalPoints,
                ppg as pointsPerGame,
                influence as influence,
                creativity as creativity,
                threat as threat,
                chance_of_playing_next_round as chanceOfPlayingNextRound
            ')
            ->from('players', 'p');

        $result = $query->execute()->fetchAll(\PDO::FETCH_ASSOC);

        return $this->generate($result);
    }

    /**
     * @param $data
     * @return Player[]
     */
    private function generate($data)
    {
        $players = [];
        foreach ($data as $datum)
        {
            /** @var Player $player */
            $player = $this->generateOne($datum);
            $playerId = $player->getId();
            $players[$playerId] = $player;
        }

        return $players;
    }

    private function generateOne($data)
    {
        return new Player([
            'id' => $data['id'],
            'teamId' => $data['teamId'],
            'type' => $data['type'],
            'firstName' => $data['firstName'],
            'secondName' => $data['secondName'],
            'photo' => $data['photo'],
            'form' => $data['form'],
            'nowCost' => $data['nowCost'],
            'totalPoints' => $data['totalPoints'],
            'pointsPerGame' => $data['pointsPerGame'],
            'influence' => $data['influence'],
            'creativity' => $data['creativity'],
            'threat' => $data['threat'],
            'chanceOfPlayingNextRound' => $data['chanceOfPlayingNextRound'],
        ]);
    }

    public function loadPlayerFixturesByType($types, $eventId = 0)
    {
        $query = $this->db->createQueryBuilder()
            ->select('
              p.first_name as firstName,
              p.second_name as secondName, 
              p.total_points as totalPoints,
              f.id as id,
              f.event_id as eventId,
              f.kickoff as kickoff,
              f.player_id as playerId,
              f.is_home as isHome,
              p.influence as influence,
              p.creativity as creativity,
              p.threat as threat,
              p.type as type,
              p.now_cost as value,
              p.team_id as teamId,
              if(f.is_home=1,teamHome.strength_overall_home,teamAway.strength_overall_away) as teamStrength,
              if(f.is_home=1,teamAway.strength_overall_away,teamHome.strength_overall_home) as opponentStrength
            ')
            ->from('fixtures', 'f')
            ->join('f', 'players', 'p', 'p.id = f.player_id')
            ->join('f', 'teams', 'teamHome', 'teamHome.id = f.team_h')
            ->join('f', 'teams', 'teamAway', 'teamAway.id = f.team_a')
            ->where('p.type IN (:types)')
            ->setParameter('types', $types, Connection::PARAM_INT_ARRAY);

        if ($eventId) {
            $query->addSelect('f.event_id as eventId')
                ->andWhere('f.event_id = :eventId')
                ->setParameter('eventId', $eventId);
        }

        $result = $query->execute()->fetchAll(\PDO::FETCH_ASSOC);

        $playerFixtures = [];
        foreach ($result as $data) {
            $playerId = $data['playerId'];
            $eventId = $data['eventId'];
            $playerFixtures[$playerId][$eventId] = $data;
        }

        return $playerFixtures;
    }

    public function loadPlayersHistoryByType($types)
    {
        $query = $this->db->createQueryBuilder()
            ->select('
                h.id as id,
                h.player_id as playerId,
                h.round as round,
                h.minutes,
                h.total_points as totalPoints,
                h.was_home as wasHome,
                h.influence as influence,
                h.creativity as creativity,
                h.threat as threat,
                h.value as value,
                p.influence as nowInfluence,
                p.creativity as nowCreativity,
                p.threat as nowThreat,
                p.type,
                if(h.was_home=1,t1.strength_overall_home,t1.strength_overall_away) as teamStrength,
                if(h.was_home=1,t2.strength_overall_away,t2.strength_overall_home) as opponentStrength
            ')
            ->from('history', 'h')
            ->join('h', 'players', 'p', 'p.id = h.player_id')
            ->join('h', 'teams', 't1', 't1.id = h.team_id')
            ->join('h', 'teams', 't2', 't2.id = h.opponent_team')
            ->where('p.type IN (:types)')
            ->andWhere('p.chance_of_playing_next_round >= 75')
            ->orWhere('p.chance_of_playing_next_round IS NULL')
            ->setParameter('types', $types, Connection::PARAM_INT_ARRAY);

        $result = $query->execute()->fetchAll(\PDO::FETCH_ASSOC);

        $playerHistory = [];
        foreach ($result as $data) {
            $playerId = $data['playerId'];
            $playerHistory[$playerId][] = $data;
        }

        return $playerHistory;
    }

    public function loadPlayersHistoryByPlayerIds($playerIds)
    {
        $query = $this->db->createQueryBuilder()
            ->select('
                h.id as id,
                h.player_id as playerId,
                h.round as round,
                h.minutes,
                h.total_points as totalPoints,
                h.was_home as wasHome,
                h.influence as influence,
                h.creativity as creativity,
                h.threat as threat,
                h.value as value,
                p.influence as nowInfluence,
                p.creativity as nowCreativity,
                p.threat as nowThreat,
                p.type,
                if(h.was_home=1,t1.strength_overall_home,t1.strength_overall_away) as teamStrength,
                if(h.was_home=1,t2.strength_overall_away,t2.strength_overall_home) as opponentStrength
            ')
            ->from('history', 'h')
            ->join('h', 'players', 'p', 'p.id = h.player_id')
            ->join('h', 'teams', 't1', 't1.id = h.team_id')
            ->join('h', 'teams', 't2', 't2.id = h.opponent_team')
            ->where('h.player_id IN (:playerIds)')
            ->setParameter('playerIds', $playerIds, Connection::PARAM_INT_ARRAY);

        $result = $query->execute()->fetchAll(\PDO::FETCH_ASSOC);

        $playerHistory = [];
        foreach ($result as $data) {
            $playerId = $data['playerId'];
            $playerHistory[$playerId][] = $data;
        }

        return $playerHistory;
    }

    public function loadPlayerFixturesByPlayerIds($playerIds, $eventId = 0)
    {
        $query = $this->db->createQueryBuilder()
            ->select('
              p.first_name as firstName,
              p.second_name as secondName, 
              p.total_points as totalPoints,
              f.id as id,
              f.event_id as eventId,
              f.kickoff as kickoff,
              f.player_id as playerId,
              f.is_home as isHome,
              p.influence as influence,
              p.creativity as creativity,
              p.threat as threat,
              p.type as type,
              p.now_cost as value,
              p.team_id as teamId,
              if(f.is_home=1,teamHome.strength_overall_home,teamAway.strength_overall_away) as teamStrength,
              if(f.is_home=1,teamAway.strength_overall_away,teamHome.strength_overall_home) as opponentStrength
            ')
            ->from('fixtures', 'f')
            ->join('f', 'players', 'p', 'p.id = f.player_id')
            ->join('f', 'teams', 'teamHome', 'teamHome.id = f.team_h')
            ->join('f', 'teams', 'teamAway', 'teamAway.id = f.team_a')
            ->where('p.id IN (:playerIds)')
            ->setParameter('playerIds', $playerIds, Connection::PARAM_INT_ARRAY);

        if ($eventId) {
            $query->addSelect('f.event_id as eventId')
                ->andWhere('f.event_id = :eventId')
                ->setParameter('eventId', $eventId);
        }

        $result = $query->execute()->fetchAll(\PDO::FETCH_ASSOC);

        $playerFixtures = [];
        foreach ($result as $data) {
            $playerId = $data['playerId'];
            $eventId = $data['eventId'];
            $playerFixtures[$playerId][$eventId] = $data;
        }

        return $playerFixtures;
    }

    /**
     * @param $samples
     * @param $data
     * @param $nextSample
     * @return mixed
     */
    public function predictRegression($samples, $data, $nextSample)
    {
        $regression = new SVR(Kernel::LINEAR);
        $regression->train($samples, $data);
        $value = $regression->predict($nextSample);

        return $value;
    }

    public function getCurrentTeamPredictions()
    {
        $playerIds = [260, 264, 245, 382, 97, 13, 255, 247, 374, 285, 394, 420, 367, 63, 151];

        $tableData = [];

        $playerHistory = $this->loadPlayersHistoryByPlayerIds($playerIds);
        $playerFixtures = $this->loadPlayerFixturesByPlayerIds($playerIds, $this->nextEvent);

        foreach ($playerHistory as $attackerId => $attackerData) {
            $type = $attackerData['type'];
            $avgMinutes = $this->avgMinutes($attackerData);
            $avgPoints = $this->avgPoints($attackerData);
            if ($avgMinutes > 10 && $avgPoints > 1) {
                $currentPlayerHistory = $playerHistory[$attackerId];
                $currentPlayerFixture = $playerFixtures[$attackerId][$this->nextEvent];

                # Predict i,c,t first
                list($predictedPlayerInfluence, $predictedPlayerCreativity, $predictedPlayerThreat) =
                    $this->predictICT($currentPlayerHistory, $currentPlayerFixture);

                if ($predictedPlayerInfluence) {
                    # Get historical data and create samples and point results
                    foreach ($currentPlayerHistory as $roundData) {
                        if ($type == Player::POSITION_GOALKEEPER) {
                            $samples[] = [
                                $roundData['influence'],
                                $roundData['value'],
                                $roundData['teamStrength'],
                                $roundData['opponentStrength'],
                            ];
                        } else {
                            $samples[] = [
                                $roundData['influence'],
                                $roundData['creativity'],
                                $roundData['threat'],
                                $roundData['value'],
                                $roundData['teamStrength'],
                                $roundData['opponentStrength'],
                            ];
                        }
                        $data[] = $roundData['totalPoints'];
                    }

                    # Get fixture data
                    if ($type == Player::POSITION_GOALKEEPER) {
                        $predictionSample = [
                            $predictedPlayerInfluence,
                            $currentPlayerFixture['value'],
                            $currentPlayerFixture['teamStrength'],
                            $currentPlayerFixture['opponentStrength'],
                        ];
                    } else {
                        $predictionSample = [
                            $predictedPlayerInfluence,
                            $predictedPlayerCreativity,
                            $predictedPlayerThreat,
                            $currentPlayerFixture['value'],
                            $currentPlayerFixture['teamStrength'],
                            $currentPlayerFixture['opponentStrength'],
                        ];
                    }

                    # Predict points
                    $prediction = $this->predictRegression($samples, $data, $predictionSample);

                    $currentPlayerFixture['predictedInfluence'] = $predictedPlayerInfluence;
                    $currentPlayerFixture['predictedCreativity'] = $predictedPlayerCreativity;
                    $currentPlayerFixture['predictedThreat'] = $predictedPlayerThreat;
                    $currentPlayerFixture['predictedPoints'] = $prediction;
                    $tableData[$attackerId] = $currentPlayerFixture;
                }
            }
        }

        $this->db->executeQuery('truncate table myteam_predictions');
        $this->importMyTeamPredictedData($tableData);

        return $tableData;
    }

    public function predictPlayersPointsByPlayerIds()
    {
        $playerIds = [];
        $myTeam = "https://fantasy.premierleague.com/drf/my-team/5304993/";
        $content = file_get_contents($myTeam);
        $objectContent = json_decode($content);
        $players = $objectContent->{'picks'};
        foreach ($players as $item) {
            $playerIds[] = $item->{'element'};
        }

        var_dump($playerIds);
    }

    /**
     * @param $type
     * @return array
     */
    public function predictPlayersPointsByType($type)
    {
        $tableData = [];

        $playerHistory = $this->loadPlayersHistoryByType([$type]);
        $playerFixtures = $this->loadPlayerFixturesByType([$type], $this->nextEvent);

        foreach ($playerHistory as $attackerId => $attackerData) {
            $avgMinutes = $this->avgMinutes($attackerData);
            $avgPoints = $this->avgPoints($attackerData);
            if ($avgMinutes > 10 && $avgPoints > 1) {
                $currentPlayerHistory = $playerHistory[$attackerId];
                $currentPlayerFixture = $playerFixtures[$attackerId][$this->nextEvent];

                # Predict i,c,t first
                list($predictedPlayerInfluence, $predictedPlayerCreativity, $predictedPlayerThreat) =
                    $this->predictICT($currentPlayerHistory, $currentPlayerFixture);

                if ($predictedPlayerInfluence) {
                    # Get historical data and create samples and point results
                    foreach ($currentPlayerHistory as $roundData) {
                        if ($type == Player::POSITION_GOALKEEPER) {
                            $samples[] = [
                                $roundData['influence'],
                                $roundData['value'],
                                $roundData['teamStrength'],
                                $roundData['opponentStrength'],
                            ];
                        } else {
                            $samples[] = [
                                $roundData['influence'],
                                $roundData['creativity'],
                                $roundData['threat'],
                                $roundData['value'],
                                $roundData['teamStrength'],
                                $roundData['opponentStrength'],
                            ];
                        }
                        $data[] = $roundData['totalPoints'];
                    }

                    # Get fixture data
                    if ($type == Player::POSITION_GOALKEEPER) {
                        $predictionSample = [
                            $predictedPlayerInfluence,
                            $currentPlayerFixture['value'],
                            $currentPlayerFixture['teamStrength'],
                            $currentPlayerFixture['opponentStrength'],
                        ];
                    } else {
                        $predictionSample = [
                            $predictedPlayerInfluence,
                            $predictedPlayerCreativity,
                            $predictedPlayerThreat,
                            $currentPlayerFixture['value'],
                            $currentPlayerFixture['teamStrength'],
                            $currentPlayerFixture['opponentStrength'],
                        ];
                    }

                    # Predict points
                    $prediction = $this->predictRegression($samples, $data, $predictionSample);

                    $currentPlayerFixture['predictedInfluence'] = $predictedPlayerInfluence;
                    $currentPlayerFixture['predictedCreativity'] = $predictedPlayerCreativity;
                    $currentPlayerFixture['predictedThreat'] = $predictedPlayerThreat;
                    $currentPlayerFixture['predictedPoints'] = $prediction;
                    $tableData[$attackerId] = $currentPlayerFixture;
                }
            }
        }

        $this->db->executeQuery('DELETE FROM predictions WHERE type = ' . $type .  ' AND event_id = ' . $this->nextEvent);
        $this->importPredictedData($tableData);

        return $tableData;
    }

    /**
     * @param $data
     * @return int
     */
    private function avgMinutes($data)
    {
        $minutes = 0;
        $eventCount = count($data);
        foreach ($data as $datum) {
            $minutes += $datum['minutes'];
        }
        $avgMinutes = (int)($minutes / $eventCount);

        return $avgMinutes;
    }

    /**
     * @param $data
     * @return int
     */
    private function avgPoints($data)
    {
        $totalPoints = 0;
        $eventCount = count($data);
        foreach ($data as $datum) {
            $totalPoints += $datum['totalPoints'];
        }
        $avgPoints = (int)($totalPoints / $eventCount);

        return $avgPoints;
    }

    private function predictICT($playerHistory, $playerFixture)
    {
        $samples = [];
        $predictionI = $predictionC = $predictionT = 0;
        $dataI = [];
        $dataC = [];
        $dataT = [];

        foreach ($playerHistory as $roundData) {
            $samples[] = [
                $roundData['value'],
                $roundData['teamStrength'],
                $roundData['opponentStrength'],
            ];
            $dataI[] = $roundData['influence'];
            $dataC[] = $roundData['creativity'];
            $dataT[] = $roundData['threat'];
        }

        $predictionSample = [
            $playerFixture['value'],
            $playerFixture['teamStrength'],
            $playerFixture['opponentStrength'],
        ];

        if ($samples) {
            if ($dataI) {
                $predictionI = $this->predictRegression($samples, $dataI, $predictionSample);
            }
            if ($dataC) {
                $predictionC = $this->predictRegression($samples, $dataC, $predictionSample);
            }
            if ($dataT) {
                $predictionT = $this->predictRegression($samples, $dataT, $predictionSample);
            }
        }

        return [$predictionI, $predictionC, $predictionT];
    }

    /**
     * @param $data
     * @return bool
     */
    private function importPredictedData($data)
    {
        $sql = "INSERT INTO predictions (
                        event_id,
                        player_id,
                        team_id,
                        type,
                        pi,
                        pc,
                        pt,
                        pp,
                        ap
                    ) VALUES ";

        foreach ($data as $predictionForPlayer) {
            $sql .=
                "(" . $this->nextEvent . "," .
                $predictionForPlayer['playerId'] . "," .
                $predictionForPlayer['teamId'] . "," .
                $predictionForPlayer['type'] . ",'" .
                $predictionForPlayer['predictedInfluence'] . "','" .
                $predictionForPlayer['predictedCreativity'] . "','" .
                $predictionForPlayer['predictedThreat'] . "','" .
                $predictionForPlayer['predictedPoints'] . "', 0),";
        }

        $sql = trim($sql, ",");

        $stmt = $this->db->prepare($sql);

        return $stmt->execute();
    }

    /**
     * @param $data
     * @return bool
     */
    private function importMyTeamPredictedData($data)
    {
        $sql = "INSERT INTO myteam_predictions (
                        player_id,
                        team_id,
                        type,
                        pi,
                        pc,
                        pt,
                        pp,
                        ap
                    ) VALUES ";

        foreach ($data as $predictionForPlayer) {
            $sql .=
                "(" .
                $predictionForPlayer['playerId'] . "," .
                $predictionForPlayer['teamId'] . "," .
                $predictionForPlayer['type'] . ",'" .
                $predictionForPlayer['predictedInfluence'] . "','" .
                $predictionForPlayer['predictedCreativity'] . "','" .
                $predictionForPlayer['predictedThreat'] . "','" .
                $predictionForPlayer['predictedPoints'] . "', 0),";
        }

        $sql = trim($sql, ",");

        $stmt = $this->db->prepare($sql);

        return $stmt->execute();
    }

    public function getAllPredictions()
    {
        $query = $this->db->createQueryBuilder()
            ->select('
                pr.event_id as eventId,
                pr.player_id as playerId,
                pr.team_id as teamId,
                pos.name as position,
                pr.pi,
                pr.pc,
                pr.pt,
                pr.pp,
                pr.ap,
                t.name as teamName,
                p.first_name as firstName,
                p.second_name as secondName,
                p.now_cost as costNow,
                p.total_points as totalPoints,
                p.form as form,
                p.ppg as ppg
            ')
            ->from('predictions', 'pr')
            ->join('pr', 'teams', 't', 'pr.team_id = t.id')
            ->join('pr', 'positions', 'pos', 'pos.id = pr.type')
            ->join('pr', 'players', 'p', 'p.id = pr.player_id');

        $result = $query->execute()->fetchAll(\PDO::FETCH_ASSOC);

        return $result;
    }

    public function getMyTeamPredictions()
    {
        $query = $this->db->createQueryBuilder()
            ->select('
                pr.player_id as playerId,
                pr.team_id as teamId,
                pos.name as position,
                pr.pi,
                pr.pc,
                pr.pt,
                pr.pp,
                pr.ap,
                t.name as teamName,
                p.first_name as firstName,
                p.second_name as secondName,
                p.now_cost as costNow,
                p.total_points as totalPoints,
                p.form as form,
                p.ppg as ppg
            ')
            ->from('myteam_predictions', 'pr')
            ->join('pr', 'teams', 't', 'pr.team_id = t.id')
            ->join('pr', 'positions', 'pos', 'pos.id = pr.type')
            ->join('pr', 'players', 'p', 'p.id = pr.player_id');

        $result = $query->execute()->fetchAll(\PDO::FETCH_ASSOC);

        return $result;
    }
}
