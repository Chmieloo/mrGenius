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
    private $nextEvent = 12;

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
        $tableData = [];

        /** @var DataService $dataService */
        $dataService = $this->get('mrgenius.dataservice');

        $goalkeeperHistory = $dataService->loadPlayersHistoryByType([Player::POSITION_GOALKEEPER]);
        $goalkeeperFixtures = $dataService->loadPlayerFixturesByType([Player::POSITION_GOALKEEPER], $this->nextEvent);

        //$goalkeeperHistory = [$goalkeeperHistory[260]];
        //$goalkeeperFixtures = [$goalkeeperFixtures[260]];
        foreach ($goalkeeperHistory as $goalkeeperId => $goalkeeperData) {
            $avgMinutes = $this->avgMinutes($goalkeeperData);
            if ($avgMinutes > 10) {
                $currentPlayerHistory = $goalkeeperHistory[$goalkeeperId];
                $currentPlayerFixture = $goalkeeperFixtures[$goalkeeperId][$this->nextEvent];

                # Predict influence first
                $predictedPlayerInfluence = $this->predictPlayerInfluence($currentPlayerHistory, $currentPlayerFixture);

                if ($predictedPlayerInfluence) {
                    # Get historical data and create samples and point results
                    foreach ($currentPlayerHistory as $roundData) {
                        $samples[] = [
                            $roundData['influence'],
                            $roundData['value'],
                            $roundData['teamStrength'],
                            $roundData['opponentStrength'],
                        ];
                        $data[] = $roundData['totalPoints'];
                    }

                    # Get fixture data
                    $predictionSample = [
                        $predictedPlayerInfluence,
                        $currentPlayerFixture['value'],
                        $currentPlayerFixture['teamStrength'],
                        $currentPlayerFixture['opponentStrength'],
                    ];

                    # Predict points
                    $prediction = $dataService->predictRegression($samples, $data, $predictionSample);

                    $currentPlayerFixture['predictedPerformance'] = $predictedPlayerInfluence;
                    $currentPlayerFixture['predictedPoints'] = $prediction;
                    $tableData[$goalkeeperId] = $currentPlayerFixture;
                }
            }
        }

        return $this->render('default/goalkeepers.html.twig', [
            'players' => $tableData,
        ]);
    }

    private function avgMinutes($data)
    {
        $minutes = 0;
        $eventCount = count($data);
        foreach ($data as $datum) {
            $minutes += $datum['minutes'];
        }
        $avgMinutes = (int)($minutes / $eventCount);

        return $avgMinutes;
    }

    public function predictAttackersAction()
    {
        $tableData = [];

        /** @var DataService $dataService */
        $dataService = $this->get('mrgenius.dataservice');

        $attackerHistory = $dataService->loadPlayersHistoryByType([Player::POSITION_FORWARDER]);
        $attackerFixtures = $dataService->loadPlayerFixturesByType([Player::POSITION_FORWARDER], $this->nextEvent);

        //$attackerHistory = [$attackerHistory[303]];
        //$attackerFixtures = [$attackerFixtures[303]];

        foreach ($attackerHistory as $attackerId => $attackerData) {
            $avgMinutes = $this->avgMinutes($attackerData);
            if ($avgMinutes > 10) {
                $currentPlayerHistory = $attackerHistory[$attackerId];
                $currentPlayerFixture = $attackerFixtures[$attackerId][$this->nextEvent];

                # Predict i,c,t first
                $predictedPlayerInfluence = $this->predictPlayerInfluence($currentPlayerHistory, $currentPlayerFixture);
                $predictedPlayerCreativity = $this->predictPlayerCreativity($currentPlayerHistory, $currentPlayerFixture);
                $predictedPlayerThreat = $this->predictPlayerThreat($currentPlayerHistory, $currentPlayerFixture);

                if ($predictedPlayerInfluence) {
                    # Get historical data and create samples and point results
                    foreach ($currentPlayerHistory as $roundData) {
                        $samples[] = [
                            $roundData['influence'],
                            $roundData['creativity'],
                            $roundData['threat'],
                            $roundData['value'],
                            $roundData['teamStrength'],
                            $roundData['opponentStrength'],
                        ];
                        $data[] = $roundData['totalPoints'];
                    }

                    # Get fixture data
                    $predictionSample = [
                        $predictedPlayerInfluence,
                        $predictedPlayerCreativity,
                        $predictedPlayerThreat,
                        $currentPlayerFixture['value'],
                        $currentPlayerFixture['teamStrength'],
                        $currentPlayerFixture['opponentStrength'],
                    ];

                    # Predict points
                    $prediction = $dataService->predictRegression($samples, $data, $predictionSample);

                    $currentPlayerFixture['predictedInfluence'] = $predictedPlayerInfluence;
                    $currentPlayerFixture['predictedCreativity'] = $predictedPlayerCreativity;
                    $currentPlayerFixture['predictedThreat'] = $predictedPlayerThreat;
                    $currentPlayerFixture['predictedPoints'] = $prediction;
                    $tableData[$attackerId] = $currentPlayerFixture;
                }
            }
        }

        return $this->render('default/attackers.html.twig', [
            'players' => $tableData,
        ]);
    }


    public function predictDefendersAction()
    {
        $tableData = [];

        /** @var DataService $dataService */
        $dataService = $this->get('mrgenius.dataservice');

        $attackerHistory = $dataService->loadPlayersHistoryByType([Player::POSITION_DEFENDER]);
        $attackerFixtures = $dataService->loadPlayerFixturesByType([Player::POSITION_DEFENDER], $this->nextEvent);

        foreach ($attackerHistory as $goalkeeperId => $goalkeeperData) {
            $currentPlayerHistory = $attackerHistory[$goalkeeperId];
            $currentPlayerFixture = $attackerFixtures[$goalkeeperId][$this->nextEvent];

            # Predict i,c,t first
            $predictedPlayerInfluence = $this->predictPlayerInfluence($currentPlayerHistory, $currentPlayerFixture);
            $predictedPlayerCreativity = $this->predictPlayerCreativity($currentPlayerHistory, $currentPlayerFixture);
            $predictedPlayerThreat = $this->predictPlayerThreat($currentPlayerHistory, $currentPlayerFixture);

            if ($predictedPlayerInfluence) {
                # Get historical data and create samples and point results
                foreach ($currentPlayerHistory as $roundData) {
                    $samples[] = [
                        $roundData['influence'],
                        $roundData['creativity'],
                        $roundData['threat'],
                        $roundData['value'],
                        $roundData['teamStrength'],
                        $roundData['opponentStrength'],
                    ];
                    $data[] = $roundData['totalPoints'];
                }

                # Get fixture data
                $predictionSample = [
                    $predictedPlayerInfluence,
                    $predictedPlayerCreativity,
                    $predictedPlayerThreat,
                    $currentPlayerFixture['value'],
                    $currentPlayerFixture['teamStrength'],
                    $currentPlayerFixture['opponentStrength'],
                ];

                # Predict points
                $prediction = $dataService->predictRegression($samples, $data, $predictionSample);

                $currentPlayerFixture['predictedInfluence'] = $predictedPlayerInfluence;
                $currentPlayerFixture['predictedCreativity'] = $predictedPlayerCreativity;
                $currentPlayerFixture['predictedThreat'] = $predictedPlayerThreat;
                $currentPlayerFixture['predictedPoints'] = $prediction;
                $tableData[$goalkeeperId] = $currentPlayerFixture;
            }
        }

        return $this->render('default/defenders.html.twig', [
            'players' => $tableData,
        ]);
    }

    /**
     * @param $playerHistory
     * @param $playerFixture
     * @return int|mixed
     */
    private function predictPlayerInfluence($playerHistory, $playerFixture)
    {
        /** @var DataService $dataService */
        $dataService = $this->get('mrgenius.dataservice');

        $samples = [];
        $data = [];
        $prediction = 0;

        foreach ($playerHistory as $roundData) {
            $samples[] = [
                $roundData['value'],
                $roundData['teamStrength'],
                $roundData['opponentStrength'],
            ];
            $data[] = $roundData['influence'];
        }

        $predictionSample = [
            $playerFixture['value'],
            $playerFixture['teamStrength'],
            $playerFixture['opponentStrength'],
        ];

        if ($data && $samples) {
            $prediction = $dataService->predictRegression($samples, $data, $predictionSample);
        }

        return $prediction;
    }

    /**
     * @param $playerHistory
     * @param $playerFixture
     * @return int|mixed
     */
    private function predictPlayerCreativity($playerHistory, $playerFixture)
    {
        /** @var DataService $dataService */
        $dataService = $this->get('mrgenius.dataservice');

        $samples = [];
        $data = [];
        $prediction = 0;

        foreach ($playerHistory as $roundData) {
            $samples[] = [
                $roundData['value'],
                $roundData['teamStrength'],
                $roundData['opponentStrength'],
            ];
            $data[] = $roundData['creativity'];
        }

        $predictionSample = [
            $playerFixture['value'],
            $playerFixture['teamStrength'],
            $playerFixture['opponentStrength'],
        ];

        if ($data && $samples) {
            $prediction = $dataService->predictRegression($samples, $data, $predictionSample);
        }

        return $prediction;
    }

    /**
     * @param $playerHistory
     * @param $playerFixture
     * @return int|mixed
     */
    private function predictPlayerThreat($playerHistory, $playerFixture)
    {
        /** @var DataService $dataService */
        $dataService = $this->get('mrgenius.dataservice');

        $samples = [];
        $data = [];
        $prediction = 0;

        foreach ($playerHistory as $roundData) {
            $samples[] = [
                $roundData['value'],
                $roundData['teamStrength'],
                $roundData['opponentStrength'],
            ];
            $data[] = $roundData['threat'];
        }

        $predictionSample = [
            $playerFixture['value'],
            $playerFixture['teamStrength'],
            $playerFixture['opponentStrength'],
        ];

        if ($data && $samples) {
            $prediction = $dataService->predictRegression($samples, $data, $predictionSample);
        }

        return $prediction;
    }
}
