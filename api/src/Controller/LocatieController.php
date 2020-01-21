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
 * Class LocatieController
 * @package App\Controller
 * @Route("/locatie-catalogus")
 */
class LocatieController extends AbstractController
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
     * @Route("/accommodations")
     * @Template
     */
    public function accomodationsAction(Request $request, EntityManagerInterface $em, CommonGroundService $commonGroundService)
    {
        $accommodations = $commonGroundService->getResourceList('https://lc.zaakonline.nl/accommodations');

        return ["accommodations"=>$accommodations];
    }

    /**
     * @Route("/place")
     * @Template
     */
    public function placesAction(Request $request, EntityManagerInterface $em, CommonGroundService $commonGroundService)
    {
        $places = $commonGroundService->getResourceList('https://lc.zaakonline.nl/places');

        return ["places"=>$places];
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
