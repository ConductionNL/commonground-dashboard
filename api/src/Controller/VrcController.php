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
 * @Route("/vrc")
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
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'vrc','type'=>'requests'])["hydra:member"];

    	return $variables;
    }

    /**
     * @Route("/requests/{id}")
     */
    public function requestAction(Request $request, CommonGroundService $commonGroundService, ZgwService $zgwService, TranslatorInterface $translator, $id)
    {

        // If it is a delete action we can stop right here
        if($request->query->get('action') == 'delete'){
            $commonGroundService->deleteResource(null,['component'=>'vrc','type'=>'requests','id'=>$id]);
            return $this->redirect($this->generateUrl('app_vrc_requests'));
        }

        $variables = [];

        // Lets see if we need to create
        if($id == 'new'){
            $variables['resource'] = ['@id' => null,'name'=>'new','id'=>'new'];
        }
        else{
            $variables['resource'] = $commonGroundService->getResource(['component'=>'vrc','type'=>'requests','id'=>$id]);
            $variables['changeLog'] = $commonGroundService->getResource($variables['@id'].'/change_log');
            $variables['auditTrail'] = $commonGroundService->getResource($variables['@id'].'/audit_trail');
        }

    	$variables['title'] = $translator->trans('request');
    	$variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('request');


        $variables['requestTypes'] = $commonGroundService->getResourceList(['component'=>'vtc','type'=>'request_types'])["hydra:member"];
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'wrc','type'=>'organizations'])["hydra:member"];
    	/*@todo dit is nog veel te maga concreet*/
    	$variables['casetypes'] = $zgwService->getResourceList('https://openzaak.utrechtproeftuin.nl/catalogi/api/v1/zaaktypen')["results"];

    	if(array_key_exists ('zaaktype', $variables['resource'])){
            /*@todo dit is nog veel te maga concreet*/
    		$variables['casestatuses'] = $zgwService->getResourceList('https://openzaak.utrechtproeftuin.nl/catalogi/api/v1/statustypen',['zaaktype'=>$variables['resource']['zaaktype']])["results"];
    	}

    	// Lets see if there is a post to procces
    	if ($request->isMethod('POST')) {

    		// Passing the variables to the resource
    		$resource = $request->request->all();
    		$resource['@id'] = $variables['resource']['@id'];
    		$resource['id'] = $variables['resource']['id'];

            if(array_key_exists('submitter', $resource)){
                $submitter = $resource['submitter'];
                $submitter['request'] = $resource['@id'];

                // The resource action section
                if(key_exists("@id",$submitter) && key_exists("action",$submitter)){
                    // The delete action
                    if($submitter['action'] == 'delete'){
                        $commonGroundService->deleteResource($submitter);
                        return $this->redirect($this->generateUrl('app_vrc_request',['id'=>$id]));
                    }
                }

                $configuration = $commonGroundService->saveResource($submitter, ['component'=>'wrc','type'=>'configurations']);
            }

            if(array_key_exists('property', $resource)){
                $property= $resource['property'];
                $property['property'] = $resource['@id'];

                $configuration = $commonGroundService->saveResource($submitter, ['component'=>'wrc','type'=>'configurations']);
            }

            /* @todo dit is echt lekker old skool*/
            // We might also want to create a zaakObject resource
            if(key_exists('zaak', $resource)){
                $zaak = $resource['zaak'];

                if(!key_exists('startdatum', $zaak)){
                    $zaak['startdatum'] = date('Y-m-d');
                }

                $zaak = $zgwService->saveResource($zaak,'https://openzaak.utrechtproeftuin.nl/zaken/api/v1/zaken', false);

                if(!key_exists('cases', $resource)){
                    $resource['cases'] = $variables['resource']['cases'];
                }

                $resource['cases'][] = $zaak['url'];
            }
    		$variables['resource'] = $commonGroundService->saveResource($resource,'https://vrc.huwelijksplanner.online/requests/');
    	}

    	/* If we have specif view for this request type use that instead */
        if(key_exists('requestType', $resource) && $this->get('twig')->getLoader()->exists('vrc/request_templates/'.$resource['requestType'].'.html.twig')){
            $this->render('vrc/request_templates/'.$resource['requestType'].'.html.twig', $variables);
        }
        else{
            $this->render('vrc/request.html.twig', $variables);
        }
    }


}
