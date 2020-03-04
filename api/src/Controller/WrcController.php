<?php

// src/Controller/DashboardController.php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use App\Service\CommonGroundService;

/**
 * Class DashboardController
 * @package App\Controller
 * @Route("/wrc")
 */
class WrcController extends AbstractController
{
    /**
     * @Route("/")
     * @Template
     */
	public function indexAction(Request $request, CommonGroundService $commonGroundService)
    {
    	return [];
    }    
    
    /**
     * @Route("/templates")
     * @Template
     */
    public function templatesAction(Request $request, CommonGroundService $commonGroundService)
    {
    	$templates = $commonGroundService->getResourceList('https://wrc.huwelijksplanner.online/templates')["hydra:member"];
    	    	
    	return ["templates"=>$templates];
    }    
    
    /**
     * @Route("/templates/{id}")
     * @Template
     */
    public function templateAction(Request $request, CommonGroundService $commonGroundService, $id)
    {    	
    	$template = $commonGroundService->getResource('https://wrc.huwelijksplanner.online/templates/'.$id);
    	    	
    	// Kijken of het formulier is getriggerd
    	if ($request->isMethod('POST')) {    		
    		// Iets met validatie
    		$template['title'] =  $request->request->get('title');
    		$template['content'] =  $request->request->get('content');
    		
    		if($template = $commonGroundService->updateResource($template)){
    			// gelukt
    			
    			// throw flashbang dat opslaan is gelukt of gefaald
    		}
    		else{
    			//niet gelukt
    		} 		
    	}
    	
    	return ["template"=>$template];
    }
    
}
