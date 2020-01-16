<?php

// src/Controller/DefaultController.php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use App\Service\CommonGroundService;

class DefaultController extends AbstractController
{
    /**
     * @Route("/")
     * @Template
     */
	public function indexAction(Request $request, CommonGroundService$commonGroundService)
    {
    	$requests= $commonGroundService->getResourceList('https://vrc.zaakonline.nl/requests');
    	$components= $commonGroundService->getComponentList();
    	
    	return ["requests"=>$requests, "components"=>$components];
    }
}
