<?php

namespace AppBundle\Service;

use AppBundle\Model\Event;
use AppBundle\Model\Player;
use AppBundle\Model\PlayerMatchFixture;
use AppBundle\Model\PlayerMatchHistory;
use AppBundle\Model\Team;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

class ImportService
{
    private $db;

    /** @var DataService $dataService */
    private $dataService;

    /**
     * OptionsService constructor.
     * @param Connection $db
     * @param DataService $dataService
     */
    public function __construct(Connection $db, DataService $dataService)
    {
        $this->db = $db;
        $this->dataService = $dataService;
    }

    public function importPlayers()
    {
        $currentStateDataUrl = $this->dataService::$fplCurrentStateFeedUrl;
        $currentStateData = file_get_contents($currentStateDataUrl);
        $currentStateDataJson = json_decode($currentStateData);

        $teamsNode = $currentStateDataJson->{'elements'};
        foreach ($teamsNode as $player) {
            $playerId = $player->{'id'};
            $players[$playerId] = new Player([
                'id' => $playerId,
                'teamId' => $player->{'team'},
                'type' => $player->{'element_type'},
                'firstName' => $player->{'first_name'},
                'secondName' => $player->{'second_name'},
                'photo' => $player->{'photo'},
                'form' => $player->{'form'},
                'nowCost' => $player->{'now_cost'},
                'totalPoints' => $player->{'total_points'},
                'pointsPerGame' => $player->{'points_per_game'},
                'influence' => $player->{'influence'},
                'creativity' => $player->{'creativity'},
                'threat' => $player->{'threat'},
                'chanceOfPlayingNextRound' => $player->{'chance_of_playing_next_round'},
            ]);

            /** @var Player $currentPlayer */
            $currentPlayer = $players[$playerId];

            $sql = "INSERT INTO players 
                (
                id, 
                team_id, 
                first_name,
                second_name,
                now_cost,
                chance_of_playing_next_round,
                total_points,
                form,
                ppg,
                influence,
                creativity,
                threat,
                type,
                photo
                ) 
                VALUES (
                :id,
                :team_id, 
                :first_name,
                :second_name,
                :now_cost,
                :chance_of_playing_next_round,
                :total_points,
                :form,
                :ppg,
                :influence,
                :creativity,
                :threat,
                :type,
                :photo
                ) ON DUPLICATE KEY UPDATE
                now_cost = :now_cost,
                chance_of_playing_next_round = :chance_of_playing_next_round,
                form = :form,
                ppg = :ppg,
                influence = :influence,
                creativity = :creativity,
                threat = :threat
                ";

            $stmt = $this->db->prepare($sql);
            $stmt->bindValue('id', $currentPlayer->getId());
            $stmt->bindValue('team_id', $currentPlayer->getTeamId());
            $stmt->bindValue('first_name', $currentPlayer->getFirstName());
            $stmt->bindValue('second_name', $currentPlayer->getSecondName());
            $stmt->bindValue('now_cost', $currentPlayer->getNowCost());
            $stmt->bindValue('chance_of_playing_next_round', $currentPlayer->getChanceOfPlayingNextRound());
            $stmt->bindValue('total_points', $currentPlayer->getTotalPoints());
            $stmt->bindValue('form', $currentPlayer->getForm());
            $stmt->bindValue('ppg', $currentPlayer->getPointsPerGame());
            $stmt->bindValue('influence', $currentPlayer->getInfluence());
            $stmt->bindValue('creativity', $currentPlayer->getCreativity());
            $stmt->bindValue('threat', $currentPlayer->getThreat());
            $stmt->bindValue('type', $currentPlayer->getType());
            $stmt->bindValue('photo', $currentPlayer->getPhoto());
            $stmt->execute();
        }

        return $players;
    }

