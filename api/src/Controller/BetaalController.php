<?php

// src/Controller/DefaultController.php

namespace App\Controller;

use App\Service\CommonGroundService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class BetaalController
 * @package App\Controller
 * @Route("/betaal-service")
 */
class BetaalController extends AbstractController
{
    /**
     * @Route("/")
     * @Template
     */
    public function indexAction(Request $request, EntityManagerInterface $em)
    {
        return [];
    }

    /**
     * @Route("/invoices")
     * @Template
     */
    public function invoicesAction(Request $request, EntityManagerInterface $em, CommonGroundService $commonGroundService)
    {
        $invoices = $commonGroundService->getResourceList('https://bc.zaakonline.nl/invoices');

        return ["invoices"=>$invoices];
    }

    /**
     * @Route("/edit/{id}")
     * @Template
     */
    public function editAction(Request $request, EntityManagerInterface $em, CommonGroundService $commonGroundService, $id)
    {
        $invoices= $commonGroundService->getResource('//https://vrc.zaakonline.nl/requests/'.$id);

        return ["invoices"=>$invoices];
    }

    /**
     * @Route("/config")
     * @Template
     */
    public function configAction(Request $request, EntityManagerInterface $em)
    {
        return [];
    }
}
