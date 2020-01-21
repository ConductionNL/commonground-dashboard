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
 * Class ProcessController
 * @package App\Controller
 * @Route("/process-type-catalogus")
 */
class ProcessController extends AbstractController
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
     * @Route("/process-types")
     * @Template
     */
    public function processTypesAction(Request $request, EntityManagerInterface $em, CommonGroundService $commonGroundService)
    {
        $processTypes = $commonGroundService->getResourceList("https://ptc.zaakonline.nl/process_types");

        return ["processTypes"=>$processTypes];
    }

    /**
     * @Route("/stages")
     * @Template
     */
    public function stagesAction(Request $request, EntityManagerInterface $em, CommonGroundService $commonGroundService)
    {
        $stages = $commonGroundService->getResourceList("https://ptc.zaakonline.nl/stages");

        return ["stages"=>$stages];
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
