<?php

namespace AppBundle\Model;

class Player
{
    const POSITION_GOALKEEPER = 1;
    const POSITION_DEFENDER = 2;
    const POSITION_MIDFIELDER = 3;
    const POSITION_FORWARDER = 4;

    private $id;
    private $teamId;
    private $type;
    private $firstName;
    private $secondName;
    private $photo;
    private $nowCost;
    private $chanceOfPlayingNextRound;
    private $form;
    private $pointsPerGame;
    private $influence;
    private $creativity;
    private $threat;
    private $totalPoints;

    /**
     * Player constructor.
     * @param $data
     */
    public function __construct($data)
    {
        $this->setId($data['id']);
        $this->setTeamId($data['teamId']);
        $this->setType($data['type']);
        $this->setFirstName($data['firstName']);
        $this->setSecondName($data['secondName']);
        $this->setPhoto($data['photo']);
        $this->setForm($data['form']);
        $this->setNowCost($data['nowCost']);
        $this->setTotalPoints($data['totalPoints']);
        $this->setPointsPerGame($data['pointsPerGame']);
        $this->setInfluence($data['influence']);
        $this->setCreativity($data['creativity']);
        $this->setThreat($data['threat']);
        $this->setChanceOfPlayingNextRound($data['chanceOfPlayingNextRound']);
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
    public function getTeamId()
    {
        return $this->teamId;
    }

    /**
     * @param mixed $teamId
     */
    public function setTeamId($teamId)
    {
        $this->teamId = $teamId;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getPositionName()
    {
        $positionName = '';
        switch ($this->getType()) {
            case 1:
                $positionName = 'goalkeeper';
                break;
            case 2:
                $positionName = 'defender';
                break;
            case 3:
                $positionName = 'midfielder';
                break;
            case 4:
                $positionName = 'attacker';
                break;
        }

        return $positionName;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->getSecondName() . ', ' . $this->getFirstName();
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param mixed $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return mixed
     */
    public function getSecondName()
    {
        return $this->secondName;
    }

    /**
     * @param mixed $secondName
     */
    public function setSecondName($secondName)
    {
        $this->secondName = $secondName;
    }

    /**
     * @return mixed
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * @param mixed $photo
     */
    public function setPhoto($photo)
    {
        $this->photo = $photo;
    }

    /**
     * @return mixed
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @param mixed $form
     */
    public function setForm($form)
    {
        $this->form = $form;
    }

    /**
     * @return mixed
     */
    public function getNowCost()
    {
        return $this->nowCost;
    }

    /**
     * @param mixed $nowCost
     */
    public function setNowCost($nowCost)
    {
        $this->nowCost = $nowCost;
    }

    /**
     * @return mixed
     */
    public function getTotalPoints()
    {
        return $this->totalPoints;
    }

    /**
     * @param mixed $totalPoints
     */
    public function setTotalPoints($totalPoints)
    {
        $this->totalPoints = $totalPoints;
    }

    /**
     * @return mixed
     */
    public function getPointsPerGame()
    {
        return $this->pointsPerGame;
    }

    /**
     * @param mixed $pointsPerGame
     */
    public function setPointsPerGame($pointsPerGame)
    {
        $this->pointsPerGame = $pointsPerGame;
    }

    /**
     * @return mixed
     */
    public function getInfluence()
    {
        return $this->influence;
    }

    /**
     * @param mixed $influence
     */
    public function setInfluence($influence)
    {
        $this->influence = $influence;
    }

    /**
     * @return mixed
     */
    public function getCreativity()
    {
        return $this->creativity;
    }

    /**
     * @param mixed $creativity
     */
    public function setCreativity($creativity)
    {
        $this->creativity = $creativity;
    }

    /**
     * @return mixed
     */
    public function getThreat()
    {
        return $this->threat;
    }

    /**
     * @param mixed $threat
     */
    public function setThreat($threat)
    {
        $this->threat = $threat;
    }

    /**
     * @return mixed
     */
    public function getChanceOfPlayingNextRound()
    {
        return $this->chanceOfPlayingNextRound;
    }

    /**
     * @param mixed $chanceOfPlayingNextRound
     */
    public function setChanceOfPlayingNextRound($chanceOfPlayingNextRound)
    {
        $this->chanceOfPlayingNextRound = $chanceOfPlayingNextRound;
    }
}