    public function importTeams()
    {
        $currentStateDataUrl = $this->dataService::$fplCurrentStateFeedUrl;
        $currentStateData = file_get_contents($currentStateDataUrl);
        $currentStateDataJson = json_decode($currentStateData);

        $teamsNode = $currentStateDataJson->{'teams'};
        foreach ($teamsNode as $team) {
            $teamId = $team->{'id'};
            $teams[$teamId] = new Team([
                'id' => $teamId,
                'name' => $team->{'name'},
                'shortName' => $team->{'short_name'},
                'strengthOverallHome' => $team->{'strength_overall_home'},
                'strengthOverallAway' => $team->{'strength_overall_away'},
                'strengthAttackHome' => $team->{'strength_attack_home'},
                'strengthAttackAway' => $team->{'strength_attack_away'},
                'strengthDefenceHome' => $team->{'strength_defence_home'},
                'strengthDefenceAway' => $team->{'strength_defence_away'},
            ]);

            /** @var Team $currentTeam */
            $currentTeam = $teams[$teamId];

            $hasTeam = $this->checkDbForTeam($currentTeam->getId());
            if (!$hasTeam) {
                $sql = "INSERT INTO teams 
                    (
                    id, 
                    name, 
                    short_name,
                    unavailable,
                    strength_overall_home,
                    strength_overall_away,
                    strength_attack_home,
                    strength_attack_away,
                    strength_defence_home,
                    strength_defence_away
                    ) 
                    VALUES (
                    :id,
                    :name, 
                    :short_name,
                    0,
                    :strength_overall_home,
                    :strength_overall_away,
                    :strength_attack_home,
                    :strength_attack_away,
                    :strength_defence_home,
                    :strength_defence_away
                    )";

                $stmt = $this->db->prepare($sql);
                $stmt->bindValue('id', $currentTeam->getId());
                $stmt->bindValue('name', $currentTeam->getName());
                $stmt->bindValue('short_name', $currentTeam->getShortName());
                $stmt->bindValue('strength_overall_home', $currentTeam->getStrengthOverallHome());
                $stmt->bindValue('strength_overall_away', $currentTeam->getStrengthOverallAway());
                $stmt->bindValue('strength_attack_home', $currentTeam->getStrengthAttackHome());
                $stmt->bindValue('strength_attack_away', $currentTeam->getStrengthAttackAway());
                $stmt->bindValue('strength_defence_home', $currentTeam->getStrengthDefenceHome());
                $stmt->bindValue('strength_defence_away', $currentTeam->getStrengthDefenceAway());
                $stmt->execute();
            }
        }

        return $teams;
    }

    /**
     * @param $teamId
     * @return bool
     */
    private function checkDbForTeam($teamId)
    {
        $query = $this->db->createQueryBuilder()
            ->select('count(t.id) as numberOfTeams')
            ->from('teams', 't')
            ->where('t.id = :id')
            ->setParameter('id', $teamId);
        $result = $query->execute()->fetch();
        $data = $result['numberOfTeams'];

        return (bool)$data;
    }

    /**
     * History for given player (append player id)
     * @var string
     */
    private static $fplPlayerHistoryFeedUrl = 'https://fantasy.premierleague.com/drf/element-summary/';

    /**
     * Import fixtures also
     * @param array $players
     */
    public function importHistoryByPlayers(array $players)
    {
        foreach ($players as $player) {
            $playerHistoryFeedUrl = static::$fplPlayerHistoryFeedUrl . $player->getId();

            $ch =  curl_init($playerHistoryFeedUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($ch, CURLOPT_TIMEOUT, 3);
            $feedContent = curl_exec($ch);

            $content = json_decode($feedContent);
            $historicalData = $content->{'history'};
            $this->importPlayerHistory($historicalData, $player);

            $fixturesData = $content->{'fixtures'};
            $this->importPlayerFixtures($fixturesData, $player);
        }
    }

    /**
     * @param $fixturesData
     * @param Player $player
     * @return bool
     */
    private function importPlayerFixtures($fixturesData, $player)
    {
        $singlePlayerFixtures = [];
        foreach ($fixturesData as $matchData) {
            $id = $matchData->{'id'};
            $singlePlayerFixtures[$id] = new PlayerMatchFixture([
                'id' => $id,
                'eventId' => $matchData->{'event'},
                'playerId' => $player->getId(),
                'kickoff' => $matchData->{'kickoff_time'},
                'isHome' => (int)$matchData->{'is_home'},
                'teamA' => $matchData->{'team_a'},
                'teamH' => $matchData->{'team_h'},
            ]);
        }

        $sql = "INSERT IGNORE INTO fixtures (
                        id,
                        event_id,
                        player_id,
                        kickoff,
                        is_home,
                        team_a,
                        team_h
                    )";

        $sqlPartStart = "VALUES ";
        $sqlPartMiddle = "";

        /** @var PlayerMatchFixture $matchData */
        foreach ($singlePlayerFixtures as $matchData) {
            # Insert data into table (match player data)
            # Construct string with values
            $sqlPartMiddle .= "('";

            $middle = [
                $matchData->getId(),
                $matchData->getEventId(),
                $player->getId(),
                date('Y-m-d H:i:s', strtotime($matchData->getKickoff())),
                $matchData->isHome(),
                $matchData->getTeamA(),
                $matchData->getTeamH(),
            ];

            $sqlPartMiddle .= join("','", $middle);
            $sqlPartMiddle .= "'),";
        }

        if ($sqlPartMiddle) {
            $sql = $sql . $sqlPartStart . trim($sqlPartMiddle, ",");
            $stmt = $this->db->prepare($sql);
            return $stmt->execute();
        }
    }

