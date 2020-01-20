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
	 * @Route("/component/{component}{resourcetype}", requirements={"resourcetype"=".+"})
	 * @Template
	 */
	public function resourceAction(Request $request, CommonGroundService $commonGroundService, $component, $resourcetype, $id = null)
	{
		
		$variables=[];
		
		$path_parts = pathinfo($resourcetype);
		$variables['resourceName'] = $path_parts['dirname'];
		$variables['id'] = $path_parts['basename'];
		
		$variables['component'] = $commonGroundService->getComponent($component);
		$variables['componentName'] = $component;
		$variables['components'] = $commonGroundService->getComponentList();
		$variables['resourceContext'] = $commonGroundService->getResourceContext($variables['component'],$resourcetype);
		$variables['resourceType'] = $resourcetype;
		
		
		$variables['resources'] = [];
		
		var_dump($variables['id']);
		
		// Lets get all the stuff
		if(is_string($variables['id']) && (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $variables['id']) == 1)){			
			$variables['resourceName'] = rtrim($path_parts['dirname'], '/');
			$variables['resource'] = $commonGroundService->getResource($variables['component']['href'].$resourcetype);
			$variables['jsondump'] = json_encode($variables['resource']);
			$defaultTemplate = 'component/resource.html.twig';		
		}
		else{
			$variables['resourceName'] = $resourcetype;
			$variables['resources'] = $commonGroundService->getResourceList($variables['component']['href'].$resourcetype); 
			$defaultTemplate = 'component/index.html.twig';
		}
		
		// Let proces any post requests		
		if($request->getMethod() == "POST"){
			
			foreach($request->request->getAll() as $property ){
				var_dump($property);	
			}
			
			// Try to save
			if($variables['resource'] = $commonGroundService->updateResource($variables['resource'], $variables['component']['href'].$resourcetype.$variables['id'])){
				//seces
				var_dump('succes');
				die;
			}
			else{
				// error
				var_dump('error');
				die;
			}
		}
		var_dump($component,$variables['resourceName'],$id);
		// Lets try to find a specific template
		if ($template = $commonGroundService->getTemplate($component,$variables['resourceName'],$variables)){
			return $template;
		}
		
		die;
		// If we dont have a specific template we are going to return the default templates
		return $variables;
	}

}
