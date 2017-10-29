<?php

namespace AppBundle\Model;

class Event
{
    private $id;
    private $name;
    private $averageEntryScore;
    private $finished;
    private $highestScore;
    private $isPrevious;
    private $isCurrent;
    private $isNext;

    public function __construct($data)
    {
        $this->setId($data['id']);
        $this->setName($data['name']);
        $this->setAverageEntryScore($data['averageEntryScore']);
        $this->setFinished($data['isFinished']);
        $this->setHighestScore($data['highestScore']);
        $this->setIsPrevious($data['isPrevious']);
        $this->setIsCurrent($data['isCurrent']);
        $this->setIsNext($data['isNext']);
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getAverageEntryScore()
    {
        return $this->averageEntryScore;
    }

    /**
     * @param mixed $averageEntryScore
     */
    public function setAverageEntryScore($averageEntryScore)
    {
        $this->averageEntryScore = $averageEntryScore;
    }

    /**
     * @return mixed
     */
    public function getFinished()
    {
        return (int)$this->finished;
    }

    /**
     * @param mixed $finished
     */
    public function setFinished($finished)
    {
        $this->finished = $finished;
    }

    /**
     * @return mixed
     */
    public function getHighestScore()
    {
        return $this->highestScore;
    }

    /**
     * @param mixed $highestScore
     */
    public function setHighestScore($highestScore)
    {
        $this->highestScore = $highestScore;
    }

    /**
     * @return mixed
     */
    public function getIsPrevious()
    {
        return (int)$this->isPrevious;
    }

    /**
     * @param mixed $isPrevious
     */
    public function setIsPrevious($isPrevious)
    {
        $this->isPrevious = $isPrevious;
    }

    /**
     * @return mixed
     */
    public function getIsCurrent()
    {
        return (int)$this->isCurrent;
    }

    /**
     * @param mixed $isCurrent
     */
    public function setIsCurrent($isCurrent)
    {
        $this->isCurrent = $isCurrent;
    }

    /**
     * @return mixed
     */
    public function getIsNext()
    {
        return (int)$this->isNext;
    }

    /**
     * @param mixed $isNext
     */
    public function setIsNext($isNext)
    {
        $this->isNext = $isNext;
    }
}
