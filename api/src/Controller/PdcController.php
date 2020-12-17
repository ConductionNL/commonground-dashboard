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
 * @Route("/pdc")
 */
class PdcController extends AbstractController
{
    /**
     * @Route("/")
     * @Template
     */
    public function indexAction(TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('product and service catalouge');
        $variables['subtitle'] = $translator->trans('the product and service catalouge holds al data concerning product, groups and offers');

        return $variables;
    }

    /**
     * @Route("/products")
     * @Template
     */
    public function productsAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('products');
        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('products');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'pdc', 'type'=>'products'])['hydra:member'];

        return $variables;
    }

    /**
     * @Route("/products/{id}")
     * @Template
     */
    public function productAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
        // If it is a delete action we can stop right here
        if ($request->query->get('action') == 'delete') {
            $commonGroundService->deleteResource(['component'=>'pdc', 'type'=>'products', 'id'=> $id]);

            return $this->redirect($this->generateUrl('app_pdc_products'));
        }

        $variables = [];

        // Lets see if we need to create
        if ($id == 'new') {
            $variables['resource'] = ['@id' => null, 'name' => 'new', 'id' => 'new'];
        } else {
            $variables['resource'] = $commonGroundService->getResource(['component'=>'pdc', 'type'=>'products', 'id'=> $id]);
        }

        // If it is a delete action we can stop right here
        if ($request->query->get('action') == 'delete') {
            $commonGroundService->deleteResource($variables['resource']);

            return $this->redirect($this->generateUrl('app_pdc_products'));
        }

        $variables['title'] = $translator->trans('product');
        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('product');
        $variables['groups'] = $commonGroundService->getResourceList(['component'=>'pdc', 'type'=>'groups'])['hydra:member'];
        $variables['catalogues'] = $commonGroundService->getResourceList(['component'=>'pdc', 'type'=>'catalogues'])['hydra:member'];
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'wrc', 'type'=>'organizations'])['hydra:member'];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();

            $resource['taxPercentage'] = (int) $resource['taxPercentage'];
            $resource['requiresAppointment'] = $resource['requiresAppointment'] == 'true' ? true : false;

            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])

            $variables['resource'] = $commonGroundService->saveResource($resource, (['component'=>'pdc', 'type'=>'products']));

            /* @to this redirect is a hotfix */
            if (array_key_exists('id', $variables['resource'])) {
                return $this->redirect($this->generateUrl('app_pdc_products', ['id' =>  $variables['resource']['id']]));
            }
        }

        return $variables;
    }

    /**
     * @Route("/groups")
     * @Template
     */
    public function groupsAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('groups');
        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('groups');
        $variables['resources'] = $commonGroundService->getResourceList(['component' =>'pdc', 'type'=>'groups'])['hydra:member'];

        return $variables;
    }

    /**
     * @Route("/groups/{id}")
     * @Template
     */
    public function groupAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
        $variables = [];

        // Lets see if we need to create
        if ($id == 'new') {
            $variables['resource'] = ['@id' => null, 'name'=>'new', 'id'=>'new'];
        } else {
            $variables['resource'] = $commonGroundService->getResource(['component' =>'pdc', 'type'=>'groups', 'id'=>$id]);
        }

        // If it is a delete action we can stop right here
        if ($request->query->get('action') == 'delete') {
            $commonGroundService->deleteResource($variables['resource']);

            return $this->redirect($this->generateUrl('app_pdc_groups'));
        }

        $variables['title'] = $translator->trans('group');
        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('group');
        $variables['catalogues'] = $commonGroundService->getResourceList(['component' =>'pdc', 'type'=>'catalogues'])['hydra:member'];
        $variables['organizations'] = $commonGroundService->getResourceList(['component' =>'wrc', 'type'=>'organizations'])['hydra:member'];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])

            $variables['resource'] = $commonGroundService->saveResource($resource, (['component' =>'pdc', 'type'=>'groups']));

            /* @to this redirect is a hotfix */
            if (array_key_exists('id', $variables['resource'])) {
                return $this->redirect($this->generateUrl('app_pdc_groups', ['id' =>  $variables['resource']['id']]));
            }
        }

        return $variables;
    }

    /**
     * @Route("/offers")
     * @Template
     */
    public function offersAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('offers');
        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('offers');
        $variables['resources'] = $commonGroundService->getResourceList(['component' =>'pdc', 'type'=>'offers'])['hydra:member'];

        return $variables;
    }

    /**
     * @Route("/offer/{id}")
     * @Template
     */
    public function offerAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
        $variables = [];

        // Lets see if we need to create
        if ($id == 'new') {
            $variables['resource'] = ['@id' => null, 'name'=>'new', 'id'=>'new'];
        } else {
            $variables['resource'] = $commonGroundService->getResource(['component' =>'pdc', 'type'=>'offers', 'id'=>$id]);
        }

        // If it is a delete action we can stop right here
        if ($request->query->get('action') == 'delete') {
            $commonGroundService->deleteResource($variables['resource']);

            return $this->redirect($this->generateUrl('app_pdc_offers'));
        }

        $variables['title'] = $translator->trans('offer');
        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('offer');
        $variables['organizations'] = $commonGroundService->getResourceList(['component' =>'wrc', 'type'=>'organizations'])['hydra:member'];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])

            $variables['resource'] = $commonGroundService->saveResource($resource, (['component' =>'pdc', 'type'=>'offers']));

            /* @to this redirect is a hotfix */
            if (array_key_exists('id', $variables['resource'])) {
                return $this->redirect($this->generateUrl('app_pdc_offers', ['id' =>  $variables['resource']['id']]));
            }
        }

        return $variables;
    }

    /**
     * @Route("/catalogues")
     * @Template
     */
    public function cataloguesAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('catalogues');
        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('catalogues');
        $variables['resources'] = $commonGroundService->getResourceList(['component' =>'pdc', 'type'=>'catalogues'])['hydra:member'];

        return $variables;
    }

    /**
     * @Route("/catalogues/{id}")
     * @Template
     */
    public function catalogueAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
        $variables = [];

        // Lets see if we need to create
        if ($id == 'new') {
            $variables['resource'] = ['@id' => null, 'name'=>'new', 'id'=>'new'];
        } else {
            $variables['resource'] = $commonGroundService->getResource(['component' =>'pdc', 'type'=>'catalogues', 'id'=>$id]);
        }

        // If it is a delete action we can stop right here
        if ($request->query->get('action') == 'delete') {
            $commonGroundService->deleteResource($variables['resource']);

            return $this->redirect($this->generateUrl('app_pdc_catalogues'));
        }

        $variables['title'] = $translator->trans('catalogue');
        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('catalogue');
        $variables['organizations'] = $commonGroundService->getResourceList(['component' =>'wrc', 'type'=>'organizations'])['hydra:member'];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])

            $variables['resource'] = $commonGroundService->saveResource($resource, (['component' =>'pdc', 'type'=>'catalogues']));
            /* @to this redirect is a hotfix */
            if (array_key_exists('id', $variables['resource'])) {
                return $this->redirect($this->generateUrl('app_pdc_catalogues', ['id' =>  $variables['resource']['id']]));
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
        $variables['resources'] = $commonGroundService->getResourceList(['component' =>'pdc', 'type'=>'taxes'])['hydra:member'];

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
            $variables['resource'] = ['@id' => null, 'name'=>'new', 'id'=>'new'];
        } else {
            $variables['resource'] = $commonGroundService->getResource(['component' =>'pdc', 'type'=>'taxes', 'id'=>$id]);
        }

        // If it is a delete action we can stop right here
        if ($request->query->get('action') == 'delete') {
            $commonGroundService->deleteResource($variables['resource']);

            return $this->redirect($this->generateUrl('app_pdc_taxes'));
        }

        $variables['title'] = $translator->trans('tax');
        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('tax');
        $variables['organizations'] = $commonGroundService->getResourceList(['component' =>'wrc', 'type'=>'organizations'])['hydra:member'];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])

            $variables['resource'] = $commonGroundService->saveResource($resource, (['component' =>'pdc', 'type'=>'taxes']));

            /* @to this redirect is a hotfix */
            if (array_key_exists('id', $variables['resource'])) {
                return $this->redirect($this->generateUrl('app_pdc_taxes', ['id' =>  $variables['resource']['id']]));
            }
        }

        return $variables;
    }

    /**
     * @Route("/suppliers")
     * @Template
     */
    public function suppliersAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('suppliers');
        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('suppliers');
        $variables['resources'] = $commonGroundService->getResourceList(['component' =>'pdc', 'type'=>'suppliers'])['hydra:member'];

        return $variables;
    }

    /**
     * @Route("/suppliers/{id}")
     * @Template
     */
    public function supplierAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
        $variables = [];

        // Lets see if we need to create
        if ($id == 'new') {
            $variables['resource'] = ['@id' => null, 'name'=>'new', 'id'=>'new'];
        } else {
            $variables['resource'] = $commonGroundService->getResource(['component' =>'pdc', 'type'=>'suppliers', 'id'=>$id]);
        }

        // If it is a delete action we can stop right here
        if ($request->query->get('action') == 'delete') {
            $commonGroundService->deleteResource($variables['resource']);

            return $this->redirect($this->generateUrl('app_pdc_suppliers'));
        }

        $variables['title'] = $translator->trans('supplier');
        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('supplier');
        $variables['organizations'] = $commonGroundService->getResourceList(['component' =>'wrc', 'type'=>'organizations'])['hydra:member'];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])

            $variables['resource'] = $commonGroundService->saveResource($resource, (['component' =>'pdc', 'type'=>'suppliers']));
            /* @to this redirect is a hotfix */
            if (array_key_exists('id', $variables['resource'])) {
                return $this->redirect($this->generateUrl('app_pdc_suppliers', ['id' =>  $variables['resource']['id']]));
            }
        }

        return $variables;
    }

    /**
     * @Route("/customer_types")
     * @Template
     */
    public function customerTypesAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('customer types');
        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('customer types');
        $variables['resources'] = $commonGroundService->getResourceList(['component' =>'pdc', 'type'=>'customer_types'])['hydra:member'];

        return $variables;
    }

    /**
     * @Route("/customer_types/{id}")
     * @Template
     */
    public function customerTypeAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
        $variables = [];

        // Lets see if we need to create
        if ($id == 'new') {
            $variables['resource'] = ['@id' => null, 'name'=>'new', 'id'=>'new'];
        } else {
            $variables['resource'] = $commonGroundService->getResource(['component' =>'pdc', 'type'=>'customer_types', 'id'=>$id]);
        }

        // If it is a delete action we can stop right here
        if ($request->query->get('action') == 'delete') {
            $commonGroundService->deleteResource($variables['resource']);

            return $this->redirect($this->generateUrl('app_pdc_customertypes'));
        }

        $variables['title'] = $translator->trans('customer type');
        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('customer type');
        $variables['organizations'] = $commonGroundService->getResourceList(['component' =>'wrc', 'type'=>'organizations'])['hydra:member'];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])

            $variables['resource'] = $commonGroundService->saveResource($resource, ['component' =>'pdc', 'type'=>'customer_types']);
            /* @to this redirect is a hotfix */
            if (array_key_exists('id', $variables['resource'])) {
                return $this->redirect($this->generateUrl('app_pdc_customertypes', ['id' =>  $variables['resource']['id']]));
            }
        }

        return $variables;
    }
}