    /**
     * @param $historicalData
     * @param Player $player
     * @return bool
     */
    private function importPlayerHistory($historicalData, $player)
    {
        foreach ($historicalData as $matchData) {
            $matchId = $matchData->{'id'};
            $teamId = $player->getTeamId();
            $singlePlayerHistory[$matchId] = new PlayerMatchHistory([
                'id' => $matchId,
                'playerId' => $matchData->{'element'},
                'matchId' => $matchId,
                'teamId' => $teamId,
                'kickoffTime' => $matchData->{'kickoff_time'},
                'teamHScore' => $matchData->{'team_h_score'},
                'teamAScore' => $matchData->{'team_a_score'},
                'wasHome' => $matchData->{'was_home'},
                'round' => $matchData->{'round'},
                'totalPoints' => $matchData->{'total_points'},
                'value' => $matchData->{'value'},
                'transfersBalance' => $matchData->{'transfers_balance'},
                'selected' => $matchData->{'selected'},
                'transfersIn' => $matchData->{'transfers_in'},
                'transfersOut' => $matchData->{'transfers_out'},
                'loanedIn' => $matchData->{'loaned_in'},
                'loanedOut' => $matchData->{'loaned_out'},
                'minutes' => $matchData->{'minutes'},
                'goalsScored' => $matchData->{'goals_scored'},
                'assists' => $matchData->{'assists'},
                'cleanSheets' => $matchData->{'clean_sheets'},
                'goalsConceded' => $matchData->{'goals_conceded'},
                'ownGoals' => $matchData->{'own_goals'},
                'penaltiesSaved' => $matchData->{'penalties_saved'},
                'penaltiesMissed' => $matchData->{'penalties_missed'},
                'yellowCards' => $matchData->{'yellow_cards'},
                'redCards' => $matchData->{'red_cards'},
                'saves' => $matchData->{'saves'},
                'bonus' => $matchData->{'bonus'},
                'bps' => $matchData->{'bps'},
                'influence' => $matchData->{'influence'},
                'creativity' => $matchData->{'creativity'},
                'threat' => $matchData->{'threat'},
                'ictIndex' => $matchData->{'ict_index'},
                'eaIndex' => $matchData->{'ea_index'},
                'openPlayCrosses' => $matchData->{'open_play_crosses'},
                'bigChancesCreated' => $matchData->{'big_chances_created'},
                'clearancesBlocksInterceptions' => $matchData->{'clearances_blocks_interceptions'},
                'recoveries' => $matchData->{'recoveries'},
                'keyPasses' => $matchData->{'key_passes'},
                'tackles' => $matchData->{'tackles'},
                'winningGoals' => $matchData->{'winning_goals'},
                'attemptedPasses' => $matchData->{'attempted_passes'},
                'completedPasses' => $matchData->{'completed_passes'},
                'penaltiesConceded' => $matchData->{'penalties_conceded'},
                'bigChancesMissed' => $matchData->{'big_chances_missed'},
                'errorsLeadingToGoal' => $matchData->{'errors_leading_to_goal'},
                'errorsLeadingToGoalAttempt' => $matchData->{'errors_leading_to_goal_attempt'},
                'tackled' => $matchData->{'tackled'},
                'offside' => $matchData->{'offside'},
                'targetMissed' => $matchData->{'target_missed'},
                'fouls' => $matchData->{'fouls'},
                'dribbles' => $matchData->{'dribbles'},
                'element' => $matchData->{'element'},
                'fixture' => $matchData->{'fixture'},
                'opponentTeam' => $matchData->{'opponent_team'},
            ]);
        }

        $playerId = 0;
        $sql = "INSERT INTO history (
                        id,
                        player_id,
                        match_id,
                        team_id,
                        kickoff_time,
                        team_h_score,
                        team_a_score,
                        was_home,
                        round,
                        total_points,
                        value,
                        transfers_balance,
                        selected,
                        transfers_in,
                        transfers_out,
                        loaned_in,
                        loaned_out,
                        minutes,
                        goals_scored,
                        assists,
                        clean_sheets,
                        goals_conceded,
                        own_goals,
                        penalties_saved,
                        penalties_missed,
                        yellow_cards,
                        red_cards,
                        saves,
                        bonus,
                        bps,
                        influence,
                        creativity,
                        threat,
                        ict_index,
                        ea_index,
                        open_play_crosses,
                        big_chances_created,
                        clearances_blocks_interceptions,
                        recoveries,
                        key_passes,
                        tackles,
                        winning_goals,
                        attempted_passes,
                        completed_passes,
                        penalties_conceded,
                        big_chances_missed,
                        errors_leading_to_goal,
                        errors_leading_to_goal_attempt,
                        tackled,
                        offside,
                        target_missed,
                        fouls,
                        dribbles,
                        element,
                        fixture,
                        opponent_team
                    )";

