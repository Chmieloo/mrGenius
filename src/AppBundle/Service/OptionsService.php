<?php

namespace AppBundle\Service;

use AppBundle\Model\Options;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

class OptionsService
{
    private $db;

    /**
     * OptionsService constructor.
     * @param Connection $db
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    /**
     * @return Options
     */
    public function getOptionsData()
    {
        $query = $this->db->createQueryBuilder()
            ->select(
                'last_imported_event',
                'bank'
            )
            ->from('options');
        $result = $query->execute()->fetch();

        return $this->generateOne($result);
    }

    /**
     * @param $data
     * @return Options
     */
    public function generateOne($data)
    {
        return new Options([
            'last_imported_event' => $data['last_imported_event'],
            'bank_balance' => $data['bank'],
        ]);
    }

    /**
     * @param $name
     * @param $value
     */
    public function saveOption($name, $value)
    {
        $sql = "UPDATE options SET $name = :value";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':value', $value);
        $stmt->execute();
    }
}