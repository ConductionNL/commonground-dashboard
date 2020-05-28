<?php

// src/Controller/DashboardController.php

namespace App\Controller;

use Conduction\CommonGroundBundle\Service\CommonGroundService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatorInterface;



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
        $variables['requests'] = $commonGroundService->getResourceList(['component' => 'vrc', 'type' => 'requests'])["hydra:member"];
        $variables['tasks'] = $commonGroundService->getResourceList(['component' => 'tc', 'type' => 'tasks'])["hydra:member"];
        $variables['events'] = $commonGroundService->getResourceList(['component' => 'arc', 'type' => 'events'])["hydra:member"];
        return $variables;
    }

}

