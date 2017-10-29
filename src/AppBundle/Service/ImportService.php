<?php

namespace AppBundle\Service;

use AppBundle\Model\Event;
use AppBundle\Model\Player;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

class ImportService
{
    private $currentUri = 'https://fantasy.premierleague.com/drf/bootstrap-static';

    private $onlineData = null;

    /** @var Connection $db */
    private $db;

    /** @var  OptionsService $optionsService */
    private $optionsService;

    public function __construct(
        Connection $db,
        OptionsService $optionsService
    )
    {
        $this->db = $db;
        $this->optionsService = $optionsService;

        $this->loadOnlineData();
    }

    /**
     * Load online json file
     */
    private function loadOnlineData()
    {
        /**
         * Get last finished from URI
         */
        $uriData = file_get_contents($this->currentUri);
        $this->onlineData = json_decode($uriData);
    }

    /**
     * @return int
     */
    public function getLastOnlineFinishedEventId()
    {
        $events = $this->onlineData->{'events'};
        $lastFinishedEventId = $this->getLastFinishedEvent($events);

        return $lastFinishedEventId;
    }

    /**
     * @param $events
     * @return int
     */
    private function getLastFinishedEvent($events)
    {
        $lastFinished = 1;
        foreach ($events as $event) {
            if ($event->{'finished'} === false) {
                break;
            }
            $eventId = $event->{'id'};
            $lastFinished = $eventId;
        }

        return $lastFinished;
    }

    public function importOnlineData()
    {
        $lastOnlineFinishedId = $this->getLastOnlineFinishedEventId();

        # Import events
        $importEvents = $this->importEvents();

        # Import fixtures

        # Import players
        $importPlayers = $this->importPlayers($lastOnlineFinishedId);

        # import teams

        # Import dictionaries

        return $lastOnlineFinishedId;
    }

    /**
     * @return bool
     */
    private function importEvents()
    {
        $events = $this->onlineData->{'events'};
        /** @var Event[] $eventsModels */
        $eventsModels = $this->generateEvents($events);

        foreach ($eventsModels as $eventsModel) {
            # Remove old data for this model
            $this->deleteEventById($eventsModel->getId());

            # Remove old data for this model
            if (!$this->saveEvent($eventsModel)) {
                return false;
                break;
            }
        }

        return true;
    }

    /**
     * @param $lastFinishedId
     * @return bool
     */
    private function importPlayers($lastFinishedId)
    {
        $players = $this->onlineData->{'elements'};
        /** @var Player[] $eventsModels */
        $playersModels = $this->generatePlayers($players, $lastFinishedId);

        foreach ($playersModels as $playersModel) {
            # Remove old data for this model
            $this->deletePlayerByIdAndEventId($playersModel->getId(), $lastFinishedId);

            # Remove old data for this model
            if (!$this->savePlayer($playersModel)) {
                return false;
                break;
            }
        }

        return true;
    }

    /**
     * @param $id
     */
    private function deleteEventById($id)
    {
        $sql = "DELETE FROM events WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
    }

    /**
     * @param $id
     * @param $eventId
     */
    private function deletePlayerByIdAndEventId($id, $eventId)
    {
        $sql = "DELETE FROM players WHERE id = :id AND event_id = :eventId";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->bindValue(':eventId', $eventId);
        $stmt->execute();
    }

    /**
     * @param $eventsModel
     * @return bool
     */
    function saveEvent($eventsModel)
    {
        $sql = "INSERT INTO events (
                    id, 
                    name, 
                    average_entry_score, 
                    finished,
                    highest_score,
                    is_previous,
                    is_current,
                    is_next
                    ) VALUES (
                    :id, 
                    :name,  
                    :average_entry_score, 
                    :finished,
                    :highest_score,
                    :is_previous,
                    :is_current,
                    :is_next
                    )";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $eventsModel->getId());
        $stmt->bindValue(':name', $eventsModel->getId());
        $stmt->bindValue(':average_entry_score', $eventsModel->getAverageEntryScore());
        $stmt->bindValue(':finished', $eventsModel->getFinished());
        $stmt->bindValue(':highest_score', $eventsModel->getHighestScore());
        $stmt->bindValue(':is_previous', $eventsModel->getisPrevious());
        $stmt->bindValue(':is_current', $eventsModel->getisCurrent());
        $stmt->bindValue(':is_next', $eventsModel->getisNext());

