<?php

namespace AppBundle\Service;

use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

class ImportService
{
    /** @var Connection $db */
    private $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    public function importEvents()
    {
        $query = $this->db->createQueryBuilder()
            ->select('id')
            ->from('events');
        $result = $query->execute()->fetchAll(\PDO::FETCH_ASSOC);
        return $result;
    }
}
