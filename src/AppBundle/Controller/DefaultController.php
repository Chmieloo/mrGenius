<?php

namespace AppBundle\Controller;

use AppBundle\Model\Player;
use AppBundle\Service\DataService;
use AppBundle\Service\OptionsService;
use AppBundle\Service\StatisticsService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Service\ImportService;

class DefaultController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        /** @var DataService $dataService */
        $dataService = $this->get('mrgenius.dataservice');
        $players = $dataService->loadAll();

        return $this->render('default/index.html.twig', [
            'players' => $players,
        ]);
    }

    public function predictGoalkeepersAction()
    {
        /** @var DataService $dataService */
        $dataService = $this->get('mrgenius.dataservice');

        $tableData = $dataService->predictGoalkeepersPoints();

        return $this->render('default/goalkeepers.html.twig', [
            'players' => $tableData,
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function predictAttackersAction()
    {
        /** @var DataService $dataService */
        $dataService = $this->get('mrgenius.dataservice');

        $tableData = $dataService->predictPlayersPointsByType(Player::POSITION_FORWARDER);

        return $this->render('default/attackers.html.twig', [
            'players' => $tableData,
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function predictDefendersAction()
    {
        /** @var DataService $dataService */
        $dataService = $this->get('mrgenius.dataservice');

        $tableData = $dataService->predictPlayersPointsByType(Player::POSITION_DEFENDER);

        return $this->render('default/defenders.html.twig', [
            'players' => $tableData,
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function predictMidfieldersAction()
    {
        /** @var DataService $dataService */
        $dataService = $this->get('mrgenius.dataservice');

        $tableData = $dataService->predictPlayersPointsByType(Player::POSITION_MIDFIELDER);

        return $this->render('default/midfielders.html.twig', [
            'players' => $tableData,
        ]);
    }
}
