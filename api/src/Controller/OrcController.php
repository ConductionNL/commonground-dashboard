<?php

// src/Controller/DefaultController.php

namespace App\Controller;

use Conduction\CommonGroundBundle\Service\CommonGroundService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class PdcController.
 *
 * @Route("/orc")
 */
class OrcController extends AbstractController
{
    /**
     * @Route("/")
     * @Template
     */
    public function indexAction(TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('Deployments and Enviroments');
        $variables['subtitle'] = $translator->trans('this dashboards alows the administations of kubernetes clusters, enviroment and components');

        return $variables;
    }

    /**
     * @Route("/orders")
     * @Template
     */
    public function ordersAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('orders');
        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('orders');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'orc', 'type'=>'orders'])['hydra:member'];

        return $variables;
    }

    /**
     * @Route("/orders/{id}")
     * @Template
     */
    public function orderAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
        $variables = [];

        // Lets see if we need to create
        if ($id == 'new') {
            $variables['resource'] = ['@id' => null, 'id'=>'new', 'name'=>'new'];
        } else {
            $variables['resource'] = $commonGroundService->getResource(['component'=>'orc', 'type'=>'orders', 'id'=> $id]);
        }

        // If it is a delete action we can stop right here
        if ($request->query->get('action') == 'delete') {
            $commonGroundService->deleteResource($variables['resource']);

            return $this->redirect($this->generateUrl('app_orc_orders'));
        }

        $variables['title'] = $translator->trans('order');
        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('order');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'wrc', 'type'=>'organizations'])['hydra:member'];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])

            $variables['resource'] = $commonGroundService->saveResource($resource, (['component'=>'orc', 'type'=>'orders']));

            /* @to this redirect is a hotfix */
            if (array_key_exists('id', $variables['resource'])) {
                return $this->redirect($this->generateUrl('app_orc_orders', ['id' =>  $variables['resource']['id']]));
            }
        }

        return $variables;
    }

    /**
     * @Route("/order_items")
     * @Template
     */
    public function orderItemsAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('persons');
        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('persons');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'orc', 'type'=>'slugs'])['hydra:member'];

        return $variables;
    }

    /**
     * @Route("/order_items/{id}")
     * @Template
     */
    public function orderItemAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
        $variables = [];

        // Lets see if we need to create
        if ($id == 'new') {
            $variables['resource'] = ['@id' => null, 'id'=>'new', 'name'=>'new'];
        } else {
            $variables['resource'] = $commonGroundService->getResource(['component'=>'orc', 'type'=>'order_items', 'id'=> $id]);
        }

        // If it is a delete action we can stop right here
        if ($request->query->get('action') == 'delete') {
            $commonGroundService->deleteResource($variables['resource']);

            return $this->redirect($this->generateUrl('app_orc_orderitems'));
        }

        $variables['title'] = $translator->trans('order item');
        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('order item');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'orc', 'type'=>'organizations'])['hydra:member'];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])

            $variables['resource'] = $commonGroundService->saveResource($resource, (['component'=>'orc', 'type'=>'order_items']));

            /* @to this redirect is a hotfix */
            if (array_key_exists('id', $variables['resource'])) {
                return $this->redirect($this->generateUrl('app_orc_orderitems', ['id' =>  $variables['resource']['id']]));
            }
        }

        return $variables;
    }

    /**
     * @Route("/taxes")
     * @Template
     */
    public function taxesAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('taxes');
        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('taxes');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'orc', 'type'=>'taxes'])['hydra:member'];

        return $variables;
    }

    /**
     * @Route("/taxes/{id}")
     * @Template
     */
    public function taxAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
        $variables = [];

        // Lets see if we need to create
        if ($id == 'new') {
            $variables['resource'] = ['@id' => null, 'id'=>'new', 'name'=>'new'];
        } else {
            $variables['resource'] = $commonGroundService->getResource(['component'=>'orc', 'type'=>'taxes', 'id'=> $id]);
        }

        // If it is a delete action we can stop right here
        if ($request->query->get('action') == 'delete') {
            $commonGroundService->deleteResource($variables['resource']);

            return $this->redirect($this->generateUrl('app_orc_taxes'));
        }

        $variables['title'] = $translator->trans('tax');
        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('tax');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'orc', 'type'=>'organizations'])['hydra:member'];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])

            $variables['resource'] = $commonGroundService->saveResource($resource, (['component'=>'orc', 'type'=>'taxes']));

            /* @to this redirect is a hotfix */
            if (array_key_exists('id', $variables['resource'])) {
                return $this->redirect($this->generateUrl('app_orc_taxes', ['id' =>  $variables['resource']['id']]));
            }
        }

        return $variables;
    }

    /**
     * @Route("/organizations")
     * @Template
     */
    public function organizationsAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('organizations');
        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('organizations');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'orc', 'type'=>'organizations'])['hydra:member'];

        return $variables;
    }

    /**
     * @Route("/organizations/{id}")
     * @Template
     */
    public function organizationAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
        $variables = [];

        // Lets see if we need to create
        if ($id == 'new') {
            $variables['resource'] = ['@id' => null, 'id'=>'new', 'name'=>'new'];
        } else {
            $variables['resource'] = $commonGroundService->getResource(['component'=>'orc', 'type'=>'organizations', 'id'=> $id]);
        }

        // If it is a delete action we can stop right here
        if ($request->query->get('action') == 'delete') {
            $commonGroundService->deleteResource($variables['resource']);

            return $this->redirect($this->generateUrl('app_orc_organizations'));
        }

        $variables['title'] = $translator->trans('organization');
        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('organization');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'orc', 'type'=>'organizations'])['hydra:member'];
        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])

            $variables['resource'] = $commonGroundService->saveResource($resource, (['component'=>'orc', 'type'=>'organizations']));

            /* @to this redirect is a hotfix */
            if (array_key_exists('id', $variables['resource'])) {
                return $this->redirect($this->generateUrl('app_orc_organizations', ['id' =>  $variables['resource']['id']]));
            }
        }

        return $variables;
    }
}
