<?php

namespace AppBundle\Model;

class Team
{
    private $id;
    private $name;
    private $shortName;
    private $strength;
    private $strengthAttackAway;
    private $strengthAttackHome;
    private $strengthDefenceAway;
    private $strengthDefenceHome;
    private $strengthOverallAway;
    private $strengthOverallHome;

    /**
     * Team constructor.
     * @param $data
     */
    public function __construct($data)
    {
        $this->setId($data['id']);
        $this->setName($data['name']);
        $this->setShortName($data['shortName']);
        $this->setStrength($data['strength']);
        $this->setStrengthAttackAway($data['strengthAttackAway']);
        $this->setStrengthAttackHome($data['strengthAttackHome']);
        $this->setStrengthDefenceAway($data['strengthDefenceAway']);
        $this->setStrengthDefenceHome($data['strengthDefenceHome']);
        $this->setStrengthOverallAway($data['strengthOverallAway']);
        $this->setStrengthOverallHome($data['strengthOverallHome']);

        return $this;
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
    public function getShortName()
    {
        return $this->shortName;
    }

    /**
     * @param mixed $shortName
     */
    public function setShortName($shortName)
    {
        $this->shortName = $shortName;
    }

    /**
     * @return mixed
     */
    public function getStrength()
    {
        return $this->strength;
    }

    /**
     * @param mixed $strength
     */
    public function setStrength($strength)
    {
        $this->strength = $strength;
    }

    /**
     * @return mixed
     */
    public function getStrengthAttackAway()
    {
        return $this->strengthAttackAway;
    }

    /**
     * @param mixed $strengthAttackAway
     */
    public function setStrengthAttackAway($strengthAttackAway)
    {
        $this->strengthAttackAway = $strengthAttackAway;
    }

    /**
     * @return mixed
     */
    public function getStrengthAttackHome()
    {
        return $this->strengthAttackHome;
    }

    /**
     * @param mixed $strengthAttackHome
     */
    public function setStrengthAttackHome($strengthAttackHome)
    {
        $this->strengthAttackHome = $strengthAttackHome;
    }

    /**
     * @return mixed
     */
    public function getStrengthDefenceAway()
    {
        return $this->strengthDefenceAway;
    }

    /**
     * @param mixed $strengthDefenceAway
     */
    public function setStrengthDefenceAway($strengthDefenceAway)
    {
        $this->strengthDefenceAway = $strengthDefenceAway;
    }

    /**
     * @return mixed
     */
    public function getStrengthDefenceHome()
    {
        return $this->strengthDefenceHome;
    }

    /**
     * @param mixed $strengthDefenceHome
     */
    public function setStrengthDefenceHome($strengthDefenceHome)
    {
        $this->strengthDefenceHome = $strengthDefenceHome;
    }

    /**
     * @return mixed
     */
    public function getStrengthOverallAway()
    {
        return $this->strengthOverallAway;
    }

    /**
     * @param mixed $strengthOverallAway
     */
    public function setStrengthOverallAway($strengthOverallAway)
    {
        $this->strengthOverallAway = $strengthOverallAway;
    }

    /**
     * @return mixed
     */
    public function getStrengthOverallHome()
    {
        return $this->strengthOverallHome;
    }

    /**
     * @param mixed $strengthOverallHome
     */
    public function setStrengthOverallHome($strengthOverallHome)
    {
        $this->strengthOverallHome = $strengthOverallHome;
    }
}
