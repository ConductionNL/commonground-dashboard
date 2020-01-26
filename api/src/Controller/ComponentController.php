<?php

// src/Controller/DashboardController.php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Ramsey\Uuid\Uuid;


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
		$components = $commonGroundService->getComponentList();
		$component= $commonGroundService->getComponent($component);
		
		return ["component"=>$component];
	}
	
	/**
	 * @Route("/{component}{resourcetype}", requirements={"resourcetype"=".+"})
	 */
	public function resourceAction(Request $request, CommonGroundService $commonGroundService, $component, $resourcetype, $id = null)
	{
		
		$variables=[];
		
		$path_parts = pathinfo($resourcetype);
		$variables['resourceName'] = $path_parts['dirname'];
		$variables['id'] = $path_parts['basename'];
		
		if($path_parts['dirname']== "/"){
			$variables['resourceName'] = $path_parts['basename'];
		}
				
		$variables['component'] = $commonGroundService->getComponent($component);
		$variables['componentName'] = $component;
		
		
		$variables['components'] = $commonGroundService->getComponentList();
		//$variables['resourceContext'] = $commonGroundService->getResourceContext($variables['component'],$resourcetype);
		$variables['resourceType'] = $resourcetype;		
		
		$resources = $commonGroundService->getResource($variables['component']['href'].$resourcetype);
		
		// Lets find out if we have one or more results
		if(array_key_exists ("hydra:member",$resources)){
			$variables['resources'] = $resources["hydra:member"];
			$variables['totalItems'] =$resources["hydra:totalItems"];
			$defaultTemplate = 'component/resourcelist.html.twig';
			
			//var_dump($component.'/'.ltrim($variables['resourceName'], '/').'/index.html.twig');
			
			// Lets try to find a specific template
			$loader = $this->get('twig')->getLoader();
			if ($loader->exists($component.'/'.ltrim($variables['resourceName'], '/').'/index.html.twig')) {
				$defaultTemplate = $component.'/'.ltrim($variables['resourceName'], '/').'/index.html.twig';
			}	
			
		}
		else{
			$variables['resource']= $resources;
			$variables['jsondump'] = json_encode($resources);
			$defaultTemplate = 'component/resource.html.twig';
									
			// Lets try to find a specific template
			$loader = $this->get('twig')->getLoader();
			if ($loader->exists($component.'/'.ltrim($variables['resourceName'], '/').'/edit.html.twig')) {
				$defaultTemplate = $component.'/'.ltrim($variables['resourceName'], '/').'/edit.html.twig';
			}	
			
		}
		
		// Let proces any post requests
		if($request->getMethod() == "POST"){
			
			/*
			foreach($request->request->all() as $key => $property ){
				$variables['resource'][$key] = $property;
				
			}
			*/
			//var_dump(json_encode($variables['resource']));
			
			// Try to save
			if($variables['resource'] = $commonGroundService->updateResource($request->request->all(), $variables['component']['href'].$resourcetype)){
				//seces
				//var_dump(json_encode($request->request->all()));
				var_dump('succes');
				//var_dump($variables['resource']);
				//die;
			}
			else{
				// error
				var_dump('error');
				//die;
			}
		}
		
		// If we dont have a specific template we are going to return the default templates
		return $this->render($defaultTemplate, $variables); ;
	}

}
