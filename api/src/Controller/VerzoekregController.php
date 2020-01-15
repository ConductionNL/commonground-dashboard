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
 * Class VerzoekregController
 * @package App\Controller
 * @Route("/verzoek-registratie-component")
 */
class VerzoekregController extends AbstractController
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
     * @Route("/archives")
     * @Template
     */
    public function archivesAction(Request $request, EntityManagerInterface $em, CommonGroundService $commonGroundService)
    {
        $archives = $commonGroundService->getResourceList('https://vrc.zaakonline.nl/archives');

        return ["archives"=>$archives];
    }

    /**
     * @Route("/organizations")
     * @Template
     */
    public function organizationsAction(Request $request, EntityManagerInterface $em, CommonGroundService $commonGroundService)
    {
        $organizations = $commonGroundService->getResourceList('https://vrc.zaakonline.nl/organizations');

        return ["organizations"=>$organizations];
    }

    /**
     * @Route("/requests")
     * @Template
     */
    public function requestsAction(Request $request, EntityManagerInterface $em, CommonGroundService $commonGroundService)
    {
        $requests = $commonGroundService->getResourceList('https://vrc.zaakonline.nl/requests');

        return ["requests"=>$requests];
    }

    /**
     * @Route("/request-cases")
     * @Template
     */
    public function requestCasesAction(Request $request, EntityManagerInterface $em, CommonGroundService $commonGroundService)
    {
        $requestCases = $commonGroundService->getResourceList('https://vrc.zaakonline.nl/requestCases');

        return ["requestCases"=>$requestCases];
    }


    /**
     * @Route("/submitters")
     * @Template
     */
    public function submittersAction(Request $request, EntityManagerInterface $em, CommonGroundService $commonGroundService)
    {
        $submitters = $commonGroundService->getResourceList('https://vrc.zaakonline.nl/submitters');

        return ["submitters"=>$submitters];
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

    /**
     * @Route("/config")
     * @Template
     */
    public function configAction(Request $request, EntityManagerInterface $em)
    {
        return [];
    }
}
