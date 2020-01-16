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
 * @Route("/web-resource-catalogus")
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

    	return ["components"=>$components];
	}

	/**
	 * @Route("/templates")
	 * @Template
	 */
	public function templatesAction(Request $request, EntityManagerInterface $em, CommonGroundService $commonGroundService)
	{
		$templates = $commonGroundService->getResourceList('https://wrc.zaakonline.nl/templates');

		return ["templates"=>$templates];
	}

	/**
	 * @Route("/templates/{id}")
	 * @Template
	 */
	public function templateAction(Request $request, EntityManagerInterface $em, CommonGroundService $commonGroundService, $id)
	{
		$template = $commonGroundService->getResource('https://wrc.zaakonline.nl/templates/'.$id);

		//var_dump($template);
		
		if($request->getMethod() == "POST"){
			$template["name"] = $request->request->get('name');
			$template["description"] = $request->request->get('description');
			$template["templateEngine"] = $request->request->get('templateEngine');
			$template["content"] = $request->request->get('content');
			
			// Try to save
			if($template = $commonGroundService->updateResource($template, 'https://wrc.zaakonline.nl/templates/'.$id)){
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

		return ["template"=>$template];
	}

	/**
     * @Route("/config")
     */
	public function configAction(Request $request, EntityManagerInterface $em)
    {
        return [];
    }
}
