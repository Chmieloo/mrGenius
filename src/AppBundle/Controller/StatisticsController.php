<?php

namespace AppBundle\Controller;

use AppBundle\Service\OptionsService;
use AppBundle\Service\StatisticsService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Service\ImportService;
use AppBundle\Model\Player;

class StatisticsController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function positionsAction()
    {
        /** @var StatisticsService $statisticsService */
        $statisticsService = $this->get('mrgenius.statisticsservice');

        $data = $statisticsService->getPositionsPoints();
        $recommendedFormation = $statisticsService->getRecommendedFormation();

        return $this->render('default/statistics_positions.html.twig', [
            'statistics' => $data,
            'recommendedFormation' => $recommendedFormation
        ]);
    }
}
