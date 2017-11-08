<?php

namespace AppBundle\Model;

class PlayerMatchHistory
{
    private $id;
    private $teamId;
    private $matchId;
    private $kickoffTime;
    private $teamHScore;
    private $teamAScore;
    private $wasHome;
    private $round;
    private $totalPoints;
    private $value;
    private $transfersBalance;
    private $selected;
    private $transfersIn;
    private $transfersOut;
    private $loanedIn;
    private $loanedOut;
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
    private $eaIndex;
    private $openPlayCrosses;
    private $bigChancesCreated;
    private $clearancesBlocksInterceptions;
    private $recoveries;
    private $keyPasses;
    private $tackles;
    private $winningGoals;
    private $attemptedPasses;
    private $completedPasses;
    private $penaltiesConceded;
    private $bigChancesMissed;
    private $errorsLeadingToGoal;
    private $errorsLeadingToGoalAttempt;
    private $tackled;
    private $offside;
    private $targetMissed;
    private $fouls;
    private $dribbles;
    private $element;
    private $fixture;
    private $opponentTeam;

    public function __construct($data)
    {
        $this->setId($data['id']);
        $this->setTeamId($data['teamId']);
        $this->setMatchId($data['matchId']);
        $this->setKickoffTime($data['kickoffTime']);
        $this->setTeamHScore($data['teamHScore']);
        $this->setTeamAScore($data['teamAScore']);
        $this->setWasHome($data['wasHome']);
        $this->setRound($data['round']);
        $this->setTotalPoints($data['totalPoints']);
        $this->setValue($data['value']);
        $this->setTransfersBalance($data['transfersBalance']);
        $this->setSelected($data['selected']);
        $this->setTransfersIn($data['transfersIn']);
        $this->setTransfersOut($data['transfersOut']);
        $this->setLoanedIn($data['loanedIn']);
        $this->setLoanedOut($data['loanedOut']);
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
        $this->setEaIndex($data['eaIndex']);
        $this->setOpenPlayCrosses($data['openPlayCrosses']);
        $this->setBigChancesCreated($data['bigChancesCreated']);
        $this->setClearancesBlocksInterceptions($data['clearancesBlocksInterceptions']);
        $this->setRecoveries($data['recoveries']);
        $this->setKeyPasses($data['keyPasses']);
        $this->setTackles($data['tackles']);
        $this->setWinningGoals($data['winningGoals']);
        $this->setAttemptedPasses($data['attemptedPasses']);
        $this->setCompletedPasses($data['completedPasses']);
        $this->setPenaltiesConceded($data['penaltiesConceded']);
        $this->setBigChancesMissed($data['bigChancesMissed']);
        $this->setErrorsLeadingToGoal($data['errorsLeadingToGoal']);
        $this->setErrorsLeadingToGoalAttempt($data['errorsLeadingToGoalAttempt']);
        $this->setTackled($data['tackled']);
        $this->setOffside($data['offside']);
        $this->setTargetMissed($data['targetMissed']);
        $this->setFouls($data['fouls']);
        $this->setDribbles($data['dribbles']);
        $this->setElement($data['element']);
        $this->setFixture($data['fixture']);
        $this->setOpponentTeam($data['opponentTeam']);
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
    public function getMatchId()
    {
        return $this->matchId;
    }

    /**
     * @param mixed $matchId
     */
    public function setMatchId($matchId)
    {
        $this->matchId = $matchId;
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
    public function getKickoffTime()
    {
        return $this->kickoffTime;
    }

    /**
     * @param mixed $kickoffTime
     */
    public function setKickoffTime($kickoffTime)
    {
        $this->kickoffTime = $kickoffTime;
    }

    /**
     * @return mixed
     */
    public function getTeamHScore()
    {
        return $this->teamHScore;
    }

    /**
     * @param mixed $teamHScore
     */
    public function setTeamHScore($teamHScore)
    {
        $this->teamHScore = $teamHScore;
    }

    /**
     * @return mixed
     */
    public function getTeamAScore()
    {
        return $this->teamAScore;
    }

    /**
     * @param mixed $teamAScore
     */
    public function setTeamAScore($teamAScore)
    {
        $this->teamAScore = $teamAScore;
    }

    /**
     * @return mixed
     */
    public function getWasHome()
    {
        return (int)$this->wasHome;
    }

    /**
     * @param mixed $wasHome
     */
    public function setWasHome($wasHome)
    {
        $this->wasHome = $wasHome;
    }

    /**
     * @return mixed
     */
    public function getRound()
    {
        return $this->round;
    }

    /**
     * @param mixed $round
     */
    public function setRound($round)
    {
        $this->round = $round;
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
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getTransfersBalance()
    {
        return $this->transfersBalance;
    }

    /**
     * @param mixed $transfersBalance
     */
    public function setTransfersBalance($transfersBalance)
    {
        $this->transfersBalance = $transfersBalance;
    }

    /**
     * @return mixed
     */
    public function getSelected()
    {
        return $this->selected;
    }

    /**
     * @param mixed $selected
     */
    public function setSelected($selected)
    {
        $this->selected = $selected;
    }

    /**
     * @return mixed
     */
    public function getTransfersIn()
    {
        return $this->transfersIn;
    }

    /**
     * @param mixed $transfersIn
     */
    public function setTransfersIn($transfersIn)
    {
        $this->transfersIn = $transfersIn;
    }

    /**
     * @return mixed
     */
    public function getTransfersOut()
    {
        return $this->transfersOut;
    }

    /**
     * @param mixed $transfersOut
     */
    public function setTransfersOut($transfersOut)
    {
        $this->transfersOut = $transfersOut;
    }

    /**
     * @return mixed
     */
    public function getLoanedIn()
    {
        return $this->loanedIn;
    }

    /**
     * @param mixed $loanedIn
     */
    public function setLoanedIn($loanedIn)
    {
        $this->loanedIn = $loanedIn;
    }

    /**
     * @return mixed
     */
    public function getLoanedOut()
    {
        return $this->loanedOut;
    }

    /**
     * @param mixed $loanedOut
     */
    public function setLoanedOut($loanedOut)
    {
        $this->loanedOut = $loanedOut;
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
    public function getEaIndex()
    {
        return $this->eaIndex;
    }

    /**
     * @param mixed $eaIndex
     */
    public function setEaIndex($eaIndex)
    {
        $this->eaIndex = $eaIndex;
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
    public function getRecoveries()
    {
        return $this->recoveries;
    }

    /**
     * @param mixed $recoveries
     */
    public function setRecoveries($recoveries)
    {
        $this->recoveries = $recoveries;
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
    public function getTackles()
    {
        return $this->tackles;
    }

    /**
     * @param mixed $tackles
     */
    public function setTackles($tackles)
    {
        $this->tackles = $tackles;
    }

    /**
     * @return mixed
     */
    public function getWinningGoals()
    {
        return $this->winningGoals;
    }

    /**
     * @param mixed $winningGoals
     */
    public function setWinningGoals($winningGoals)
    {
        $this->winningGoals = $winningGoals;
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
    public function getPenaltiesConceded()
    {
        return $this->penaltiesConceded;
    }

    /**
     * @param mixed $penaltiesConceded
     */
    public function setPenaltiesConceded($penaltiesConceded)
    {
        $this->penaltiesConceded = $penaltiesConceded;
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
    public function getTackled()
    {
        return $this->tackled;
    }

    /**
     * @param mixed $tackled
     */
    public function setTackled($tackled)
    {
        $this->tackled = $tackled;
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
    public function getTargetMissed()
    {
        return $this->targetMissed;
    }

    /**
     * @param mixed $targetMissed
     */
    public function setTargetMissed($targetMissed)
    {
        $this->targetMissed = $targetMissed;
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
    public function getElement()
    {
        return $this->element;
    }

    /**
     * @param mixed $element
     */
    public function setElement($element)
    {
        $this->element = $element;
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
    public function getOpponentTeam()
    {
        return $this->opponentTeam;
    }

    /**
     * @param mixed $opponentTeam
     */
    public function setOpponentTeam($opponentTeam)
    {
        $this->opponentTeam = $opponentTeam;
    }
}
