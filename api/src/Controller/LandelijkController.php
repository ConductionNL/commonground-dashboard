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
 * Class LandelijkController
 * @package App\Controller
 * @Route("/landelijke-tabellen-catalogus")
 */
class LandelijkController extends AbstractController
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
     * @Route("/rsin-numbers")
     * @Template
     */
    public function rsinNumbersAction(Request $request, EntityManagerInterface $em, CommonGroundService $commonGroundService)
    {
        $rsinNumbers = $commonGroundService->getResourceList('https://ltc.zaakonline.nl/rsin');

        return ["rsinNumbers"=>$rsinNumbers];
    }

    /**
     * @Route("/tabel-32's")
     * @Template
     */
    public function tabel32sAction(Request $request, EntityManagerInterface $em, CommonGroundService $commonGroundService)
    {
        $tabel32s = $commonGroundService->getResourceList('https://ltc.zaakonline.nl/tabel32');

        return ["tabel32s"=>$tabel32s];
    }

    /**
     * @Route("/tabel-33's")
     * @Template
     */
    public function tabel33sAction(Request $request, EntityManagerInterface $em, CommonGroundService $commonGroundService)
    {
        $tabel33s = $commonGroundService->getResourceList('https://ltc.zaakonline.nl/tabel33');

        return ["tabel33s"=>$tabel33s];
    }

    /**
     * @Route("/tabel-34's")
     * @Template
     */
    public function tabel34sAction(Request $request, EntityManagerInterface $em, CommonGroundService $commonGroundService)
    {
        $tabel34s = $commonGroundService->getResourceList('https://ltc.zaakonline.nl/tabel34');

        return ["tabel34s"=>$tabel34s];
    }

    /**
     * @Route("/tabel-36's")
     * @Template
     */
    public function tabel36sAction(Request $request, EntityManagerInterface $em, CommonGroundService $commonGroundService)
    {
        $tabel36s = $commonGroundService->getResourceList('https://ltc.zaakonline.nl/tabel36');

        return ["tabel36s"=>$tabel36s];
    }

    /**
     * @Route("/tabel-37's")
     * @Template
     */
    public function tabel37sAction(Request $request, EntityManagerInterface $em, CommonGroundService $commonGroundService)
    {
        $tabel37s = $commonGroundService->getResourceList('https://ltc.zaakonline.nl/tabel37');

        return ["tabel37s"=>$tabel37s];
    }

    /**
     * @Route("/tabel-38's")
     * @Template
     */
    public function tabel38sAction(Request $request, EntityManagerInterface $em, CommonGroundService $commonGroundService)
    {
        $tabel38s = $commonGroundService->getResourceList('https://ltc.zaakonline.nl/tabel38');

        return ["tabel38s"=>$tabel38s];
    }

    /**
     * @Route("/tabel-39's")
     * @Template
     */
    public function tabel39sAction(Request $request, EntityManagerInterface $em, CommonGroundService $commonGroundService)
    {
        $tabel39s = $commonGroundService->getResourceList('https://ltc.zaakonline.nl/tabel39');

        return ["tabel39s"=>$tabel39s];
    }

    /**
     * @Route("/tabel-41's")
     * @Template
     */
    public function tabel41sAction(Request $request, EntityManagerInterface $em, CommonGroundService $commonGroundService)
    {
        $tabel41s = $commonGroundService->getResourceList('https://ltc.zaakonline.nl/tabel41');

        return ["tabe41s"=>$tabel41s];
    }

    /**
     * @Route("/tabel-48's")
     * @Template
     */
    public function tabel48sAction(Request $request, EntityManagerInterface $em, CommonGroundService $commonGroundService)
    {
        $tabel48s = $commonGroundService->getResourceList('https://ltc.zaakonline.nl/tabel48');

        return ["tabe48s"=>$tabel48s];
    }

    /**
     * @Route("/tabel-49's")
     * @Template
     */
    public function tabel49sAction(Request $request, EntityManagerInterface $em, CommonGroundService $commonGroundService)
    {
        $tabel49s = $commonGroundService->getResourceList('https://ltc.zaakonline.nl/tabel49');

        return ["tabel49s"=>$tabel49s];
    }

    /**
     * @Route("/tabel-55's")
     * @Template
     */
    public function tabel55sAction(Request $request, EntityManagerInterface $em, CommonGroundService $commonGroundService)
    {
        $tabel55s = $commonGroundService->getResourceList('https://ltc.zaakonline.nl/tabel55');

        return ["tabel55s"=>$tabel55s];
    }

    /**
     * @Route("/tabel-56's")
     * @Template
     */
    public function tabel56sAction(Request $request, EntityManagerInterface $em, CommonGroundService $commonGroundService)
    {
        $tabel56s = $commonGroundService->getResourceList('https://ltc.zaakonline.nl/tabel56');

        return ["tabel56s"=>$tabel56s];
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
