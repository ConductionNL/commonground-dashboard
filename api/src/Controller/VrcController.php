<?php

// src/Controller/DashboardController.php

namespace App\Controller;

use Conduction\CommonGroundBundle\Service\CommonGroundService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatorInterface;


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
     */
    public function requestsAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('requests');
        $variables['subtitle'] = $translator->trans('all') . ' ' . $translator->trans('requests');

        $variables['requestType'] = $request->query->get('requestType');


        if(isset($variables['requestType'])){
            $variables['requestType'] = $commonGroundService->getResource(['component'=>'vtc','type'=>'request_types','id'=>$variables['requestType']]);
            $variables['subtitle'] = "alle ".$variables['requestType']['name'];
            $variables['resources'] = $commonGroundService->getResourceList(['component'=>'vrc','type'=>'requests'],['requestType'=> $variables['requestType']['@id']])["hydra:member"];
        }
        else{
            $variables['resources'] = $commonGroundService->getResourceList(['component'=>'vrc','type'=>'requests'])["hydra:member"];
        }

        /* If we have specific view for this request type use that instead */
        if (key_exists('requestType', $variables) && $this->get('twig')->getLoader()->exists('vrc/requests_templates/' . $variables['requestType']['id'] . '.html.twig')) {
            return $this->render('vrc/requests_templates/' . $variables['requestType']['id'] . '.html.twig', $variables);
        } else {
            return $this->render('vrc/requests.html.twig', $variables);
        }
        return $this->render('vrc/requests.html.twig', $variables);
    }

    /**
     * @Route("/requests/{id}")
     */
    public function requestAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {

        // If it is a delete action we can stop right here
        if ($request->query->get('action') == 'delete') {
            $commonGroundService->deleteResource(null, ['component' => 'vrc', 'type' => 'requests', 'id' => $id]);
            return $this->redirect($this->generateUrl('app_vrc_requests'));
        }

        $variables = [];

        // Lets see if we need to create
        if ($id == 'new') {
            $variables['resource'] = ['@id' => null, 'name' => 'new', 'id' => 'new'];
        } else {
            $variables['resource'] = $commonGroundService->getResource(['component'=>'vrc','type'=>'requests','id'=>$id]);
            $variables['changeLog'] = $commonGroundService->getResourceList($variables['resource']['@id'].'/change_log');
            $variables['auditTrail'] = $commonGroundService->getResourceList($variables['resource']['@id'].'/audit_trail');
            $variables['submitters'] = $commonGroundService->getResource(['component'=>'vrc','type'=>'requests','id'=>$id]);

            if(array_key_exists('requestType',$variables['resource'])){
            $variables['requestType'] = $commonGroundService->getResource($variables['resource']['requestType']);
            }

            /*
            $variables['resource'] = $commonGroundService->getResource('https://vrc.huwelijksplanner.online/submitters/' . $id);
            $variables['groups'] = $commonGroundService->getResourceList('https://vrc.huwelijksplanner.online/submitters/'.$id);
            $variables['changeLog'] = $commonGroundService->getResourceList('https://vrc.huwelijksplanner.online/submitters/'.$id.'/change_log')["hydra:member"];
            $variables['auditTrail'] = $commonGroundService->getResourceList('https://vrc.huwelijksplanner.online/submitters/'.$id.'/audit_trail')["hydra:member"];
            */
        }

        if(isset($variables['requestType'])){
            $variables['title'] = $translator->trans($variables['requestType']['name']);
            $variables['subtitle'] = $translator->trans('save or create a').' '.$variables['requestType']['name'].' '.$translator->trans('request');
        }
        else{
            $variables['title'] = $translator->trans('request');
            $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('request');
        }


        $variables['requestTypes'] = $commonGroundService->getResourceList(['component' => 'vtc', 'type' => 'request_types'])["hydra:member"];
        $variables['organizations'] = $commonGroundService->getResourceList(['component' => 'wrc', 'type' => 'organizations'])["hydra:member"];

        $variables['casetypes'] = $commonGroundService->getResourceList(['component' => 'ztc', 'type' => 'zaaktypen'])["results"];

        if (array_key_exists('zaaktype', $variables['resource'])) {
            $variables['casestatuses'] = $commonGroundService->getResourceList(['component' => 'ztc', 'type' => 'statustypen'], ['zaaktype' => $variables['resource']['zaaktype']])["results"];
        }

        if (array_key_exists('requestType', $variables['resource'])) {
            $variables['requestType'] = $commonGroundService->getResource($variables['resource']['requestType']);
        }

        // Lets see if there is a post to procces
        if ($request->query->get('action') == 'save') {

            //Check requestType
            if ($variables['requestType']['id'] == "cdd7e88b-1890-425d-a158-7f9ec92c9508") {

                $resource['properties'] = $request->request->all();
                $resource['properties'] = $variables['huwelijk']['properties'];

                if ($request->request->has('name')) {
                    $resource['properties']['gegevens']['name'] = $request->request->get('name');
                }

                if ($request->request->has('familyName')) {
                    $resource['properties']['gegevens']['familyName'] = $request->request->get('familyName');
                }

                if ($request->request->has('email')) {
                    $resource['properties']['gegevens']['email'] = $request->request->get('email');
                }

                if ($request->request->has('telefoon')) {
                    $resource['properties']['gegevens']['telephone'] = $request->request->get('telefoon');
                }

                $variables['resource'] = $commonGroundService->saveResource($resource, 'https://vrc.huwelijksplanner.online/requests/');
            }


//            if(array_key_exists('submitter', $resource)){
//                $submitter = $resource['submitter'];
//                $submitter['request'] = $resource['@id'];
//
//                // The resource action section
//                if(key_exists("@id",$submitter) && key_exists("action",$submitter)){
//                    // The delete action
//                    if($submitter['action'] == 'delete'){
//                        $commonGroundService->deleteResource($submitter);
//                        return $this->redirect($this->generateUrl('app_vrc_request',['id'=>$id]));
//                    }
//                }
//
//                $configuration = $commonGroundService->saveResource($submitter, ['component'=>'wrc','type'=>'configurations']);
//            }

//            if(array_key_exists('property', $resource)){
//                $property= $resource['property'];
//                $property['property'] = $resource['@id'];
//
//                $configuration = $commonGroundService->saveResource($submitter, ['component'=>'wrc','type'=>'configurations']);
//            }

//            /* @todo dit is echt lekker old skool*/
//            // We might also want to create a zaakObject resource
//            if(key_exists('zaak', $resource)){
//                $zaak = $resource['zaak'];
//
//                if(!key_exists('startdatum', $zaak)){
//                    $zaak['startdatum'] = date('Y-m-d');
//                }
//
//                $zaak = $zgwService->saveResource($zaak,'https://openzaak.utrechtproeftuin.nl/zaken/api/v1/zaken', false);
//
//                if(!key_exists('cases', $resource)){
//                    $resource['cases'] = $variables['resource']['cases'];
//                }
//
//                $resource['cases'][] = $zaak['url'];
//            }
        }




        /* If we have specific view for this request type use that instead */
        if(key_exists('requestType',  $variables['resource']) && $this->get('twig')->getLoader()->exists('vrc/request_templates/'.$variables['requestType']['id'].'.html.twig')){
            return $this->render('vrc/request_templates/'.$variables['requestType']['id'].'.html.twig', $variables);
        }
        else{
            return $this->render('vrc/request.html.twig', $variables);
        }
    }
}
