<?php

// src/Controller/DashboardController.php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatorInterface;

use App\Service\CommonGroundService;
use App\Service\ZgwService;


/**
 * Class UdController
 * @package App\Controller
 * @Route("/ud")
 */
class UdController extends AbstractController
{

    /**
     * @Route("/")
     * @Template
     */
    public function indexAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['requests'] = $commonGroundService->getResourceList(['component' => 'vrc', 'type' => 'requests'],['roles.participant'=>$this->getUser()->getPerson()])["hydra:member"];
        //$variables['tasks'] = $commonGroundService->getResourceList(['component' => 'tc', 'type' => 'tasks'])["hydra:member"];
        //$variables['events'] = $commonGroundService->getResourceList(['component' => 'arc', 'type' => 'events'])["hydra:member"];
        return $variables;
    }

}

