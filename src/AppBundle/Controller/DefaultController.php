<?php

namespace AppBundle\Controller;

use AppBundle\Service\OptionsService;
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
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
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
}