        return $stmt->execute();
    }


    /**
     * @param Player $playersModel
     * @return bool
     */
    function savePlayer($playersModel)
    {
        $sql = "INSERT INTO players (
                    event_id,
                    id,
                    photo,
                    team_code,
                    status,
                    code,
                    first_name,
                    second_name,
                    squad_number,
                    news,
                    now_cost,
                    chance_of_playing_this_round,
                    chance_of_playing_next_round,
                    form,
                    total_points,
                    event_points,
                    points_per_game,
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
                    element_type,
                    team
                    ) VALUES (
                    :event_id,
                    :id,
                    :photo,
                    :team_code,
                    :status,
                    :code,
                    :first_name,
                    :second_name,
                    :squad_number,
                    :news,
                    :now_cost,
                    :chance_of_playing_this_round,
                    :chance_of_playing_next_round,
                    :form,
                    :total_points,
                    :event_points,
                    :points_per_game,
                    :minutes,
                    :goals_scored,
                    :assists,
                    :clean_sheets,
                    :goals_conceded,
                    :own_goals,
                    :penalties_saved,
                    :penalties_missed,
                    :yellow_cards,
                    :red_cards,
                    :saves,
                    :bonus,
                    :bps,
                    :influence,
                    :creativity,
                    :threat,
                    :ict_index,
                    :element_type,
                    :team
                    )";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':event_id', $playersModel->getEventId());
        $stmt->bindValue(':id', $playersModel->getId());
        $stmt->bindValue(':photo', $playersModel->getPhoto());
        $stmt->bindValue(':team_code', $playersModel->getTeamCode());
        $stmt->bindValue(':status', $playersModel->getStatus());
        $stmt->bindValue(':code', $playersModel->getCode());
        $stmt->bindValue(':first_name', $playersModel->getFirstName());
        $stmt->bindValue(':second_name', $playersModel->getSecondName());
        $stmt->bindValue(':squad_number', $playersModel->getSquadNumber());
        $stmt->bindValue(':news', $playersModel->getNews());
        $stmt->bindValue(':now_cost', $playersModel->getNowCost());
        $stmt->bindValue(':chance_of_playing_this_round', $playersModel->getChanceOfPlayingThisRound());
        $stmt->bindValue(':chance_of_playing_next_round', $playersModel->getChanceOfPlayingNextRound());
        $stmt->bindValue(':form', $playersModel->getForm());
        $stmt->bindValue(':total_points', $playersModel->getTotalPoints());
        $stmt->bindValue(':event_points', $playersModel->getEventPoints());
        $stmt->bindValue(':points_per_game', $playersModel->getPointsPerGame());
        $stmt->bindValue(':minutes', $playersModel->getMinutes());
        $stmt->bindValue(':goals_scored', $playersModel->getGoalsScored());
        $stmt->bindValue(':assists', $playersModel->getAssists());
        $stmt->bindValue(':clean_sheets', $playersModel->getCleanSheets());
        $stmt->bindValue(':goals_conceded', $playersModel->getGoalsConceded());
        $stmt->bindValue(':own_goals', $playersModel->getOwnGoals());
        $stmt->bindValue(':penalties_saved', $playersModel->getPenaltiesSaved());
        $stmt->bindValue(':penalties_missed', $playersModel->getPenaltiesMissed());
        $stmt->bindValue(':yellow_cards', $playersModel->getYellowCards());
        $stmt->bindValue(':red_cards', $playersModel->getRedCards());
        $stmt->bindValue(':saves', $playersModel->getSaves());
        $stmt->bindValue(':bonus', $playersModel->getBonus());
        $stmt->bindValue(':bps', $playersModel->getBps());
        $stmt->bindValue(':influence', $playersModel->getInfluence());
        $stmt->bindValue(':creativity', $playersModel->getCreativity());
        $stmt->bindValue(':threat', $playersModel->getThreat());
        $stmt->bindValue(':ict_index', $playersModel->getIctIndex());
        $stmt->bindValue(':element_type', $playersModel->getElementType());
        $stmt->bindValue(':team', $playersModel->getTeam());

        return $stmt->execute();
    }

    /**
     * @param $events
     * @return array
     */
    private function generateEvents($events)
    {
        $eventsArray = [];
        foreach ($events as $event) {
            $eventsArray[] = $this->generateEvent($event);
        }

        return $eventsArray;
    }

    public function generatePlayers($players, $lastFinishedId)
    {
        $playersArray = [];
        foreach ($players as $player) {
            $playersArray[] = $this->generatePlayer($player, $lastFinishedId);
        }

        return $playersArray;
    }

    /**
     * @param $data
     * @return Event
     */
    private function generateEvent($data)
    {
        $event = new Event([
            'id' => $data->{'id'},
            'name' => $data->{'name'},
            'deadlineTime' => $data->{'deadline_time'},
            'averageEntryScore' => $data->{'average_entry_score'},
            'isFinished' => $data->{'finished'},
            'highestScore' => $data->{'highest_score'},
            'isPrevious' => $data->{'is_previous'},
            'isCurrent' => $data->{'is_current'},
            'isNext' => $data->{'is_next'},
        ]);

        return $event;
    }

    public function generatePlayer($data, $lastFinishedId)
    {
        $player = new Player([
            'eventId' => $lastFinishedId,
            'id' => $data->{'id'},
            'photo' => $data->{'photo'},
            'teamCode' => $data->{'team_code'},
            'status' => $data->{'status'},
            'code' => $data->{'code'},
            'firstName' => $data->{'first_name'},
            'secondName' => $data->{'second_name'},
            'squadNumber' => $data->{'squad_number'},
            'news' => $data->{'news'},
            'nowCost' => $data->{'now_cost'},
            'chanceOfPlayingThisRound' => $data->{'chance_of_playing_this_round'},
            'chanceOfPayingNextRound' => $data->{'chance_of_playing_next_round'},
            'form' => $data->{'form'},
            'totalPoints' => $data->{'total_points'},
            'eventPoints' => $data->{'event_points'},
            'pointsPerGame' => $data->{'points_per_game'},
            'minutes' => $data->{'minutes'},
            'goalsScored' => $data->{'goals_scored'},
            'assists' => $data->{'assists'},
            'cleanSheets' => $data->{'clean_sheets'},
            'goalsConceded' => $data->{'goals_conceded'},
            'ownGoals' => $data->{'own_goals'},
            'penaltiesSaved' => $data->{'penalties_saved'},
            'penaltiesMissed' => $data->{'penalties_missed'},
            'yellowCards' => $data->{'yellow_cards'},
            'redCards' => $data->{'red_cards'},
            'saves' => $data->{'saves'},
            'bonus' => $data->{'bonus'},
            'bps' => $data->{'bps'},
            'influence' => $data->{'influence'},
            'creativity' => $data->{'creativity'},
            'threat' => $data->{'threat'},
            'ictIndex' => $data->{'ict_index'},
            'elementType' => $data->{'element_type'},
            'teams' => $data->{'team'},
        ]);

        return $player;
    }
}
