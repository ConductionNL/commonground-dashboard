<?php

// src/Controller/DashboardController.php

namespace App\Controller;

use App\Service\CommonGroundService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DashboardController.
 *
 * @Route("/dashboard")
 */
class DashboardController extends AbstractController
{
    /**
     * @Route("/")
     * @Template
     */
    public function indexAction(Request $request, EntityManagerInterface $em, CommonGroundService $commonGroundService)
    {
        $components = $commonGroundService->getComponentList();

        foreach ($components as $key=>$component) {
            //$components[$key] = $commonGroundService->getComponentHealth($key);
            $components[$key] = $commonGroundService->getComponentResources($key);
        }

        return ['components'=>$components];
    }
}
