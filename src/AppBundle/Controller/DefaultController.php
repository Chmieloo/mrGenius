<?php

namespace AppBundle\Controller;

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
        // Create a stream
        $opts = array(
            'http' => array(
                'method' => "GET",
                'header' =>
                    "Content-Type: application/json\r\n" .
                    "X-Api-Key: 61AFF35F\r\n"
            )
        );

        //$context = stream_context_create($opts);

        // Open the file using the HTTP headers set above
        //$file = file_get_contents('https://fpl.tlj.no/api/players', false, $context);
        //$test = json_decode($file);
        //var_dump($test[0]);

        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR,
        ]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function infoAction(Request $request)
    {
        /** @var OptionsService $optionsService */
        $optionsService = $this->get('mrgenius.optionsservice');
        $options = $optionsService->getOptionsData();

        return $this->render('default/info.html.twig', ['options' => $options]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateAction()
    {
        /** @var ImportService $importService */
        $importService = $this->get('mrgenius.importservice');
        $lastOnlineFinishedEventId = $importService->getLastOnlineFinishedEventId();

        /** @var OptionsService $optionsService */
        $optionsService = $this->get('mrgenius.optionsservice');
        $options = $optionsService->getOptionsData();
        $lastImportedEventId = $options->getLastImportedEvent();

        if ($lastImportedEventId < $lastOnlineFinishedEventId) {
            $importAvailable = true;
        }

        return $this->render('default/update.html.twig', [
            'lastOnline' => $lastOnlineFinishedEventId,
            'lastImported' => $lastImportedEventId,
            'importAvailable' => $importAvailable
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function importAction()
    {
        /** @var ImportService $importService */
        $importService = $this->get('mrgenius.importservice');
        /** @var OptionsService $optionsService */
        $optionsService = $this->get('mrgenius.optionsservice');

        if ($lastImportedId = $importService->importOnlineData()) {
            $optionsService->saveOption('last_imported_event', $lastImportedId);
        }
        return $this->render('default/import.html.twig', []);
    }

    public function statsFormationPointsAction()
    {
        /** @var StatisticsService $statisticsService */
        $statisticsService = $this->get('mrgenius.statisticsService');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function recommendationsAction()
    {
        /** @var StatisticsService $statisticsService */
        $statisticsService = $this->get('mrgenius.statisticsservice');
        $recommendedFormation = $statisticsService->getRecommendedFormation();

        return $this->render('default/recommendations.html.twig', [
            'recommendedFormation' => $recommendedFormation
        ]);
    }
}
