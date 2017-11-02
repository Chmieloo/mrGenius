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
    public function getPlayerByType($playersArray)
    {
        $types = [];
        foreach ($playersArray as $player) {
            $playerType = $player->getElementType();
            $types[$playerType][] = $player;
        }

        return $types;
    }

    /**
     * @param Player[] $playersArray
     * @return array
     */
    public function getPlayerByTypeFormatted($playersArray)
    {
        $types = [];
        foreach ($playersArray as $player) {
            if ($player->getTotalPoints() > 0 && $player->getForm() > 0) {
                $playerType = $player->getElementType();
                $playerPi = $player->getForm() * $player->getTotalPoints() * $player->getPointsPerGame();
                $types[$playerType][] = [
                    'id' => $player->getId(),
                    'name' => $player->getFirstName() . ' ' . $player->getSecondName(),
                    'form' => $player->getForm(),
                    'totalPoints' => $player->getTotalPoints(),
                    'ppg' => $player->getPointsPerGame(),
                    'value' => $player->getNowCost() / 10,
                    'teamId'=> $player->getTeam()->getId(),
                    'pi' => $playerPi,
                ];
            }
        }

        usort($types[1], function ($a, $b) {
            return $b['pi'] - $a['pi'];
        });

        usort($types[2], function ($a, $b) {
            return $b['pi'] - $a['pi'];
        });

        usort($types[3], function ($a, $b) {
            return $b['pi'] - $a['pi'];
        });

        usort($types[4], function ($a, $b) {
            return $b['pi'] - $a['pi'];
        });

        return $types;
    }

    /**
     * @param Player[] $playersArray
     * @return string
     */
    public function getFormation($playersArray)
    {
        $maxPlayersPerPosition = [
            1 => 1,
            2 => 5,
            3 => 5,
            4 => 3,
        ];

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

        var_dump($playerDataAveragePoints);

        // Sort by average points
        uasort($playerDataAveragePoints, function ($a, $b) {
            return $b['averagePoints'] - $a['averagePoints'];
        });

        $recommendedFormation = [];
        $playersOnFieldPool = 11;
        foreach ($playerDataAveragePoints as $positionId => $element) {
            $recommendedFormation[$positionId] =
                $playersOnFieldPool > $maxPlayersPerPosition[$positionId] ?
                    $maxPlayersPerPosition[$positionId] :
                    $playersOnFieldPool;
            $playersOnFieldPool -= $maxPlayersPerPosition[$positionId];
        }

        return $recommendedFormation;
    }
}