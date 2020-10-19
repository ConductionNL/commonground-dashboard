<?php

// src/Controller/DashboardController.php

namespace App\Controller;

use Conduction\CommonGroundBundle\Service\CommonGroundService;
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
        $requests = $commonGroundService->getResourceList('https://vrc.huwelijksplanner.online/requests');

        return ['requests'=>$requests];
    }


    /**
     * @Route("/persoonlijk")
     */
    public function persoonlijkAction()
    {
        return $this->redirect('/ud');
    }

}
