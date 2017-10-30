<?php

namespace AppBundle\Service;

use AppBundle\Model\Player;
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
                'p.element_type as positionId',
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

    /**
     * @return string
     */
    public function getRecommendedFormation()
    {
        $maxPlayersPerPosition = [
            1 => 1,
            2 => 5,
            3 => 5,
            4 => 3,
        ];

        $data = $this->getPositionsPoints();
        foreach ($data as $element) {
            $playersData[$element['positionId']] = $element;
        }
        # Remove goalkeeper, there has to be one only in basic squad
        unset($playersData[1]);

        uasort($playersData, function ($a, $b) {
            return $b['averagePoints'] - $a['averagePoints'];
        });

        $recommendedFormation = [];
        $playersOnFieldPool = 10;
        foreach ($playersData as $positionId => $element) {
            $recommendedFormation[$positionId] =
                $playersOnFieldPool > $maxPlayersPerPosition[$positionId] ?
                    $maxPlayersPerPosition[$positionId] :
                    $playersOnFieldPool;
            $playersOnFieldPool -= $maxPlayersPerPosition[$positionId];
        }

        $formation = '1-' .
            $recommendedFormation[Player::POSITION_DEFENDER] . '-' .
            $recommendedFormation[Player::POSITION_MIDFIELDER] . '-' .
            $recommendedFormation[Player::POSITION_FORWARDER];

        return $formation;
    }
}