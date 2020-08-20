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
 * Class PdcController.
 *
 * @Route("/uc")
 */
class UcController extends AbstractController
{
    /**
     * @Route("/")
     * @Template
     */
    public function indexAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('user configuration');
        $variables['subtitle'] = $translator->trans('the user component holds all users, user groups and acces rights');

        return $variables;
    }

    /**
     * @Route("/users")
     * @Template
     */
    public function usersAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('users');
        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('users');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'uc', 'type'=>'users'], "organization={$this->getUser()->getOrganization()}")['hydra:member'];

        return $variables;
    }

    /**
     * @Route("/users/{id}")
     * @Template
     */
    public function userAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
        // If it is a delete action we can stop right here
        if ($request->query->get('action') == 'delete') {
            $commonGroundService->deleteResource(['component'=>'uc', 'type'=>'users', 'id'=>$id]);

            return $this->redirect($this->generateUrl('app_uc_users'));
        }

        $variables = [];

        // Lets see if we need to create
        if ($id == 'new') {
            $variables['resource'] = ['@id' => null, 'name'=>'new', 'id'=>'new'];
        } else {
            $variables['resource'] = $commonGroundService->getResource(['component'=>'uc', 'type'=>'users', 'id'=>$id]);
        }

        $variables['title'] = $translator->trans('users');
        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('users');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'wrc', 'type'=>'organizations'])['hydra:member'];
        $variables['people'] = $commonGroundService->getResourceList(['component'=>'cc', 'type'=>'people'])['hydra:member'];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource = $variables['resource'];

            $newRole = $request->request->get('role');
            $deleteRole = $request->request->get('deleteRole');

            if (isset($newRole) && !empty($newRole)) {
                $resource['roles'][] = $newRole;
            }

            if (isset($deleteRole) && !empty($deleteRole)) {
                for ($i = 0; $i < count($resource['roles']); $i++) {
                    if ($resource['roles'][$i] == $deleteRole) {
                        unset($resource['roles'][$i]);
                    }
                }
            }

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])

            $variables['resource'] = $commonGroundService->updateResource($resource, ['component'=>'uc', 'type'=>'users']);

            /* @to this redirect is a hotfix */
            if (array_key_exists('id', $variables['resource'])) {
                return $this->redirect($this->generateUrl('app_uc_users', ['id' =>  $variables['resource']['id']]));
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
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'uc', 'type'=>'groups'], "organization={$this->getUser()->getOrganization()}")['hydra:member'];

        return $variables;
    }

    /**
     * @Route("/groups/{id}")
     * @Template
     */
    public function groupAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
        // If it is a delete action we can stop right here
        if ($request->query->get('action') == 'delete') {
            $commonGroundService->deleteResource(null, ['component'=>'uc', 'type'=>'groups', 'id'=>$id]);

            return $this->redirect($this->generateUrl('app_uc_groups'));
        }
        $variables = [];

        // Lets see if we need to create
        if ($id == 'new') {
            $variables['resource'] = ['@id' => null, 'name'=>'new', 'id'=>'new'];
            $variables['users'] = [];
        } else {
            $variables['resource'] = $commonGroundService->getResource(['component'=>'uc', 'type'=>'groups', 'id'=>$id]);
            $variables['users'] = $commonGroundService->getResourceList(['component'=>'uc', 'type'=>'users'])['hydra:member'];
        }

        $variables['title'] = $translator->trans('group');
        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('group');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'wrc', 'type'=>'organizations'])['hydra:member'];
        $variables['applications'] = $commonGroundService->getResourceList(['component'=>'wrc', 'type'=>'applications'])['hydra:member'];
        $variables['scopes'] = $commonGroundService->getResourceList(['component'=>'uc', 'type'=>'scopes'])['hydra:member'];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // Lets see if we also need t add an slug
            if (array_key_exists('scope', $resource)) {
                $scope = $resource['scope'];
                $scope['userGroups'][] = $resource['@id'];
                // The resource action section
                if (array_key_exists('@id', $scope) && array_key_exists('action', $scope)) {
                    // The delete action
                    if ($scope['action'] == 'delete') {
                        $commonGroundService->deleteResource($scope);

                        $variables['resource'] = $commonGroundService->saveResource($resource, ['component'=>'uc', 'type'=>'scopes']);

                        return $this->redirect($this->generateUrl('app_uc_group', ['id' => $id]));
                    }
                }
                unset($scope['application']);
                $scope = $commonGroundService->saveResource($scope, ['component' => 'uc', 'type' => 'scopes']);
            }

            // Lets see if we also need to add an configurati
            $variables['resource'] = $commonGroundService->saveResource($resource, ['component'=>'uc', 'type'=>'groups']);

            /* @to this redirect is a hotfix */
            if (array_key_exists('id', $variables['resource'])) {
                return $this->redirect($this->generateUrl('app_uc_groups', ['id' =>  $variables['resource']['id']]));
            }
        }

        return $variables;
    }

    /**
     * @Route("/scopes")
     * @Template
     */
    public function scopesAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('scopes');
        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('scopes');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'uc', 'type'=>'scopes'])['hydra:member'];

        return $variables;
    }

    /**
     * @Route("/scopes/{id}")
     * @Template
     */
    public function scopeAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
        // If it is a delete action we can stop right here
        if ($request->query->get('action') == 'delete') {
            $commonGroundService->deleteResource(null, ['component'=>'uc', 'type'=>'scopes', 'id'=>$id]);

            return $this->redirect($this->generateUrl('app_uc_scopes'));
        }

        $variables = [];

        // Lets see if we need to create
        if ($id == 'new') {
            $variables['resource'] = ['@id' => null, 'name'=>'new', 'id'=>'new'];
        } else {
            $variables['resource'] = $commonGroundService->getResource(['component'=>'uc', 'type'=>'scopes', 'id'=>$id]);
        }

        // If it is a delete action we can stop right here
        if ($request->query->get('action') == 'delete') {
            $commonGroundService->deleteResource($variables['resource']);

            return $this->redirect($this->generateUrl('app_uc_scopes'));
        }

        $variables['title'] = $translator->trans('scope');
        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('scope');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'wrc', 'type'=>'organizations'])['hydra:member'];
        $variables['applications'] = $commonGroundService->getResourceList(['component'=>'wrc', 'type'=>'applications'])['hydra:member'];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])

            $variables['resource'] = $commonGroundService->saveResource($resource, ['component'=>'uc', 'type'=>'scopes']);

            /* @to this redirect is a hotfix */
            if (array_key_exists('id', $variables['resource'])) {
                return $this->redirect($this->generateUrl('app_uc_scopes', ['id' =>  $variables['resource']['id']]));
            }
        }

        return $variables;
    }

    /**
     * @Route("/applications")
     * @Template
     */
    public function applicationsAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('applications');
        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('applications');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'uc', 'type'=>'applications'])['hydra:member'];

        return $variables;
    }

    /**
     * @Route("/applications/{id}")
     * @Template
     */
    public function applicationAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
        // If it is a delete action we can stop right here
        if ($request->query->get('action') == 'delete') {
            $commonGroundService->deleteResource(null, ['component'=>'uc', 'type'=>'applications', 'id'=>$id]);

            return $this->redirect($this->generateUrl('app_uc_applications'));
        }
        $variables = [];

        // Lets see if we need to create
        if ($id == 'new') {
            $variables['resource'] = ['@id' => null, 'name'=>'new', 'id'=>'new'];
        } else {
            $variables['resource'] = $commonGroundService->getResource(['component'=>'uc', 'type'=>'applications', 'id'=>$id]);
        }

        $variables['title'] = $translator->trans('application');
        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('application');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'wrc', 'type'=>'organizations'])['hydra:member'];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // Lets see if we also need to add an configuration

            $variables['resource'] = $commonGroundService->saveResource($resource, ['component'=>'uc', 'type'=>'applications']);

            /* @to this redirect is a hotfix */
            if (array_key_exists('id', $variables['resource'])) {
                return $this->redirect($this->generateUrl('app_uc_applications', ['id' =>  $variables['resource']['id']]));
            }
        }

        return $variables;
    }

    /**
     * @Route("/providers")
     * @Template
     */
    public function providersAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('providers');
        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('providers');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'uc', 'type'=>'providers'])['hydra:member'];

        return $variables;
    }

    /**
     * @Route("/providers/{id}")
     * @Template
     */
    public function providerAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
        // If it is a delete action we can stop right here
        if ($request->query->get('action') == 'delete') {
            $commonGroundService->deleteResource(null, ['component'=>'uc', 'type'=>'providers', 'id'=>$id]);

            return $this->redirect($this->generateUrl('app_uc_providers'));
        }
        $variables = [];

        // Lets see if we need to create
        if ($id == 'new') {
            $variables['resource'] = ['@id' => null, 'name'=>'new', 'id'=>'new'];
        } else {
            $variables['resource'] = $commonGroundService->getResource(['component'=>'uc', 'type'=>'providers', 'id'=>$id]);
        }

        $variables['title'] = $translator->trans('provider');
        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('provider');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'wrc', 'type'=>'organizations'])['hydra:member'];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // Lets see if we also need to add an configuration

            $variables['resource'] = $commonGroundService->saveResource($resource, ['component'=>'uc', 'type'=>'providers']);

            /* @to this redirect is a hotfix */
            if (array_key_exists('id', $variables['resource'])) {
                return $this->redirect($this->generateUrl('app_uc_providers', ['id' =>  $variables['resource']['id']]));
            }
        }

        return $variables;
    }

    /**
     * @Route("/tokens")
     * @Template
     */
    public function tokensAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('tokens');
        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('tokens');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'uc', 'type'=>'tokens'])['hydra:member'];

        return $variables;
    }

    /**
     * @Route("/tokens/{id}")
     * @Template
     */
    public function tokenAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
        // If it is a delete action we can stop right here
        if ($request->query->get('action') == 'delete') {
            $commonGroundService->deleteResource(null, ['component'=>'uc', 'type'=>'tokens', 'id'=>$id]);

            return $this->redirect($this->generateUrl('app_uc_tokens'));
        }
        $variables = [];

        // Lets see if we need to create
        if ($id == 'new') {
            $variables['resource'] = ['@id' => null, 'name'=>'new', 'id'=>'new'];
        } else {
            $variables['resource'] = $commonGroundService->getResource(['component'=>'uc', 'type'=>'tokens', 'id'=>$id]);
        }

        $variables['title'] = $translator->trans('token');
        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('token');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'wrc', 'type'=>'organizations'])['hydra:member'];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // Lets see if we also need to add an configuration

            $variables['resource'] = $commonGroundService->saveResource($resource, ['component'=>'uc', 'type'=>'tokens']);

            /* @to this redirect is a hotfix */
            if (array_key_exists('id', $variables['resource'])) {
                return $this->redirect($this->generateUrl('app_uc_tokens', ['id' =>  $variables['resource']['id']]));
            }
        }

        return $variables;
    }
}
