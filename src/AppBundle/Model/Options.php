<?php

namespace AppBundle\Model;

class Options
{
    private $lastImportedEvent;
    private $bankBalance;

    public function __construct($data)
    {
        $this->setLastImportedEvent($data['last_imported_event']);
        $this->setBankBalance($data['bank_balance']);
    }

    /**
     * @return mixed
     */
    public function getLastImportedEvent()
    {
        return $this->lastImportedEvent;
    }

    /**
     * @param mixed $lastImportedEvent
     */
    public function setLastImportedEvent($lastImportedEvent)
    {
        $this->lastImportedEvent = $lastImportedEvent;
    }

    /**
     * @return mixed
     */
    public function getBankBalance()
    {
        return $this->bankBalance;
    }

    /**
     * @param mixed $bankBalance
     */
    public function setBankBalance($bankBalance)
    {
        $this->bankBalance = $bankBalance;
    }
}