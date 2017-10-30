<?php

namespace AppBundle\Model;

class Player
{
    const POSITION_GOALKEEPER = 1;
    const POSITION_DEFENDER = 2;
    const POSITION_MIDFIELDER = 3;
    const POSITION_FORWARDER = 4;

    private $eventId;
    private $id;
    private $photo;
    private $teamCode;
    private $status;
    private $code;
    private $firstName;
    private $secondName;
    private $squadNumber;
    private $news;
    private $nowCost;
    private $chanceOfPlayingThisRound;
    private $chanceOfPlayingNextRound;
    private $form;
    private $totalPoints;
    private $eventPoints;
    private $pointsPerGame;
    private $minutes;
    private $goalsScored;
    private $assists;
    private $cleanSheets;
    private $goalsConceded;
    private $ownGoals;
    private $penaltiesSaved;
    private $penaltiesMissed;
    private $yellowCards;
    private $redCards;
    private $saves;
    private $bonus;
    private $bps;
    private $influence;
    private $creativity;
    private $threat;
    private $ictIndex;
    private $elementType;
    private $team;


    public function __construct($data)
    {
        $this->setEventId($data['eventId']);
        $this->setId($data['id']);
        $this->setPhoto($data['photo']);
        $this->setTeamCode($data['teamCode']);
        $this->setStatus($data['status']);
        $this->setCode($data['code']);
        $this->setFirstName($data['firstName']);
        $this->setSecondName($data['secondName']);
        $this->setSquadNumber($data['squadNumber']);
        $this->setNews($data['news']);
        $this->setNowCost($data['nowCost']);
        $this->setChanceOfPlayingThisRound($data['chanceOfPlayingThisRound']);
        $this->setChanceOfPlayingNextRound($data['chanceOfPayingNextRound']);
        $this->setForm($data['form']);
        $this->setTotalPoints($data['totalPoints']);
        $this->setEventPoints($data['eventPoints']);
        $this->setPointsPerGame($data['pointsPerGame']);
        $this->setMinutes($data['minutes']);
        $this->setGoalsScored($data['goalsScored']);
        $this->setAssists($data['assists']);
        $this->setCleanSheets($data['cleanSheets']);
        $this->setGoalsConceded($data['goalsConceded']);
        $this->setOwnGoals($data['ownGoals']);
        $this->setPenaltiesSaved($data['penaltiesSaved']);
        $this->setPenaltiesMissed($data['penaltiesMissed']);
        $this->setYellowCards($data['yellowCards']);
        $this->setRedCards($data['redCards']);
        $this->setSaves($data['saves']);
        $this->setBonus($data['bonus']);
        $this->setBps($data['bps']);
        $this->setInfluence($data['influence']);
        $this->setCreativity($data['creativity']);
        $this->setThreat($data['threat']);
        $this->setIctIndex($data['ictIndex']);
        $this->setElementType($data['elementType']);
        $this->setTeam($data['teams']);
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
    public function getTeamCode()
    {
        return $this->teamCode;
    }

    /**
     * @param mixed $teamCode
     */
    public function setTeamCode($teamCode)
    {
        $this->teamCode = $teamCode;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param mixed $code
     */
    public function setCode($code)
    {
        $this->code = $code;
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
    public function getSquadNumber()
    {
        return $this->squadNumber;
    }

    /**
     * @param mixed $squadNumber
     */
    public function setSquadNumber($squadNumber)
    {
        $this->squadNumber = $squadNumber;
    }

    /**
     * @return mixed
     */
    public function getNews()
    {
        return $this->news;
    }

    /**
     * @param mixed $news
     */
    public function setNews($news)
    {
        $this->news = $news;
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
    public function getChanceOfPlayingThisRound()
    {
        return $this->chanceOfPlayingThisRound;
    }

    /**
     * @param mixed $chanceOfPlayingThisRound
     */
    public function setChanceOfPlayingThisRound($chanceOfPlayingThisRound)
    {
        $this->chanceOfPlayingThisRound = $chanceOfPlayingThisRound;
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
    public function getEventPoints()
    {
        return $this->eventPoints;
    }

    /**
     * @param mixed $eventPoints
     */
    public function setEventPoints($eventPoints)
    {
        $this->eventPoints = $eventPoints;
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
    public function getMinutes()
    {
        return $this->minutes;
    }

    /**
     * @param mixed $minutes
     */
    public function setMinutes($minutes)
    {
        $this->minutes = $minutes;
    }

    /**
     * @return mixed
     */
    public function getGoalsScored()
    {
        return $this->goalsScored;
    }

    /**
     * @param mixed $goalsScored
     */
    public function setGoalsScored($goalsScored)
    {
        $this->goalsScored = $goalsScored;
    }

    /**
     * @return mixed
     */
    public function getAssists()
    {
        return $this->assists;
    }

    /**
     * @param mixed $assists
     */
    public function setAssists($assists)
    {
        $this->assists = $assists;
    }

    /**
     * @return mixed
     */
    public function getCleanSheets()
    {
        return $this->cleanSheets;
    }

    /**
     * @param mixed $cleanSheets
     */
    public function setCleanSheets($cleanSheets)
    {
        $this->cleanSheets = $cleanSheets;
    }

    /**
     * @return mixed
     */
    public function getGoalsConceded()
    {
        return $this->goalsConceded;
    }

    /**
     * @param mixed $goalsConceded
     */
    public function setGoalsConceded($goalsConceded)
    {
        $this->goalsConceded = $goalsConceded;
    }

    /**
     * @return mixed
     */
    public function getOwnGoals()
    {
        return $this->ownGoals;
    }

    /**
     * @param mixed $ownGoals
     */
    public function setOwnGoals($ownGoals)
    {
        $this->ownGoals = $ownGoals;
    }

    /**
     * @return mixed
     */
    public function getPenaltiesSaved()
    {
        return $this->penaltiesSaved;
    }

    /**
     * @param mixed $penaltiesSaved
     */
    public function setPenaltiesSaved($penaltiesSaved)
    {
        $this->penaltiesSaved = $penaltiesSaved;
    }

    /**
     * @return mixed
     */
    public function getPenaltiesMissed()
    {
        return $this->penaltiesMissed;
    }

    /**
     * @param mixed $penaltiesMissed
     */
    public function setPenaltiesMissed($penaltiesMissed)
    {
        $this->penaltiesMissed = $penaltiesMissed;
    }

    /**
     * @return mixed
     */
    public function getYellowCards()
    {
        return $this->yellowCards;
    }

    /**
     * @param mixed $yellowCards
     */
    public function setYellowCards($yellowCards)
    {
        $this->yellowCards = $yellowCards;
    }

    /**
     * @return mixed
     */
    public function getRedCards()
    {
        return $this->redCards;
    }

    /**
     * @param mixed $redCards
     */
    public function setRedCards($redCards)
    {
        $this->redCards = $redCards;
    }

    /**
     * @return mixed
     */
    public function getSaves()
    {
        return $this->saves;
    }

    /**
     * @param mixed $saves
     */
    public function setSaves($saves)
    {
        $this->saves = $saves;
    }

    /**
     * @return mixed
     */
    public function getBonus()
    {
        return $this->bonus;
    }

    /**
     * @param mixed $bonus
     */
    public function setBonus($bonus)
    {
        $this->bonus = $bonus;
    }

    /**
     * @return mixed
     */
    public function getBps()
    {
        return $this->bps;
    }

    /**
     * @param mixed $bps
     */
    public function setBps($bps)
    {
        $this->bps = $bps;
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
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * @param mixed $team
     */
    public function setTeam($team)
    {
        $this->team = $team;
    }
}
