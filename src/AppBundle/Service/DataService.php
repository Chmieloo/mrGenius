<?php

namespace AppBundle\Service;

use AppBundle\Model\Element;
use AppBundle\Model\Player;
use AppBundle\Model\Team;

class DataService
{
    /**
     * Current state of the game
     * @var string
     */
    private static $fplCurrentStateFeedUrl = 'https://fantasy.premierleague.com/drf/bootstrap-static';

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
}
