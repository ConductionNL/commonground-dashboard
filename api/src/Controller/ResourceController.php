<?php

// src/Controller/DashboardController.php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use App\Service\CommonGroundService;

/**
 * Class DashboardController
 * @package App\Controller
 * @Route("/resources")
 */
class ResourceController extends AbstractController
{

    /**
     * @Route("/")
     * @Template
     */
	public function indexAction(Request $request, EntityManagerInterface $em, CommonGroundService $commonGroundService)
    {
    	$components = $commonGroundService->getComponentList();
    	
    	foreach($components as $key=>$component){
    		//$components[$key] = $commonGroundService->getComponentHealth($key);
    		$components[$key] = $commonGroundService->getComponentResources($key);
    	}

    	return ["components"=>$components];
    }
}
