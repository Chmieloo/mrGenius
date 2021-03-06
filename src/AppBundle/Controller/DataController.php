<?php

namespace AppBundle\Controller;

use AppBundle\Model\Player;
use AppBundle\Model\Team;
use AppBundle\Service\DataService;
use AppBundle\Service\OptionsService;
use AppBundle\Service\StatisticsService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Service\ImportService;

class DataController extends Controller
{
    /**
     *
     */
    public function importHistoryAction()
    {
        /** @var DataService $dataService */
        $dataService = $this->get('mrgenius.dataservice');
        /** @var ImportService $importService */
        $importService = $this->get('mrgenius.importservice');

        $players = $dataService->getAllPlayers();
        $importService->importHistoryByPlayers($players);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function importPlayersAction()
    {
        /** @var ImportService $importService */
        $importService = $this->get('mrgenius.importservice');

        /** @var Player[] $players */
        $players = $importService->importPlayers();

        return $this->render('default/players.html.twig', [
            'players' => $players,
        ]);

    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function importTeamsAction()
    {
        /** @var ImportService $importService */
        $importService = $this->get('mrgenius.importservice');

        /** @var Team[] $teams */
        $teams = $importService->importTeams();

        return $this->render('default/teams.html.twig', [
            'teams' => $teams,
        ]);

    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function nextAction()
    {
        /** @var DataService $dataService */
        $dataService = $this->get('mrgenius.dataservice');

        /** @var Player[] $teams */
        $players = $dataService->getAllPredictions();

        return $this->render('default/predictions.html.twig', [
            'players' => $players,
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function currentAction()
    {
        /** @var DataService $dataService */
        $dataService = $this->get('mrgenius.dataservice');

        # CALCULATION
        /** @var Player[] $teams */
        $players = $dataService->getCurrentTeamPredictions();

        /** @var Player[] $teams */
        $players = $dataService->getMyTeamPredictions();

        return $this->render('default/mypredictions.html.twig', [
            'players' => $players,
        ]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        /** @var DataService $dataService */
        $dataService = $this->get('mrgenius.dataservice');
        $playersObjects = $dataService->loadAll();

        $players = $dataService->getPlayerByTypeFormatted($playersObjects);
        $formation = $dataService->getFormation($playersObjects);

        $formationString = '1-' .
            $formation[Player::POSITION_DEFENDER] . '-' .
            $formation[Player::POSITION_MIDFIELDER] . '-' .
            $formation[Player::POSITION_FORWARDER];

        # TODO - get goalkeeper

        # TODO - get rest of the team by points priority

        $squad = $dataService->getSquad();

        $json = ' 
        {
            "team_id": 5304993,
  "event": 11,
  "first_eleven": [260,394,285,374,264,245,13,97,382,255,247],
  "substitutes": [420,367,63,151],
  "captain": 285,
  "vice_captain": 260,
  "wildcard": false,
  "bench_boost": false,
  "free_hit": false,
  "triple_captain": false
}';

echo $json;

        return $this->render('default/index.html.twig', [
            'players' => $players,
            'formation' => $formationString
        ]);
    }
}
