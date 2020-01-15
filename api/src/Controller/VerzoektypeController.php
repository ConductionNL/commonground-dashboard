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
 * Class VerzoektypeController
 * @package App\Controller
 * @Route("/verzoek-type-catalogus")
 */
class VerzoektypeController extends AbstractController
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
     * @Route("/request-types")
     * @Template
     */
    public function requestTypesAction(Request $request, EntityManagerInterface $em, CommonGroundService $commonGroundService)
    {
        $requestTypes = $commonGroundService->getResourceList('https://vtc.zaakonline.nl/requestTypes');

        return ["requestTypes"=>$requestTypes];
    }

    /**
     * @Route("/properties")
     * @Template
     */
    public function propertiesAction(Request $request, EntityManagerInterface $em, CommonGroundService $commonGroundService)
    {
        $properties = $commonGroundService->getResourceList('https://vtc.zaakonline.nl/properties');

        return ["properties"=>$properties];
    }

    /**
     * @Route("/edit/{id}")
     * @Template
     */
    public function editAction(Request $request, EntityManagerInterface $em, CommonGroundService $commonGroundService, $id)
    {
        $requestTypes= $commonGroundService->getResource('//https://vtc.zaakonline.nl/requestTypes/'.$id);

        return ["requestTypes"=>$requestTypes];
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
