<?php

namespace AppBundle\Service;

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
}