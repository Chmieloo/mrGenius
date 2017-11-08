<?php

namespace AppBundle\Service;

use AppBundle\Model\Event;
use AppBundle\Model\Player;
use AppBundle\Model\PlayerMatchHistory;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

class ImportService
{
    /*
    MULTI CURL REQUESTS
    // build the individual requests, but do not execute them
$ch_1 = curl_init('http://webservice.one.com/');
$ch_2 = curl_init('http://webservice.two.com/');
curl_setopt($ch_1, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch_2, CURLOPT_RETURNTRANSFER, true);

// build the multi-curl handle, adding both $ch
$mh = curl_multi_init();
curl_multi_add_handle($mh, $ch_1);
curl_multi_add_handle($mh, $ch_2);

// execute all queries simultaneously, and continue when all are complete
  $running = null;
  do {
    curl_multi_exec($mh, $running);
  } while ($running);

//close the handles
curl_multi_remove_handle($mh, $ch1);
curl_multi_remove_handle($mh, $ch2);
curl_multi_close($mh);

// all of our requests are done, we can now access the results
$response_1 = curl_multi_getcontent($ch_1);
$response_2 = curl_multi_getcontent($ch_2);
echo "$response_1 $response_2"; // output results
     */

    private $db;

    private $playersMatches;

    private $playerMatchCount;

    /**
     * OptionsService constructor.
     * @param Connection $db
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
        $this->getPlayersMatches();
    }

    /**
     * History for given player (append player id)
     * @var string
     */
    private static $fplPlayerHistoryFeedUrl = 'https://fantasy.premierleague.com/drf/element-summary/';

    /**
     * @param array $players
     */
    public function importHistoryByPlayers(array $players)
    {
        # TODO TESTING $players = [$players[260]];
        foreach ($players as $player) {
            $playerHistoryFeedUrl = static::$fplPlayerHistoryFeedUrl . $player->getId();

            $singlePlayerHistory = [];
            $ch =  curl_init($playerHistoryFeedUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($ch, CURLOPT_TIMEOUT, 3);
            $feedContent = curl_exec($ch);

            $historyContent = json_decode($feedContent);
            $data = $historyContent->{'history'};

            # If there are more history matches online than imported, execute the import
            if (count($data) != $this->playerMatchCount) {
                foreach ($data as $matchData) {
                    $matchId = $matchData->{'id'};
                    $teamId = $player->getTeamId();
                    $singlePlayerHistory[$matchId] = new PlayerMatchHistory([
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

                $this->importPlayerHistory($singlePlayerHistory);
            }
        }
    }

    /**
     * @param PlayerMatchHistory[] $singlePlayerHistory
     * @return bool
     */
    private function importPlayerHistory($singlePlayerHistory)
    {
        $sql = "INSERT INTO history (
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
            $matchId = $matchData->getMatchId();
            $checkKey = $playerId . 'p-m' . $matchId;

            if (!array_key_exists($checkKey, $this->playersMatches)) {
                # Insert data into table (match player data)
                # Construct string with values
                $sqlPartMiddle .= "('";

                $middle = [
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
        }

        if ($sqlPartMiddle) {
            $sql = $sql . $sqlPartStart . trim($sqlPartMiddle, ",");
            //$stmt = $this->db->prepare($sql);
            //return $stmt->execute();
        }
    }

    /**
     * Get helper array with player matches and their count separately
     */
    private function getPlayersMatches()
    {
        $array = [];
        $playerMatchCount = [];
        $query = $this->db->createQueryBuilder()
            ->select(
                'player_id as playerId',
                'match_id as matchId'
            )
            ->from('history');
        $result = $query->execute()->fetchAll(\PDO::FETCH_ASSOC);

        if ($result) {
            foreach ($result as $data) {
                $playerId = $data['playerId'];
                $matchId = $data['matchId'];
                $key = $playerId . 'p-m' . $matchId;
                $array[$key] = 1;
                $playerMatchCount[$playerId]++;
            }
        }

        $this->playersMatches = $array;
        $this->playerMatchCount = $playerMatchCount;
    }
}
