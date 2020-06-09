<?php

// src/Controller/DefaultController.php

namespace App\Controller;

use Conduction\CommonGroundBundle\Service\CommonGroundService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class BsController.
 *
 * @Route("/bs")
 */
class BsController extends AbstractController
{
    /**
     * @Route("/")
     * @Template
     */
    public function indexAction(TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('Berichten Service');
        $variables['subtitle'] = $translator->trans('the location catalogue holds al data concerning accomodations, places, changelogs and auditrails.');

        return $variables;
    }

    /**
     * @Route("/services")
     * @Template
     */
    public function servicesAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('services');
        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('services');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'bs', 'type'=>'services'])['hydra:member'];

        return $variables;
    }

    /**
     * @Route("/services/{id}")
     * @Template
     */
    public function serviceAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
        $variables = [];

        // Lets see if we need to create
        if ($id == 'new') {
            $variables['resource'] = ['@id' => null, 'id'=>'new', 'name'=>'new'];
        } else {
            $variables['resource'] = $commonGroundService->getResource(['component'=>'bs', 'type'=>'services', 'id'=> $id]);
        }

        // If it is a delete action we can stop right here
        if ($request->query->get('action') == 'delete') {
            $commonGroundService->deleteResource($variables['resource']);

            return $this->redirect($this->generateUrl('app_bs_services'));
        }

        $variables['title'] = $translator->trans('service');
        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('services');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'wrc', 'type'=>'organizations'])['hydra:member'];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])

            $variables['resource'] = $commonGroundService->saveResource($resource, (['component'=>'bs', 'type'=>'resource']));
        }

        return $variables;
    }

    /**
     * @Route("/messages")
     * @Template
     */
    public function messagesAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('messages');
        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('messages');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'bs', 'type'=>'messages'])['hydra:member'];

        return $variables;
    }

    /**
     * @Route("/messages/{id}")
     * @Template
     */
    public function messageAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
        $variables = [];

        // Lets see if we need to create
        if ($id == 'new') {
            $variables['resource'] = ['@id' => null, 'id'=>'new', 'name'=>'new'];
        } else {
            $variables['resource'] = $commonGroundService->getResource(['component'=>'bs', 'type'=>'messages', 'id'=> $id]);
        }

        // If it is a delete action we can stop right here
        if ($request->query->get('action') == 'delete') {
            $commonGroundService->deleteResource($variables['resource']);

            return $this->redirect($this->generateUrl('app_bs_messages'));
        }

        $variables['title'] = $translator->trans('message');
        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('messages');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'wrc', 'type'=>'organizations'])['hydra:member'];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])

            $variables['resource'] = $commonGroundService->saveResource($resource, (['component'=>'bs', 'type'=>'messages']));
        }

        return $variables;
    }
}