        $sqlPartStart = "VALUES ";
        $sqlPartMiddle = "";

        /**
         * We are getting array of past matches, we have to check if the match for this
         * player is already in the history table.
         */
        foreach ($singlePlayerHistory as $matchData) {
            $playerId = $matchData->getElement();

            # Insert data into table (match player data)
            # Construct string with values
            $sqlPartMiddle .= "('";

            $middle = [
                $matchData->getId(),
                $matchData->getElement(),
                $matchData->getMatchId(),
                $matchData->getTeamId(),
                date('Y-m-d H:i:s', strtotime($matchData->getKickoffTime())),
                $matchData->getTeamHScore(),
                $matchData->getTeamAScore(),
                $matchData->getWasHome(),
                $matchData->getRound(),
                $matchData->getTotalPoints(),
                $matchData->getValue(),
                $matchData->getTransfersBalance(),
                $matchData->getSelected(),
                $matchData->getTransfersIn(),
                $matchData->getTransfersOut(),
                $matchData->getLoanedIn(),
                $matchData->getLoanedOut(),
                $matchData->getMinutes(),
                $matchData->getGoalsScored(),
                $matchData->getAssists(),
                $matchData->getCleanSheets(),
                $matchData->getGoalsConceded(),
                $matchData->getOwnGoals(),
                $matchData->getPenaltiesSaved(),
                $matchData->getPenaltiesMissed(),
                $matchData->getYellowCards(),
                $matchData->getRedCards(),
                $matchData->getSaves(),
                $matchData->getBonus(),
                $matchData->getBps(),
                $matchData->getInfluence(),
                $matchData->getCreativity(),
                $matchData->getThreat(),
                $matchData->getIctIndex(),
                $matchData->getEaIndex(),
                $matchData->getOpenPlayCrosses(),
                $matchData->getBigChancesCreated(),
                $matchData->getClearancesBlocksInterceptions(),
                $matchData->getRecoveries(),
                $matchData->getKeyPasses(),
                $matchData->getTackles(),
                $matchData->getWinningGoals(),
                $matchData->getAttemptedPasses(),
                $matchData->getCompletedPasses(),
                $matchData->getPenaltiesConceded(),
                $matchData->getBigChancesMissed(),
                $matchData->getErrorsLeadingToGoal(),
                $matchData->getErrorsLeadingToGoalAttempt(),
                $matchData->getTackled(),
                $matchData->getOffside(),
                $matchData->getTargetMissed(),
                $matchData->getFouls(),
                $matchData->getDribbles(),
                $matchData->getElement(),
                $matchData->getFixture(),
                $matchData->getOpponentTeam(),
            ];

            $sqlPartMiddle .= join("','", $middle);
            $sqlPartMiddle .= "'),";
        }

        if ($sqlPartMiddle) {
            echo 'Imported player: ' . $playerId . PHP_EOL;
            $sql = $sql . $sqlPartStart . trim($sqlPartMiddle, ",") .
                " ON DUPLICATE KEY UPDATE id = id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute();
        }
    }
}
