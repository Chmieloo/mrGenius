<?php

namespace AppBundle\Controller;

use AppBundle\Service\OptionsService;
use AppBundle\Service\StatisticsService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Service\ImportService;

class StatisticsController extends Controller
{
    public function positionsAction()
    {
        /** @var StatisticsService $statisticsService */
        $statisticsService = $this->get('mrgenius.statisticsservice');

        $data = $statisticsService->getPositionsPoints();

        return $this->render('default/statistics_positions.html.twig', [
            'statistics' => $data
        ]);
    }
}
