<?php

namespace AppBundle\Model;

class Performance
{
    private $assists;
    private $attemptedPasses;
    private $bigChancesCreated;
    private $bigChancesMissed;
    private $bonus;
    private $bps;
    private $cleanSheets;
    private $clearancesBlocksInterceptions;
    private $completedPasses;
    private $creativity;
    private $dribbles;
    private $errorsLeadingToGoal;
    private $errorsLeadingToGoalAttempt;
    private $fixture;
    private $fouls;
    private $goalsConceded;
    private $goalsScored;
    private $ictIndex;
    private $influence;
    private $keyPasses;
    private $minutes;
    private $offside;
    private $openPlayCrosses;
    private $opponent;
    private $opponentId;

    /**
     * Performance constructor.
     * @param $data
     */
    public function __construct($data)
    {
        $this->setAssists($data['assists']);
        $this->setAttemptedPasses($data['attemptedPasses']);
        $this->setBigChancesCreated($data['bigChancesCreated']);
        $this->setBigChancesMissed($data['bigChancesMissed']);
        $this->setBonus($data['bonus']);
        $this->setBps($data['bps']);
        $this->setCleanSheets($data['cleanSheets']);
        $this->setClearancesBlocksInterceptions($data['clearancesBlocksInterceptions']);
        $this->setCompletedPasses($data['completedPasses']);
        $this->setCreativity($data['creativity']);
        $this->setDribbles($data['dribbles']);
        $this->setErrorsLeadingToGoal($data['errorsLeadingToGoal']);
        $this->setErrorsLeadingToGoalAttempt($data['errorsLeadingToGoalAttempt']);
        $this->setFixture($data['fixture']);
        $this->setFouls($data['fouls']);
        $this->setGoalsConceded($data['goalsConceded']);
        $this->setGoalsScored($data['goalsScored']);
        $this->setIctIndex($data['ictIndex']);
        $this->setInfluence($data['influence']);
        $this->setKeyPasses($data['keyPasses']);
        $this->setMinutes($data['minutes']);
        $this->setOffside($data['offside']);
        $this->setOpenPlayCrosses($data['openPlayCrosses']);
        $this->setOpponent($data['opponent']);
        $this->setPpponentId($data['opponentId']);
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
    public function getAttemptedPasses()
    {
        return $this->attemptedPasses;
    }

    /**
     * @param mixed $attemptedPasses
     */
    public function setAttemptedPasses($attemptedPasses)
    {
        $this->attemptedPasses = $attemptedPasses;
    }

    /**
     * @return mixed
     */
    public function getBigChancesCreated()
    {
        return $this->bigChancesCreated;
    }

    /**
     * @param mixed $bigChancesCreated
     */
    public function setBigChancesCreated($bigChancesCreated)
    {
        $this->bigChancesCreated = $bigChancesCreated;
    }

    /**
     * @return mixed
     */
    public function getBigChancesMissed()
    {
        return $this->bigChancesMissed;
    }

    /**
     * @param mixed $bigChancesMissed
     */
    public function setBigChancesMissed($bigChancesMissed)
    {
        $this->bigChancesMissed = $bigChancesMissed;
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
    public function getClearancesBlocksInterceptions()
    {
        return $this->clearancesBlocksInterceptions;
    }

    /**
     * @param mixed $clearancesBlocksInterceptions
     */
    public function setClearancesBlocksInterceptions($clearancesBlocksInterceptions)
    {
        $this->clearancesBlocksInterceptions = $clearancesBlocksInterceptions;
    }

    /**
     * @return mixed
     */
    public function getCompletedPasses()
    {
        return $this->completedPasses;
    }

    /**
     * @param mixed $completedPasses
     */
    public function setCompletedPasses($completedPasses)
    {
        $this->completedPasses = $completedPasses;
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
    public function getDribbles()
    {
        return $this->dribbles;
    }

    /**
     * @param mixed $dribbles
     */
    public function setDribbles($dribbles)
    {
        $this->dribbles = $dribbles;
    }

    /**
     * @return mixed
     */
    public function getErrorsLeadingToGoal()
    {
        return $this->errorsLeadingToGoal;
    }

    /**
     * @param mixed $errorsLeadingToGoal
     */
    public function setErrorsLeadingToGoal($errorsLeadingToGoal)
    {
        $this->errorsLeadingToGoal = $errorsLeadingToGoal;
    }

    /**
     * @return mixed
     */
    public function getErrorsLeadingToGoalAttempt()
    {
        return $this->errorsLeadingToGoalAttempt;
    }

    /**
     * @param mixed $errorsLeadingToGoalAttempt
     */
    public function setErrorsLeadingToGoalAttempt($errorsLeadingToGoalAttempt)
    {
        $this->errorsLeadingToGoalAttempt = $errorsLeadingToGoalAttempt;
    }

    /**
     * @return mixed
     */
    public function getFixture()
    {
        return $this->fixture;
    }

    /**
     * @param mixed $fixture
     */
    public function setFixture($fixture)
    {
        $this->fixture = $fixture;
    }

    /**
     * @return mixed
     */
    public function getFouls()
    {
        return $this->fouls;
    }

    /**
     * @param mixed $fouls
     */
    public function setFouls($fouls)
    {
        $this->fouls = $fouls;
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
    public function getKeyPasses()
    {
        return $this->keyPasses;
    }

    /**
     * @param mixed $keyPasses
     */
    public function setKeyPasses($keyPasses)
    {
        $this->keyPasses = $keyPasses;
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
    public function getOffside()
    {
        return $this->offside;
    }

    /**
     * @param mixed $offside
     */
    public function setOffside($offside)
    {
        $this->offside = $offside;
    }

    /**
     * @return mixed
     */
    public function getOpenPlayCrosses()
    {
        return $this->openPlayCrosses;
    }

    /**
     * @param mixed $openPlayCrosses
     */
    public function setOpenPlayCrosses($openPlayCrosses)
    {
        $this->openPlayCrosses = $openPlayCrosses;
    }

    /**
     * @return mixed
     */
    public function getOpponent()
    {
        return $this->opponent;
    }

    /**
     * @param mixed $opponent
     */
    public function setOpponent($opponent)
    {
        $this->opponent = $opponent;
    }

    /**
     * @return mixed
     */
    public function getOpponentId()
    {
        return $this->opponentId;
    }

    /**
     * @param mixed $opponentId
     */
    public function setOpponentId($opponentId)
    {
        $this->opponentId = $opponentId;
    }
}
