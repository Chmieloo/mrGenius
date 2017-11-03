<?php

namespace AppBundle\Service;

use AppBundle\Model\Element;
use AppBundle\Model\Player;
use AppBundle\Model\Team;

class DataService
{
    private $allPlayers = null;

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

            $playerId = $player->{'id'};

            $playersObjects[$playerId] = new Player([
                'id' => $playerId,
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

        $this->allPlayers = $playersObjects;

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
                    var_dump('picking goalkeepers');
                    $firstEleven =
                        $this->getFirstElevenGoalKeeper(
                            $playerByPosition[Player::POSITION_GOALKEEPER],
                            $firstEleven
                        );
                    break;
                case Player::POSITION_DEFENDER:
                    var_dump('picking defenders');
                    $firstEleven =
                        $this->getFirstElevenDefenders(
                            $playerByPosition[Player::POSITION_DEFENDER],
                            $firstEleven,
                            $numberOfPlayers
                        );
                    break;
                case 666:
                    $firstEleven =
                        $this->getFirstElevenMidfielders(
                            $playerByPosition[Player::POSITION_MIDFIELDER],
                            $firstEleven,
                            $numberOfPlayers
                        );
                    break;
                case Player::POSITION_FORWARDER:
                    var_dump('picking forwards');
                    $firstEleven =
                        $this->getFirstElevenAttackers(
                            $playerByPosition[Player::POSITION_FORWARDER],
                            $firstEleven,
                            $numberOfPlayers
                        );
                    break;
            }
        }

        //var_dump(json_encode($firstEleven));

        foreach ($firstEleven as $playerId) {
            /** @var Player $player */
            $player = $this->allPlayers[$playerId];
            //var_dump($player->getName());
        }

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

    /**
     * @param $playerByPosition
     * @param $firstEleven
     * @param $numberOfPlayers
     * @return array
     */
    private function getFirstElevenAttackers($playerByPosition, $firstEleven, $numberOfPlayers)
    {
        $pickedAttackers = 0;
        $playerIndex = 0;
        while ($pickedAttackers < $numberOfPlayers) {
            # Get player with given index
            /** @var Player $potentialPlayer */
            $potentialPlayer = $playerByPosition[$playerIndex];

            # Get player team
            $potentialPlayerTeamId = $potentialPlayer->getTeam()->getId();

            # Check if it is possible to get this player (no more than 3 per team)
            $canBeAdded = $this->checkPlayerTeam($potentialPlayerTeamId, $firstEleven);

            if ($canBeAdded) {
                $firstEleven[] = $potentialPlayer->getId();
                $pickedAttackers++;
            }

            $playerIndex++;
        }

        return $firstEleven;
    }

    private function getFirstElevenMidfielders($playerByPosition, $firstEleven, $numberOfPlayers)
    {
        return $firstEleven;
    }

    public function getFirstElevenDefenders($playerByPosition, $firstEleven, $numberOfPlayers)
    {
        //var_dump($playerByPosition);
        $pickedDefenders = 0;
        $playerIndex = 0;
        while ($pickedDefenders < $numberOfPlayers) {
            //var_dump($pickedDefenders);
            # Get player with given index
            /** @var Player $potentialPlayer */
            $potentialPlayer = $playerByPosition[$playerIndex];

            # Get player team
            $potentialPlayerTeamId = $potentialPlayer->getTeam()->getId();
            //var_dump($potentialPlayerTeamId);

            # Check if it is possible to get this player (no more than 3 per team)
            $canBeAdded = $this->checkPlayerTeam($potentialPlayerTeamId, $firstEleven);
var_dump($canBeAdded);
            if ($canBeAdded) {
                $firstEleven[] = $potentialPlayer->getId();
                $pickedDefenders++;
            }

            $playerIndex++;
        }


        return $firstEleven;
    }

    /**
     * Get player cost
     * @param $playerId
     * @return mixed
     */
    public function getPlayerCostById($playerId)
    {
        /** @var Player $player */
        $player = $this->allPlayers[$playerId];

        return $player->getNowCost();
    }

    /**
     * Get player team id
     * @param $playerId
     * @return mixed
     */
    private function getPlayerTeamId($playerId)
    {
        /** @var Player $player */
        $player = $this->allPlayers[$playerId];

        return $player->getTeam()->getId();
    }

    /**
     * Checks if player can be added to the team
     * @param $teamId
     * @param array $firstEleven
     * @return bool
     */
    private function checkPlayerTeam($teamId, $firstEleven = [])
    {
        $grouped = [];
        foreach ($firstEleven as $playerId) {
            $teamId = $this->getPlayerTeamId($playerId);
            $grouped[$teamId]++;
        }

        var_dump($grouped);

        if (array_key_exists($teamId, $grouped) && $grouped[$teamId] >= 3) {
            return false;
        }

        return true;
    }

    /**
     * @param $playersArray
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
                     'teamId' => $player->getTeam()->getId(),
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
}