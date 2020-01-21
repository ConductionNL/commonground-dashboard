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
 * Class InstemmingController
 * @package App\Controller
 * @Route("/instemming-service")
 */
class InstemmingController extends AbstractController
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
     * @Route("/assents")
     * @Template
     */
    public function assentsAction(Request $request, EntityManagerInterface $em, CommonGroundService $commonGroundService)
    {
        $assents = $commonGroundService->getResourceList('https://irc.zaakonline.nl/assents');

        return ["assents"=>$assents];
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
