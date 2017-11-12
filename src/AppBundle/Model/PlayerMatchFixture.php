<?php

namespace AppBundle\Model;

class PlayerMatchFixture
{
    private $id;
    private $eventId;
    private $playerId;
    private $kickoff;
    private $isHome;
    private $teamA;
    private $teamH;

    /**
     * PlayerMatchFixture constructor.
     * @param $data
     */
    public function __construct($data)
    {
        $this->setId($data['id']);
        $this->setEventId($data['eventId']);
        $this->setPlayerId($data['playerId']);
        $this->setKickoff($data['kickoff']);
        $this->setIsHome($data['isHome']);
        $this->setTeamA($data['teamA']);
        $this->setTeamH($data['teamH']);
    }

    /**
     * @return mixed
     */
    public function getEventId()
    {
        return $this->eventId;
    }

    /**
     * @param mixed $eventId
     */
    public function setEventId($eventId)
    {
        $this->eventId = $eventId;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getPlayerId()
    {
        return $this->playerId;
    }

    /**
     * @param mixed $playerId
     */
    public function setPlayerId($playerId)
    {
        $this->playerId = $playerId;
    }

    /**
     * @return mixed
     */
    public function getKickoff()
    {
        return $this->kickoff;
    }

    /**
     * @param mixed $kickoff
     */
    public function setKickoff($kickoff)
    {
        $this->kickoff = $kickoff;
    }

    /**
     * @return mixed
     */
    public function isHome()
    {
        return $this->isHome;
    }

    /**
     * @param mixed $isHome
     */
    public function setIsHome($isHome)
    {
        $this->isHome = $isHome;
    }

    /**
     * @return mixed
     */
    public function getTeamA()
    {
        return $this->teamA;
    }

    /**
     * @param mixed $teamA
     */
    public function setTeamA($teamA)
    {
        $this->teamA = $teamA;
    }

    /**
     * @return mixed
     */
    public function getTeamH()
    {
        return $this->teamH;
    }

    /**
     * @param mixed $teamH
     */
    public function setTeamH($teamH)
    {
        $this->teamH = $teamH;
    }
}
