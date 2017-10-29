<?php

namespace AppBundle\Service;

use AppBundle\Model\Event;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

class ImportService
{

    /*
     * $sql = "INSERT INTO table (field1, field2) VALUES ('foo', 'var')";
$stmt = $em->getConnection()->prepare($sql);
$stmt->bindValue(':invoice', $invoiceId);
$result = $stmt->execute();
     */

    private $currentUri = 'https://fantasy.premierleague.com/drf/bootstrap-static';

    private $onlineData = null;

    /** @var Connection $db */
    private $db;

    /** @var  OptionsService$optionsService */
    private $optionsService;

    public function __construct(
        Connection $db,
        OptionsService $optionsService
    ) {
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
        # Import events
        $importEvents = $this->importEvents();

        # Import fixtures

        # import teams

        # Import dictionaries

        return $this->getLastOnlineFinishedEventId();
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
}
