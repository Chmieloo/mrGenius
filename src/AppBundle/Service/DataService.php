<?php

namespace AppBundle\Service;

use AppBundle\Model\Element;
use AppBundle\Model\Player;
use AppBundle\Model\Team;
use Doctrine\DBAL\Connection;
use Phpml\Regression\LeastSquares;
use Phpml\Regression\SVR;
use Phpml\SupportVectorMachine\Kernel;

class DataService
{
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
            ->setParameter('types', $types, Connection::PARAM_INT_ARRAY);

        $result = $query->execute()->fetchAll(\PDO::FETCH_ASSOC);

        $playerHistory = [];
        foreach ($result as $data) {
            $playerId = $data['playerId'];
            $playerHistory[$playerId][] = $data;
        }

        return $playerHistory;
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
}
