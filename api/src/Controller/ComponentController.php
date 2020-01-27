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
		$variables=[];
		
		$variables['component'] = $commonGroundService->getComponent($component);
		$variables['components'] = $commonGroundService->getComponentList();
		$variables['componentName'] = $component;
				
		return $variables;
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
		
		
		$loader = $this->get('twig')->getLoader();
		
		// Lets find out if we have one or more results
		if(array_key_exists ("hydra:member",$resources)){
			$variables['resources'] = $resources["hydra:member"];
			$variables['totalItems'] =$resources["hydra:totalItems"];
			$defaultTemplate = 'component/resourcelist.html.twig';
			
			//var_dump($component.'/'.ltrim($variables['resourceName'], '/').'/index.html.twig');
			
			// Lets try to find a specific template
			if ($loader->exists($component.'/'.ltrim($variables['resourceName'], '/').'/index.html.twig')) {
				$defaultTemplate = $component.'/'.ltrim($variables['resourceName'], '/').'/index.html.twig';
			}	
			
		}
		else{
			$variables['resource']= $resources;
			$variables['jsondump'] = json_encode($resources);
			$defaultTemplate = 'component/resource.html.twig';
									
			// Lets try to find a specific template
			if ($loader->exists($component.'/'.ltrim($variables['resourceName'], '/').'/edit.html.twig')) {
				$defaultTemplate = $component.'/'.ltrim($variables['resourceName'], '/').'/edit.html.twig';
			}	
			
			// Let see if the resource has a type that we need to take into acount
			if(array_key_exists("request_type", $variables['resource']) || array_key_exists("type", $variables['resource'])){
				// Get resource type as variable
				$type = $variables['resource']['request_type'];
				
				if(!$type){
					$type = $variables['resource']['type'];
				}
				
				// Temporary overrides
				if($type == 'http://vtc.zaakonline.nl/request_types/5b10c1d6-7121-4be2-b479-7523f1b625f1'){
					$type = 'huwelijk';
				}
				if($type == 'https://vtc.zaakonline.nl/request_types/9d76fb58-0711-4437-acc4-9f4d9d403cdf'){
					$type = 'verhuizen';
				}
				
				
												
				if ($loader->exists($component.'/'.ltrim($variables['resourceName'], '/').'/edit_'.$type.'.html.twig')) {
					$defaultTemplate = $component.'/'.ltrim($variables['resourceName'], '/').'/edit_'.$type.'.html.twig';
				}
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
				$this->addFlash('success','Resource '.$variables['resource']['@id'].' is bijgewerkt');
			}
			else{
				
				$this->addFlash('danger', 'Resource '.$variables['resource']['@id'].' kon niet worden bijgewerkt');
			}
		}
		
		// If we dont have a specific template we are going to return the default templates
		return $this->render($defaultTemplate, $variables); ;
	}

}
