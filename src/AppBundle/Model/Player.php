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
    private $elementType;
    private $firstName;
    private $secondName;
    private $photo;
    private $form;
    private $nowCost;
    private $totalPoints;
    private $pointsPerGame;
    private $ictIndex;
    private $influence;
    private $creativity;
    private $threat;
    private $chanceOfPlayingNextRound;
    /** @var Team $team */
    private $team;
    /** @var  Performance[] $performances */
    private $performances;

    /** @var  float */
    private $piIndex;

    public function __construct($data)
    {
        $this->setId($data['id']);
        $this->setTeamId($data['teamId']);
        $this->setElementType($data['elementType']);
        $this->setFirstName($data['firstName']);
        $this->setSecondName($data['secondName']);
        $this->setPhoto($data['photo']);
        $this->setForm($data['form']);
        $this->setNowCost($data['nowCost']);
        $this->setTotalPoints($data['totalPoints']);
        $this->setPointsPerGame($data['pointsPerGame']);
        $this->setIctIndex($data['ictIndex']);
        $this->setInfluence($data['influence']);
        $this->setCreativity($data['creativity']);
        $this->setThreat($data['threat']);
        $this->setChanceOfPlayingNextRound($data['chanceOfPayingNextRound']);
        $this->setTeam($data['team']);
        $this->setPerformances($data['performances']);

        $this->calculatePiIndex();
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
    public function getElementType()
    {
        return $this->elementType;
    }

    /**
     * @param mixed $elementType
     */
    public function setElementType($elementType)
    {
        $this->elementType = $elementType;
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
    public function getIctIndex()
    {
        return $this->ictIndex;
    }

    /**
     * @param mixed $ictIndex
     */
    public function setIctIndex($ictIndex)
    {
        $this->ictIndex = $ictIndex;
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

    /**
     * @return Team
     */
    public function getTeam(): Team
    {
        return $this->team;
    }

    /**
     * @param Team $team
     */
    public function setTeam(Team $team)
    {
        $this->team = $team;
    }

    /**
     * @return Performance[]
     */
    public function getPerformances(): array
    {
        return $this->performances;
    }

    /**
     * @param Performance[] $performances
     */
    public function setPerformances(array $performances)
    {
        $this->performances = $performances;
    }

    public function calculatePiIndex()
    {
        $this->piIndex = $this->getForm() * $this->getTotalPoints() * $this->getPointsPerGame();
    }

    /**
     * @return float
     */
    public function getPiIndex()
    {
        return $this->piIndex;
    }
}
