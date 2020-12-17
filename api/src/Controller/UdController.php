<?php

// src/Controller/DashboardController.php

namespace App\Controller;

use Conduction\CommonGroundBundle\Service\CommonGroundService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class UdController.
 *
 * @Route("/")
 */
class UdController extends AbstractController
{
    /**
     * @Route("/ud")
     * @Route("/persoonlijk")
     * @Template
     */
    public function indexAction(Request $request, CommonGroundService $commonGroundService)
    {
        $variables = [];
        $application = $commonGroundService->getResource(['component' => 'wrc', 'type' => 'applications', 'id' => getenv('APP_ID')]);
        $variables['requests'] = $commonGroundService->getResourceList(['component' => 'vrc', 'type' => 'requests'], "organization={$this->getUser()->getOrganization()}")['hydra:member'];
        //$variables['tasks'] = $commonGroundService->getResourceList(['component' => 'tc', 'type' => 'tasks'])['hydra:member'];
       //$variables['events'] = $commonGroundService->getResourceList(['component' => 'arc', 'type' => 'events'], "calendar.organization={$this->getUser()->getOrganization()}")['hydra:member'];

        return $variables;
    }

    /**
     * @Route("/ud/applications")
     * @Template
     */
    public function Applications(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
    }
}
