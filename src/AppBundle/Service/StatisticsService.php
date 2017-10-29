<?php

namespace AppBundle\Service;

use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

class StatisticsService
{
    /** @var Connection $db */
    private $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    /**
     * @return mixed
     */
    public function getPositionsPoints()
    {
        $query = $this->db->createQueryBuilder()
            ->select(
                'p.element_type',
                'pos.name as position',
                'count(p.element_type) as numPlayers',
                'sum(p.total_points) as totalPoints',
                'sum(p.total_points) / count(p.element_type) as averagePoints'
            )
            ->from('players', 'p')
            ->join('p', 'positions', 'pos', 'pos.id = p.element_type')
            ->groupBy('p.element_type');
        $result = $query->execute()->fetchAll(\PDO::FETCH_ASSOC);

        return $result;
    }

}