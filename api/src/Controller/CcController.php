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
 * Class CcController.
 *
 * @Route("/cc")
 */
class CcController extends AbstractController
{
    /**
     * @Route("/")
     * @Template
     */
    public function indexAction(TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('location catalogue');
        $variables['subtitle'] = $translator->trans('the location catalogue holds al data concerning accomodations, places, changelogs and auditrails.');

        return $variables;
    }

    /**
     * @Route("/people")
     * @Template
     */
    public function peopleAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('persons');
        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('persons');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'cc', 'type'=>'people'])['hydra:member'];

        return $variables;
    }

    /**
     * @Route("/people/{id}")
     * @Template
     */
    public function personAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
        // If it is a delete action we can stop right here
        if ($request->query->get('action') == 'delete') {
            $commonGroundService->deleteResource(['component'=>'cc', 'type'=>'people', 'id'=> $id]);

            return $this->redirect($this->generateUrl('app_cc_people'));
        }

        $variables = [];

        // Lets see if we need to create
        if ($id == 'new') {
            $variables['resource'] = ['@id' => null, 'id'=>'new', 'name'=>'new'];
        } else {
            $variables['resource'] = $commonGroundService->getResource(['component'=>'cc', 'type'=>'people', 'id'=> $id]);
        }

        /*
        // If it is a delete action we can stop right here
        if($request->query->get('action') == 'delete'){
            $commonGroundService->deleteResource($variables['resource']);
            return $this->redirect($this->generateUrl('app_cc_persons'));
        }
        */

        $variables['title'] = $translator->trans('person');
        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('person');
        $variables['telephones'] = $commonGroundService->getResourceList(['component'=>'cc', 'type'=>'telephones'])['hydra:member'];
        $variables['addresses'] = $commonGroundService->getResourceList(['component'=>'cc', 'type'=>'addresses'])['hydra:member'];
        $variables['emails'] = $commonGroundService->getResourceList(['component'=>'cc', 'type'=>'emails'])['hydra:member'];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            $variables['resource'] = $commonGroundService->saveResource($resource, ['component'=>'cc', 'type'=>'people']);

            /* @to this redirect is a hotfix */
            if (array_key_exists('id', $variables['resource'])) {
                return $this->redirect($this->generateUrl('app_cc_people', ['id' =>  $variables['resource']['id']]));
            }
        }

        return $variables;
    }

    /**
     * @Route("/addresses")
     * @Template
     */
    public function addressesAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('addresses');
        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('addresses');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'cc', 'type'=>'addresses'])['hydra:member'];

        return $variables;
    }

    /**
     * @Route("/adresses/{id}")
     * @Template
     */
    public function addressAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
        $variables = [];

        // Lets see if we need to create
        if ($id == 'new') {
            $variables['resource'] = ['@id' => null, 'id'=>'new', 'name'=>'new'];
        } else {
            $variables['resource'] = $commonGroundService->getResource(['component'=>'cc', 'type'=>'addresses', 'id'=> $id]);
        }

        // If it is a delete action we can stop right here
        if ($request->query->get('action') == 'delete') {
            $commonGroundService->deleteResource($variables['resource']);

            return $this->redirect($this->generateUrl('app_cc_addresses'));
        }

        $variables['title'] = $translator->trans('address');
        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('address');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'wrc', 'type'=>'organizations'])['hydra:member'];
        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])

            $variables['resource'] = $commonGroundService->saveResource($resource, (['component'=>'cc', 'type'=>'addresses']));
        }

        /* @to this redirect is a hotfix */
        if (array_key_exists('id', $variables['resource'])) {
            return $this->redirect($this->generateUrl('app_cc_addresses', ['id' =>  $variables['resource']['id']]));
        }

        return $variables;
    }

    /**
     * @Route("/emails")
     * @Template
     */
    public function emailsAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('emails');
        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('emails');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'cc', 'type'=>'emails'])['hydra:member'];

        return $variables;
    }

    /**
     * @Route("/emails/{id}")
     * @Template
     */
    public function emailAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
        $variables = [];

        // Lets see if we need to create
        if ($id == 'new') {
            $variables['resource'] = ['@id' => null, 'id'=>'new', 'name'=>'new'];
        } else {
            $variables['resource'] = $commonGroundService->getResource(['component'=>'cc', 'type'=>'emails', 'id'=> $id]);
        }

        // If it is a delete action we can stop right here
        if ($request->query->get('action') == 'delete') {
            $commonGroundService->deleteResource($variables['resource']);

            return $this->redirect($this->generateUrl('app_cc_emails'));
        }

        $variables['title'] = $translator->trans('email');
        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('email');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'wrc', 'type'=>'organizations'])['hydra:member'];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])

            $variables['resource'] = $commonGroundService->saveResource($resource, (['component'=>'cc', 'type'=>'emails']));
        }

        /* @to this redirect is a hotfix */
        if (array_key_exists('id', $variables['resource'])) {
            return $this->redirect($this->generateUrl('app_cc_emails', ['id' =>  $variables['resource']['id']]));
        }

        return $variables;
    }

    /**
     * @Route("/telephones")
     * @Template
     */
    public function telephonesAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('telephones');
        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('telephones');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'cc', 'type'=>'telephones'])['hydra:member'];

        return $variables;
    }

    /**
     * @Route("/telephones/{id}")
     * @Template
     */
    public function telephoneAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
        $variables = [];

        // Lets see if we need to create
        if ($id == 'new') {
            $variables['resource'] = ['@id' => null, 'id'=>'new', 'name'=>'new'];
        } else {
            $variables['resource'] = $commonGroundService->getResource(['component'=>'cc', 'type'=>'telephones', 'id'=> $id]);
        }

        // If it is a delete action we can stop right here
        if ($request->query->get('action') == 'delete') {
            $commonGroundService->deleteResource($variables['resource']);

            return $this->redirect($this->generateUrl('app_cc_telephones'));
        }

        $variables['title'] = $translator->trans('telephone');
        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('telephones');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'wrc', 'type'=>'organizations'])['hydra:member'];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])

            $variables['resource'] = $commonGroundService->saveResource($resource, (['component'=>'cc', 'type'=>'telephones']));

            /* @to this redirect is a hotfix */
            if (array_key_exists('id', $variables['resource'])) {
                return $this->redirect($this->generateUrl('app_cc_telephones', ['id' =>  $variables['resource']['id']]));
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
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'cc', 'type'=>'organizations'])['hydra:member'];

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
            $variables['resource'] = $commonGroundService->getResource(['component'=>'cc', 'type'=>'organizations', 'id'=> $id]);
        }

        // If it is a delete action we can stop right here
        if ($request->query->get('action') == 'delete') {
            $commonGroundService->deleteResource($variables['resource']);

            return $this->redirect($this->generateUrl('app_cc_organizations'));
        }

        $variables['title'] = $translator->trans('organization');
        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('organizations');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'wrc', 'type'=>'organizations'])['hydra:member'];
        $variables['telephones'] = $commonGroundService->getResourceList(['component'=>'cc', 'type'=>'telephones'])['hydra:member'];
        $variables['addresses'] = $commonGroundService->getResourceList(['component'=>'cc', 'type'=>'addresses'])['hydra:member'];
        $variables['emails'] = $commonGroundService->getResourceList(['component'=>'cc', 'type'=>'emails'])['hydra:member'];
        $variables['people'] = $commonGroundService->getResourceList(['component'=>'cc', 'type'=>'people'])['hydra:member'];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])

            $variables['resource'] = $commonGroundService->saveResource($resource, (['component'=>'cc', 'type'=>'organizations']));

            /* @to this redirect is a hotfix */
            if (array_key_exists('id', $variables['resource'])) {
                return $this->redirect($this->generateUrl('app_cc_organizations', ['id' =>  $variables['resource']['id']]));
            }
        }

        return $variables;
    }

    /**
     * @Route("/contact_lists")
     * @Template
     */
    public function contactListsAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('contact lists');
        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('contact lists');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'cc', 'type'=>'contact_lists'])['hydra:member'];

        return $variables;
    }

    /**
     * @Route("/contact_lists/{id}")
     * @Template
     */
    public function contactListAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
        $variables = [];

        // Lets see if we need to create
        if ($id == 'new') {
            $variables['resource'] = ['@id' => null, 'id'=>'new', 'name'=>'new'];
        } else {
            $variables['resource'] = $commonGroundService->getResource(['component'=>'cc', 'type'=>'contact_lists', 'id'=> $id]);
        }

        // If it is a delete action we can stop right here
        if ($request->query->get('action') == 'delete') {
            $commonGroundService->deleteResource($variables['resource']);

            return $this->redirect($this->generateUrl('app_cc_contactlists'));
        }

        $variables['title'] = $translator->trans('contact list');
        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('contact lists');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'cc', 'type'=>'organizations'])['hydra:member'];
        $variables['people'] = $commonGroundService->getResourceList(['component'=>'cc', 'type'=>'people'])['hydra:member'];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])

            $variables['resource'] = $commonGroundService->saveResource($resource, (['component'=>'cc', 'type'=>'contact_lists']));
        }

        /* @to this redirect is a hotfix */
        if (array_key_exists('id', $variables['resource'])) {
            return $this->redirect($this->generateUrl('app_cc_contactlists', ['id' =>  $variables['resource']['id']]));
        }

        return $variables;
    }
}
