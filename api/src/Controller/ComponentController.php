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
		$components = $commonGroundService->getComponentList();
		$component= $commonGroundService->getComponent($component);
		
		return ["component"=>$component];
	}
	
	/**
	 * @Route("/{component}/resource{resource}", requirements={"resource"=".+"})
	 * @Template
	 */
	public function resourceAction(Request $request, CommonGroundService $commonGroundService, $component, $resource)
	{
		$componentName = $component;
		$components = $commonGroundService->getComponentList();
		$component= $commonGroundService->getComponent($component);
		$resource = $commonGroundService->getResource($component['href'].$resource);
		
		var_dump('App:'.$componentName.':'.rtrim(ltrim($resource, '/'), 's').'.html.twig');
		
		$variables = ["component"=>$component,"resource"=>$resource,"jsondump"=> json_encode($resource)];
		// Lets try to find a specific template
		if ($this->get('twig')->getLoader()->exists('App:'.$componentName.':'.rtrim(ltrim($resource, '/'), 's').'.html.twig')) {
			var_dump('found it');
			return $this->render('App:'.$componentName.':'.rtrim(ltrim($resource, '/'), 's').'.html.twig', $variables);
		}
		
		// If we dont have a specific template we are going to return the default template
		return $variables;
	}
	
	/**
	 * @Route("/{component}/index{resourcetype}", requirements={"resourcetype"=".+"})
	 * @Template
	 */
	public function resourcelistAction(Request $request, CommonGroundService $commonGroundService, $component, $resourcetype)
	{
		$componentName = $component;
		$components = $commonGroundService->getComponentList();
		$component= $commonGroundService->getComponent($component);
		$resources = $commonGroundService->getResourceList($component['href'].$resourcetype);
		$resourceContext = $commonGroundService->getResourceContext($component,$resourcetype);
		
		var_dump('App:'.$componentName.':'.ltrim($resourcetype, '/').'.html.twig');
		
		$variables = ["component"=>$component,"resources"=>$resources,"resourceContext"=>$resourceContext,"components"=>$components];
		// Lets try to find a specific template
		if ($this->get('twig')->getLoader()->exists('App:'.$componentName.':'.ltrim($resourcetype, '/').'.html.twig')) {
			var_dump('found it');
			return $this->render('App:'.$componentName.':'.ltrim($resourcetype, '/').'.html.twig', $variables);
		}
		
		// If we dont have a specific template we are going to return the default template
		return $variables;
	}

}
