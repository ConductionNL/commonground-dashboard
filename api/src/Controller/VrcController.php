<?php

// src/Controller/DashboardController.php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatorInterface;

use App\Service\CommonGroundService;
use App\Service\ZgwService;

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
	public function indexAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        return [];
    }

    /**
     * @Route("/requests")
     * @Template
     */
    public function requestsAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
    	$variables = [];
    	$variables['title'] = $translator->trans('requests');
    	$variables['subtitle'] = $translator->trans('all').' '.$translator->trans('requests');
    	$variables['resources'] = $commonGroundService->getResourceList('https://vrc.huwelijksplanner.online/requests')["hydra:member"];

    	return $variables;
    }

    /**
     * @Route("/requests/{id}")
     * @Template
     */
    public function requestAction(Request $request, CommonGroundService $commonGroundService, ZgwService $zgwService, TranslatorInterface $translator, $id)
    {
    	$variables = [];

    	// Lets see if we need to create
    	if($id == 'new'){
    		$variables['resource'] = ['@id' => null,'id'=>'new'];
    	}
    	else{
    		$variables['resource'] = $commonGroundService->getResource('https://vrc.huwelijksplanner.online/requests/'.$id);
            $variables['changeLog'] = $commonGroundService->getResourceList('https://vrc.huwelijksplanner.online/requests/'.$id.'/change_log');
            $variables['auditTrail'] = $commonGroundService->getResourceList('https://vrc.huwelijksplanner.online/requests/'.$id.'/audit_trail');
    	}

    	// If it is a delete action we can stop right here
    	if($request->query->get('action') == 'delete'){
    		$commonGroundService->deleteResource($variables['resource']);
    		return $this->redirect($this->generateUrl('app_vrc_requests'));
    	}

    	$variables['title'] = $translator->trans('request');
    	$variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('request');
    	$variables['requestTypes'] = $commonGroundService->getResourceList('https://vtc.huwelijksplanner.online/request_types')["hydra:member"];
    	$variables['organizations'] = $commonGroundService->getResourceList('https://wrc.huwelijksplanner.online/organizations')["hydra:member"];
    	$variables['casetypes'] = $zgwService->getResourceList('https://openzaak.utrechtproeftuin.nl/catalogi/api/v1/zaaktypen')["results"];

    	// Case statuses can only be loaded if the case type is known
    	if(array_key_exists ('zaaktype', $variables['resource'])){
    		$variables['casestatuses'] = $zgwService->getResourceList('https://openzaak.utrechtproeftuin.nl/catalogi/api/v1/statustypen',['zaaktype'=>$variables['resource']['zaaktype']])["results"];
    	}

    	// Lets see if there is a post to procces
    	if ($request->isMethod('POST')) {

    		// Passing the variables to the resource
    		$resource = $request->request->all();
    		$resource['@id'] = $variables['resource']['@id'];
    		$resource['id'] = $variables['resource']['id'];

    		// If there are any sub data sources the need to be removed below in order to save the resource
    		// unset($resource['somedatasource'])

    		$variables['resource'] = $commonGroundService->saveResource($resource,'https://vrc.huwelijksplanner.online/requests/');
    	}
    	return $variables;
    }

    /**
     * @Route("/submitters")
     * @Template
     */
    public function submittersAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
    	$variables = [];
    	$variables['title'] = $translator->trans('submitters');
    	$variables['subtitle'] = $translator->trans('all').' '.$translator->trans('submitters');
    	$variables['resources'] = $commonGroundService->getResourceList('https://vrc.huwelijksplanner.online/submitters')["hydra:member"];

    	return $variables;
    }

    /**
     * @Route("/submitters/{id}")
     * @Template
     */
    public function submitterAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {

    	$variables = [];

        // Lets see if we need to create
        if($id == 'new'){
            $variables['resource'] = ['@id' => null,'id'=>'new'];
        }
        else{
            $variables['groups'] = $commonGroundService->getResourceList('https://vrc.huwelijksplanner.online/submitters/'.$id);
            $variables['changeLog'] = $commonGroundService->getResourceList('https://vrc.huwelijksplanner.online/submitters/'.$id.'/change_log')["hydra:member"];
            $variables['auditTrail'] = $commonGroundService->getResourceList('https://vrc.huwelijksplanner.online/submitters/'.$id.'/audit_trail')["hydra:member"];
        }

        // If it is a delete action we can stop right here
        if($request->query->get('action') == 'delete'){
            $commonGroundService->deleteResource($variables['resource']);
            return $this->redirect($this->generateUrl('app_vrc_submitters'));
        }

        $variables['title'] = 'Submitters';
    	$variables['subtitle'] = 'Overzicht van de door de organisatie aangeboden producten en diensten';

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])

            $variables['resource'] = $commonGroundService->saveResource($resource,'https://vrc.huwelijksplanner.online/submitters/');
        }

    	return $variables;
    }

}
