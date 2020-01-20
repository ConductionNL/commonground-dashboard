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
 * Class BerichtController
 * @package App\Controller
 * @Route("/bericht-service")
 */
class BerichtController extends AbstractController
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
     * @Route("/messages")
     * @Template
     */
    public function messagesAction(Request $request, EntityManagerInterface $em, CommonGroundService $commonGroundService)
    {
        $messages = $commonGroundService->getResourceList('https://vrc.zaakonline.nl/messages');

        return ["messages"=>$messages];
    }

    /**
     * @Route("/services")
     * @Template
     */
    public function servicesAction(Request $request, EntityManagerInterface $em, CommonGroundService $commonGroundService)
    {
        $services = $commonGroundService->getResourceList('https://vrc.zaakonline.nl/services');

        return ["services"=>$services];
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
