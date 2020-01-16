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
 * @Route("/component")
 */
class ComponentController extends AbstractController
{

    /**
     * @Route("/")
     * @Template
     */
	public function indexAction(Request $request, CommonGroundService $commonGroundService)
    {
    	$components = $commonGroundService->getComponentList();

    	return ["components"=>$components];
	}
	
	/**
	 * @Route("/{component}")
	 * @Template
	 */
	public function componentAction(Request $request, CommonGroundService $commonGroundService, $component)
	{
		$component= $commonGroundService->getComponent($component);
		
		return ["component"=>$component];
	}
	
	/**
	 * @Route("/{component}/resource/{resource}", requirements={"resource"=".+"})
	 * @Template
	 */
	public function resourceAction(Request $request, CommonGroundService $commonGroundService, $component, $resource)
	{
		$component= $commonGroundService->getComponent($component);
		$resource = $commonGroundService->getResource($component['href'].$resource);
		
		return ["resource"=>$resource,"jsondump"=> json_encode($resource)];
	}
	
	/**
	 * @Route("/{component}/{resourcetype}", requirements={"resourcetype"=".+"})
	 * @Template
	 */
	public function resourcelistAction(Request $request, CommonGroundService $commonGroundService, $component, $resourcetype)
	{
		$component= $commonGroundService->getComponent($component);
		$resource = $commonGroundService->getResource($component['href'].$resourcetype);
		
		return ["resources"=>$resources];
	}

}
