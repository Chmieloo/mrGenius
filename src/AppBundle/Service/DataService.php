<?php

namespace AppBundle\Service;

use AppBundle\Model\Element;
use AppBundle\Model\Player;
use AppBundle\Model\Team;

class DataService
{
    /**
     * @var array
     */
    private static $opts = [
        'http' => [
            'method' => "GET",
            'header' =>
                "Content-Type: application/json\r\n" .
                "X-Api-Key: 61AFF35F\r\n"
        ]
    ];

    /** @var string $apiPlayersEndpointUri */
    private $apiPlayersEndpointUri = 'https://fpl.tlj.no/api/players';

    /**
     * @return array
     */
    public function loadData()
    {
        $context = stream_context_create(static::$opts);
        $data = file_get_contents($this->apiPlayersEndpointUri, false, $context);
        $players = json_decode($data);

        $playersObjects = [];
        foreach ($players as $player) {
            $teamInfo = $player->{'team_info'};
            $team = new Team([
                'id' => $teamInfo->{'id'},
                'name' => $teamInfo->{'name'},
                'shortName' => $teamInfo->{'short_name'},
                'strength' => $teamInfo->{'strength'},
                'strengthAttackAway' => $teamInfo->{'strength_attack_away'},
                'strengthAttackHome' => $teamInfo->{'strength_attack_home'},
                'strengthDefenceAway' => $teamInfo->{'strength_defence_away'},
                'strengthDefenceHome' => $teamInfo->{'strength_defence_home'},
                'strengthOverallAway' => $teamInfo->{'strength_overall_away'},
                'strengthOverallHome' => $teamInfo->{'strength_overall_home'},
            ]);

            $playerPerformances = $player->{'player_performances'};
            # TODO parse performances
            $performances = [];

            $playersObjects[] = new Player([
                'id' => $player->{'id'},
                'teamId' => $player->{'team_id'},
                'elementType' => $player->{'element_type'},
                'firstName' => $player->{'first_name'},
                'secondName' => $player->{'second_name'},
                'photo' => $player->{'photo'},
                'form' => $player->{'form'},
                'nowCost' => $player->{'now_cost'},
                'totalPoints' => $player->{'total_points'},
                'pointsPerGame' => $player->{'points_per_game'},
                'ictIndex' => $player->{'ict_index'},
                'influence' => $player->{'influence'},
                'creativity' => $player->{'creativity'},
                'threat' => $player->{'threat'},
                'chanceOfPayingNextRound' => $player->{'chance_of_playing_next_round'},
                'team' => $team,
                'performances' => $performances,
            ]);
        }

        return $playersObjects;
    }

    /**
     * @param Player[] $playersArray
     * @return array
     */
    public function getPlayerByPosition($playersArray)
    {
        $types = [];
        foreach ($playersArray as $player) {
            $playerType = $player->getElementType();
            if ($player->getTotalPoints() > 0 && $player->getForm() > 0) {
                $types[$playerType][] = $player;
            }
        }

        usort($types[1], function ($a, $b) {
            return $b->getPiIndex() - $a->getPiIndex();
        });

        usort($types[2], function ($a, $b) {
            return $b->getPiIndex() - $a->getPiIndex();
        });

        usort($types[3], function ($a, $b) {
            return $b->getPiIndex() - $a->getPiIndex();
        });

        usort($types[4], function ($a, $b) {
            return $b->getPiIndex() - $a->getPiIndex();
        });

        return $types;
    }

    /**
     * Get averages for positions
     * @param $playersArray
     * @return mixed
     */
    public function getAveragesForPositions($playersArray)
    {
        $playerData = [];
        foreach ($playersArray as $player) {
            if ($player->getTotalPoints() > 0 && $player->getForm() > 0) {
                $playerType = $player->getElementType();
                $playerData[$playerType]['sumPoints'] += $player->getTotalPoints();
                $playerData[$playerType]['numPlayers'] += 1;
            }
        }

        // Calculate averages
        foreach ($playerData as $position => $datum) {
            $playerDataAveragePoints[$position]['averagePoints'] =
                $playerData[$position]['sumPoints'] / $playerData[$position]['numPlayers'];
        }

        // Sort by average points
        uasort($playerDataAveragePoints, function ($a, $b) {
            return $b['averagePoints'] - $a['averagePoints'];
        });

        return $playerDataAveragePoints;
    }

    /**
     * @param Player[] $playersArray
     * @return array
     */
    public function getFormation($playersArray)
    {
        $recommendedFormation = [];

        $maxPlayersPerPosition = [
            1 => 1,
            2 => 5,
            3 => 5,
            4 => 3,
        ];

        $averagesForPositions = $this->getAveragesForPositions($playersArray);

        $recommendedFormation = [];
        $playersOnFieldPool = 11;
        foreach ($averagesForPositions as $positionId => $element) {
            $recommendedFormation[$positionId] =
                $playersOnFieldPool > $maxPlayersPerPosition[$positionId] ?
                    $maxPlayersPerPosition[$positionId] :
                    $playersOnFieldPool;
            $playersOnFieldPool -= $maxPlayersPerPosition[$positionId];
        }

        return $recommendedFormation;
    }

    public function getSquad()
    {
        $firstEleven    = [];
        $teamsCount     = [];

        /** @var Player[] $allPlayers */
        $allPlayers = $this->loadData();

        /** @var array $playerByPosition */
        $playerByPosition = $this->getPlayerByPosition($allPlayers);

        /** @var array $formation */
        $formation = $this->getFormation($allPlayers);

        foreach ($formation as $position => $numberOfPlayers) {
            /** Get number of players from given position */
            switch ($position) {
                case Player::POSITION_GOALKEEPER:
                    $firstEleven =
                        $this->getFirstElevenGoalKeeper($playerByPosition[Player::POSITION_GOALKEEPER], $firstEleven);
                    break;
                case Player::POSITION_DEFENDER:
                    break;
                case Player::POSITION_MIDFIELDER:
                    break;
                case Player::POSITION_FORWARDER:
                    break;
            }
        }

        $playersPool = $playerByPosition;
        $goalkeepers    = $playersPool[1];
        $defenders      = $playersPool[2];
        $midfielders    = $playersPool[3];
        $attackers      = $playersPool[4];

        /** @var Player $goalkeeper1 */
        $goalkeeper1 = array_shift($goalkeepers);
        $teamsCount[$goalkeeper1->getTeam()->getId()] += 1;
        var_dump(json_encode($firstEleven));

        //var_dump($averagesForPositions);
        //var_dump($playerByPosition[1]);
    }

    /**
     * @param $playerByPosition
     * @param $firstEleven
     * @return array
     */
    public function getFirstElevenGoalKeeper($playerByPosition, $firstEleven)
    {
        /*
         * TODO - since the goalkeeper is being picked first, take first element from sorted goalkeepers
         * It has to be changed for next rounds
         */
        /** @var Player $topGoalkeeper */
        $topGoalkeeper = array_shift($playerByPosition);
        $firstEleven[] = $topGoalkeeper->getId();

        return $firstEleven;
    }

    private function getFirstElevenAttackers()
    {

    }

    private function getFirstElevenMidfielders()
    {

    }

    public function getFirstElevenDefenders()
    {

    }

    private function checkTeamPlayers()
    {

    }
}