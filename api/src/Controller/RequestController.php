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
 * @Route("/requests")
 */
class RequestController extends AbstractController
{

    /**
     * @Route("/")
     * @Template
     */
	public function indexAction(Request $request, EntityManagerInterface $em, CommonGroundService $commonGroundService)
    {
    	$requests = $commonGroundService->getResourceList('https://vrc.zaakonline.nl/requests');

    	return ["requests"=>$requests];
	}
	
	/**
	 * @Route("/edit/{id}")
	 * @Template
	 */
	public function editAction(Request $request, EntityManagerInterface $em, CommonGroundService $commonGroundService, $id)
	{
		$request= $commonGroundService->getResource('//https://vrc.zaakonline.nl/requests/'.$id);
		
		return ["request"=>$request];
	}
}
