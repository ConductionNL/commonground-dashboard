<?php

// src/Controller/DashboardController.php

namespace App\Controller;

use App\Service\RequestService;
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
    public function requestAction(Request $request, CommonGroundService $commonGroundService, RequestService $requestService, ZgwService $zgwService, TranslatorInterface $translator, $id)
    {

        // If it is a delete action we can stop right here
        if ($request->query->get('action') == 'delete') {
            $commonGroundService->deleteResource(null, ['component' => 'vrc', 'type' => 'requests', 'id' => $id]);
            return $this->redirect($this->generateUrl('app_vrc_requests'));
        }

        $variables = [];
        $variables['employees'] = $commonGroundService->getResourceList(['component' => 'mrc', 'type' => 'employees'])["hydra:member"];
        // Lets see if we need to create

        if ($id == 'new') {
            if(!$request->isMethod('POST')){
                $variables['resource'] = ['@id' => null, 'name' => 'new', 'id' => 'new', 'reference' => 'new request'];
            }
        } else {
            $variables['resource'] = $commonGroundService->getResource(['component'=>'vrc','type'=>'requests','id'=>$id], [],true);
            $variables['changeLog'] = $commonGroundService->getResourceList($variables['resource']['@id'].'/change_log');
            $variables['auditTrail'] = $commonGroundService->getResourceList($variables['resource']['@id'].'/audit_trail');
            //$variables['submitters'] = $commonGroundService->getResourceList(['component'=>'vrc','type'=>'submitters'],['request'=> $variables['resource']['@id']])["hydra:member"];
            $variables['roles'] = $commonGroundService->getResourceList(['component' => 'vrc', 'type' => 'roles'])["hydra:member"];
            $variables['tasks'] = []; //$commonGroundService->getResourceList(['component' => 'tc', 'type' => 'tasks'])["hydra:member"];
            $variables['messages'] = $commonGroundService->getResourceList(['component' => 'bs', 'type' => 'messages'])["hydra:member"];
            $variables['memos'] = []; //$commonGroundService->getResourceList(['component' => 'memo', 'type' => 'memo'])["hydra:member"];
            $variables['queues'] = []; //$commonGroundService->getResourceList(['component' => 'qc', 'type' => 'memo'])["hydra:member"];

            if(array_key_exists('requestType',$variables['resource'])){
            $variables['requestType'] = $commonGroundService->getResource($variables['resource']['requestType']);
            }

            if (array_key_exists('zaaktype', $variables['resource'])) {
                $variables['casestatuses'] = $commonGroundService->getResourceList(['component' => 'ztc', 'type' => 'statustypen'], ['zaaktype' => $variables['resource']['zaaktype']])["results"];
            }

            if (array_key_exists('requestType', $variables['resource'])) {
                $variables['requestType'] = $commonGroundService->getResource($variables['resource']['requestType']);


                /* @todo wat doet dit hier ? */
                if ($variables['requestType']['id'] == "5b10c1d6-7121-4be2-b479-7523f1b625f1") {
                    $variables['requestStatus'] = $requestService->checkRequestStatus($variables['resource'], $variables['requestType']);
                }
            }
            /*
            $variables['resource'] = $commonGroundService->getResource('https://vrc.huwelijksplanner.online/submitters/' . $id);
            $variables['groups'] = $commonGroundService->getResourceList('https://vrc.huwelijksplanner.online/submitters/'.$id);
            $variables['changeLog'] = $commonGroundService->getResourceList('https://vrc.huwelijksplanner.online/submitters/'.$id.'/change_log')["hydra:member"];
            $variables['auditTrail'] = $commonGroundService->getResourceList('https://vrc.huwelijksplanner.online/submitters/'.$id.'/audit_trail')["hydra:member"];
            */
        }

        $variables['requestTypes'] = $commonGroundService->getResourceList(['component' => 'vtc', 'type' => 'request_types'])["hydra:member"];
        $variables['organizations'] = $commonGroundService->getResourceList(['component' => 'wrc', 'type' => 'organizations'])["hydra:member"];

        $variables['casetypes'] = $commonGroundService->getResourceList(['component' => 'ztc', 'type' => 'zaaktypen'])["results"];

        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            // if we have a resource we want to use that id
            if(array_key_exists('resource', $variables)){
                $resource['@id'] = $variables['resource']['@id'];
                $resource['id'] = $variables['resource']['id'];
            }

            // Fix for properties not being nullabe @todo long term fix should be implemented
            if(!array_key_exists('properties', $resource)){
                $resource['properties'] = [];
            }

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])

            $variables['resource'] = $commonGroundService->saveResource($resource,(['component'=>'vrc','type'=>'requests']));

            /* @to this redirect is a hotfix */
            if(array_key_exists('id', $variables['resource'])){
                return $this->redirect($this->generateUrl('app_vrc_request', ["id" =>  $variables['resource']['id']]));
            }
        }




        /* If we have specific view for this request type use that instead */
        if(key_exists('requestType',  $variables['resource']) && $this->get('twig')->getLoader()->exists('vrc/request_templates/'.$variables['requestType']['id'].'.html.twig')){
            return $this->render('vrc/request_templates/'.$variables['requestType']['id'].'.html.twig', $variables);
        }
        else{
            return $this->render('vrc/request.html.twig', $variables);
        }
    }

    /**
     * @Route("/labels")
     * @Template
     */
    public function labelsAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('labels');
        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('labels');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'vrc','type'=>'labels'])["hydra:member"];
        return $variables;
    }

    /**
     * @Route("/labels/{id}")
     * @Template
     */
    public function labelAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {

        $variables = [];

        // Lets see if we need to create
        if($id == 'new'){
            $variables['resource'] = ['@id' => null,'id'=>'new','name'=>'label'];
        }
        else{
            $variables['resource'] = $commonGroundService->getResource(['component'=>'vrc','type'=>'labels','id'=> $id]);        }

        // If it is a delete action we can stop right here
        if($request->query->get('action') == 'delete'){
            $commonGroundService->deleteResource($variables['resource']);
            return $this->redirect($this->generateUrl('app_vrc_labels'));
        }

        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'vrc','type'=>'organizations'])["hydra:member"];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])


            $variables['resource'] = $commonGroundService->saveResource($resource,(['component'=>'vrc','type'=>'labels','id'=>$id]));
        }


        return $variables;
    }

    /**
     * @Route("/roles")
     * @Template
     */
    public function rolesAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('roles');
        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('roles');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'vrc','type'=>'roles'])["hydra:member"];
        return $variables;
    }

    /**
     * @Route("/roles/{id}")
     * @Template
     */
    public function RoleAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {

        $variables = [];

        // Lets see if we need to create
        if($id == 'new'){
            $variables['resource'] = ['@id' => null,'id'=>'new'];
        }
        else{
            $variables['resource'] = $commonGroundService->getResource(['component'=>'vrc','type'=>'roles','id'=> $id]);        }

        // If it is a delete action we can stop right here
        if($request->query->get('action') == 'delete'){
            $commonGroundService->deleteResource($variables['resource']);
            return $this->redirect($this->generateUrl('app_vrc_roles'));
        }


        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'vrc','type'=>'organizations'])["hydra:member"];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])


            $variables['resource'] = $commonGroundService->saveResource($resource,(['component'=>'vrc','type'=>'roles','id'=>$id]));
        }


        return $variables;
    }
}
