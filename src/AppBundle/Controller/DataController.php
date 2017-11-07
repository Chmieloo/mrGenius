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
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        /** @var DataService $dataService */
        $dataService = $this->get('mrgenius.dataservice');
        $playersObjects = $dataService->loadData();

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
