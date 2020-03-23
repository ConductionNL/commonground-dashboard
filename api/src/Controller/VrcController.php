<?php

// src/Controller/DashboardController.php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatorInterface;

use App\Service\CommonGroundService;

/**
 * Class DashboardController
 * @package App\Controller
 * @Route("/configuratie")
 */
class VrcController extends AbstractController
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
     * @Route("/requests")
     * @Template
     */
    public function requestsAction(Request $request, CommonGroundService $commonGroundService)
    {
    	
    	$variables = [];
    	$variables['title'] = 'Requests';
    	$variables['subtitle'] = 'Overzicht van de door de organisatie aangeboden producten en diensten';
    	$variables['groups'] = $commonGroundService->getResourceList('https://vrc.huwelijksplanner.online/requests')["hydra:member"];
    	
    	return $variables;
    }
    
    /**
     * @Route("/requests/{id}")
     * @Template
     */
    public function requestAction(Request $request, CommonGroundService $commonGroundService)
    {
    	
    	$variables = [];
    	$variables['title'] = 'Requests';
    	$variables['subtitle'] = 'Overzicht van de door de organisatie aangeboden producten en diensten';
    	$variables['groups'] = $commonGroundService->getResourceList('https://vrc.huwelijksplanner.online/requests/'.$id);
    	
    	return $variables;
    }
    
    /**
     * @Route("/submitters")
     * @Template
     */
    public function submittersAction(Request $request, CommonGroundService $commonGroundService)
    {
    	
    	$variables = [];
    	$variables['title'] = 'Submitters';
    	$variables['subtitle'] = 'Overzicht van de door de organisatie aangeboden producten en diensten';
    	$variables['groups'] = $commonGroundService->getResourceList('https://vrc.huwelijksplanner.online/submitters')["hydra:member"];
    	
    	return $variables;
    }
    
    /**
     * @Route("/submitters/{id}")
     * @Template
     */
    public function submitterAction(Request $request, CommonGroundService $commonGroundService)
    {
    	
    	$variables = [];
    	$variables['title'] = 'Submitters';
    	$variables['subtitle'] = 'Overzicht van de door de organisatie aangeboden producten en diensten';
    	$variables['groups'] = $commonGroundService->getResourceList('https://vrc.huwelijksplanner.online/submitters/'.$id);
    	
    	return $variables;
    }

}
