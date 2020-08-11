<?php

// src/Controller/DashboardController.php

namespace App\Controller;

use Conduction\CommonGroundBundle\Service\CommonGroundService;
use Conduction\CommonGroundBundle\Service\RequestService;
use http\Params;
use phpDocumentor\Reflection\DocBlock\Tags\Param;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class DashboardController.
 *
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
     * @Route("/requests/{organization}", defaults={"organization"="none"})
     */
    public function requestsAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, ParameterBagInterface $params, $organization)
    {
        $variables = [];
        $variables['title'] = $translator->trans('requests');
        $variables['subtitle'] = $translator->trans('all') . ' ' . $translator->trans('requests');
        $variables['thisPath'] = "app_vrc_requests";

        $variables['requestTypes'] = $commonGroundService->getResourceList(['component' => 'vtc', 'type' => 'request_types'])['hydra:member'];

        $query = "";

        if (!isset($organization) || empty($organization) || $organization == 'none') {
            $variables['organizations'] = $commonGroundService->getResourceList(['component' => 'wrc', 'type' => 'organizations'])['hydra:member'];
        } elseif ($params->get('app_env') == 'dev') {
            $variables['organization'] = [];
            $variables['organization']['id'] = $organization;
            $variables['organization']['@id'] = 'https://wrc.dev.' . $params->get('app_domain') . '/organizations/' . $organization;

            if (isset($query) && !empty($query)) {
                $query = $query . "&organization=" . $variables['organization']['@id'];
            } else {
                $query = $query . "organization=" . $variables['organization']['@id'];
            }
        } else {
            $variables['organization'] = [];
            $variables['organization']['id'] = $organization;
            $variables['organization']['@id'] = 'https://wrc.' . $params->get('app_domain') . '/organizations/' . $organization;

            if (isset($query) && !empty($query)) {
                $query = $query . "&organization=" . $variables['organization']['@id'];
            } else {
                $query = $query . "organization=" . $variables['organization']['@id'];
            }
        }

        $variables['requestType'] = $request->query->get('requestType');


        if ($request->query->get('status')) {
            $variables['status'] = $query['status'];
        }

        if (isset($variables['requestType'])) {
            $variables['requestType'] = $commonGroundService->getResource(['component' => 'vtc', 'type' => 'request_types', 'id' => $variables['requestType']]);

            if (isset($query) && !empty($query)) {
                $query = $query . "&requestType=" . $variables['requestType'];
            } else {
                $query = $query . "requestType=" . $variables['requestType'];
            }

            $variables['subtitle'] = 'alle ' . $variables['requestType']['name'];

            $variables['resources'] = $commonGroundService->getResourceList(['component' => 'vrc', 'type' => 'requests'], "[" . $query . "]")['hydra:member'];

        } else {

            $variables['resources'] = $commonGroundService->getResourceList(['component' => 'vrc', 'type' => 'requests'], "[" . $query . "]")['hydra:member'];
        }

        if ($request->isMethod('POST')) {

            if (isset($_POST['filter'])) {

                $filters = $request->request->all();

//                var_dump($filters);
//die;
                $typeFilter = $request->request->get('typeFilter');
                $referenceFilter = $request->request->get('referenceFilter');
                $createdFilter = $request->request->get('createdFilter');
                $modifiedFilter = $request->request->get('modifiedFilter');
                $statusFilter = $request->request->get('statusFilter');

                if (isset($typeFilter) && !empty($typeFilter)) {
                    if (isset($query) && !empty($query)) {
                        $query = $query . "&requestType=" . $typeFilter;
                    } else {
                        $query = $query . "requestType=" . $typeFilter;
                    }
                }

                if (isset($referenceFilter) && !empty($referenceFilter)) {
                    if (isset($query) && !empty($query)) {
                        $query = $query . "&reference=" . $referenceFilter;
                    } else {
                        $query = $query . "reference=" . $referenceFilter;
                    }
                }

                if(isset($createdFilter) && !empty($createdFilter)){
                    $date = $createdFilter;

                    // Because you cant filter for 1 date we have to filter between 2 dates
                    $date1 = date('Y-m-d', strtotime($date.' - 1 day'));
                    $date2 = date('Y-m-d', strtotime($date.' + 1 day'));

                    if (isset($query) && !empty($query)) {
                        $query = $query . "&dateCreated[strictly_before]=" . $date2 . "&dateCreated[strictly_after]=" .$date1;
                    } else {
                        $query = $query . "dateCreated[strictly_before]=" . $date2 . "&dateCreated[strictly_after]=" .$date1;
                    }

                }

                if(isset($modifiedFilter) && !empty($modifiedFilter)){
                    $date = $modifiedFilter;

                    // Because you cant filter for 1 date we have to filter between 2 dates
                    $date1 = date('Y-m-d', strtotime($date.' - 1 day'));
                    $date2 = date('Y-m-d', strtotime($date.' + 1 day'));

                    if (isset($query) && !empty($query)) {
                        $query = $query . "&dateModified[strictly_before]=" . $date2 . "&dateModified[strictly_after]=" .$date1;
                    } else {
                        $query = $query . "dateModified[strictly_before]=" . $date2 . "&dateModified[strictly_after]=" .$date1;
                    }

                }

                if (isset($statusFilter) && !empty($statusFilter)) {
                    if (isset($query) && !empty($query)) {
                        $query = $query . "&status=" . $statusFilter;
                    } else {
                        $query = $query . "status=" . $statusFilter;
                    }
                }

                $variables['resources'] = $commonGroundService->getResourceList(['component' => 'vrc', 'type' => 'requests'], $query)['hydra:member'];
            }
        }

        // Tadaa a very simple download function
        if ($request->query->get('download')) {
            $serializer = new Serializer([new ObjectNormalizer()], [new CsvEncoder()]);

            $response = new Response();

            $filename = date('YmdHis') . '_requests';
            //set headers
            $response->headers->set('Content-Type', 'text/csv');
            $response->headers->set('Content-Disposition', 'attachment;filename="' . $filename . '.csv');

            $response->setContent($serializer->encode($variables['resources'], 'csv'));

            return $response;
        }

        /* If we have specific view for this request type use that instead */
        if (array_key_exists('requestType', $variables) && $this->get('twig')->getLoader()->exists('vrc/requests_templates/' . $variables['requestType']['id'] . '.html.twig')) {
            return $this->render('vrc/requests_templates/' . $variables['requestType']['id'] . '.html.twig', $variables);
        } else {
            return $this->render('vrc/requests.html.twig', $variables);
        }

        return $this->render('vrc/requests.html.twig', $variables);
    }

    /**
     * @Route("/requests/{id}")
     */
    public function requestAction(Request $request, CommonGroundService $commonGroundService, RequestService $requestService, TranslatorInterface $translator, $id)
    {

        // If it is a delete action we can stop right here
        if ($request->query->get('action') == 'delete') {
            $commonGroundService->deleteResource(null, ['component' => 'vrc', 'type' => 'requests', 'id' => $id]);

            return $this->redirect($this->generateUrl('app_vrc_requests'));
        }

        $variables = [];
        $variables['employees'] = $commonGroundService->getResourceList(['component' => 'mrc', 'type' => 'employees'])['hydra:member'];
        // Lets see if we need to create

        if ($id == 'new') {
            if (!$request->isMethod('POST')) {
                $variables['resource'] = ['@id' => null, 'name' => 'new', 'id' => 'new', 'reference' => 'new request'];
            }
        } else {
            $variables['resource'] = $commonGroundService->getResource(['component' => 'vrc', 'type' => 'requests', 'id' => $id], [], true);
            $variables['changeLog'] = $commonGroundService->getResourceList($variables['resource']['@id'] . '/change_log');
            $variables['auditTrail'] = $commonGroundService->getResourceList($variables['resource']['@id'] . '/audit_trail');
            $variables['submitters'] = $commonGroundService->getResourceList(['component' => 'vrc', 'type' => 'submitters'], ['request' => $variables['resource']['@id']])['hydra:member'];
            $variables['roles'] = $commonGroundService->getResourceList(['component' => 'vrc', 'type' => 'roles'])['hydra:member'];
            $variables['requestTypes'] = $commonGroundService->getResourceList(['component' => 'vtc', 'type' => 'request_types'])['hydra:member'];

            // $variables['tasks'] = []; //$commonGroundService->getResourceList(['component' => 'tc', 'type' => 'tasks'])["hydra:member"];
            // $variables['messages'] = $commonGroundService->getResourceList(['component' => 'bs', 'type' => 'messages'])["hydra:member"];
            // $variables['memos'] = $commonGroundService->getResourceList(['component' => 'memo', 'type' => 'memos'])["hydra:member"];
            $variables['queues'] = $commonGroundService->getResourceList(['component' => 'qc', 'type' => 'tasks'], ['resource' => $variables['resource']['@id']])['hydra:member'];

            if (array_key_exists('requestType', $variables['resource'])) {
                $variables['requestType'] = $commonGroundService->getResource($variables['resource']['requestType']);
            }

            if (array_key_exists('zaaktype', $variables['resource'])) {
                $variables['casestatuses'] = $commonGroundService->getResourceList(['component' => 'ztc', 'type' => 'statustypen'], ['zaaktype' => $variables['resource']['zaaktype']])['results'];
            }

            if (array_key_exists('requestType', $variables['resource'])) {
                $variables['requestType'] = $commonGroundService->getResource($variables['resource']['requestType']);

                /* @todo wat doet dit hier ? */
                if ($variables['requestType']['id'] == '5b10c1d6-7121-4be2-b479-7523f1b625f1') {
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

        $variables['camundaTasks'] = [];
        if (array_key_exists('resource', $variables) && array_key_exists('processes', $variables['resource'])) {
            foreach ($variables['resource']['processes'] as $proces) {
                $camundaTasks = $commonGroundService->getResourceList(['component' => 'be', 'type' => 'task'], ['processInstanceId' => $commonGroundService->getUuidFromUrl($proces)]);
                foreach ($camundaTasks as $camundaTask) {
                    $camundaTask['form'] = $commonGroundService->getResource(['component' => 'be', 'type' => 'task/' . $camundaTask['id'] . '/rendered-form']);
                    $variables['camundaTasks'][] = $camundaTask;
                }
            }
        }

        $variables['requestTypes'] = $commonGroundService->getResourceList(['component' => 'vtc', 'type' => 'request_types'])['hydra:member'];
        $variables['organizations'] = $commonGroundService->getResourceList(['component' => 'wrc', 'type' => 'organizations'])['hydra:member'];

        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            // if we have a resource we want to use that id
            if (array_key_exists('resource', $variables)) {
                $resource['@id'] = $variables['resource']['@id'];
                $resource['id'] = $variables['resource']['id'];
            }

            if (array_key_exists('role', $resource)) {
                $role = $resource['role'];
                $role['request'] = $resource['@id'];

                // The resource action section
                if (array_key_exists('@id', $role) && array_key_exists('action', $role)) {
                    // The delete action
                    if ($role['action'] == 'delete') {
                        $commonGroundService->deleteResource($role);

                        return $this->redirect($this->generateUrl('app_vrc_request', ['id' => $id]));
                    }
                }
                $role = $commonGroundService->saveResource($role, ['component' => 'vrc', 'type' => 'roles']);
            }

            if (array_key_exists('memo', $resource)) {
                $memo = $resource['memo'];
                $memo['topic'] = $resource['@id'];

                // The resource action section
                if (array_key_exists('@id', $memo) && array_key_exists('action', $memo)) {
                    // The delete action
                    if ($memo['action'] == 'delete') {
                        $commonGroundService->deleteResource($memo);

                        return $this->redirect($this->generateUrl('app_vrc_request', ['id' => $id]));
                    }
                }
                $memo = $commonGroundService->saveResource($memo, ['component' => 'memo', 'type' => 'memos']);
            }

            if (array_key_exists('task', $resource)) {
                $task = $resource['task'];
                $task['topic'] = $resource['@id'];
                $task['priority'] = (int)$task['priority'];
                $task['percentageDone'] = (int)$task['percentageDone'];

                // The resource action section
                if (array_key_exists('@id', $task) && array_key_exists('action', $task)) {
                    // The delete action
                    if ($task['action'] == 'delete') {
                        $commonGroundService->deleteResource($task);

                        return $this->redirect($this->generateUrl('app_vrc_request', ['id' => $id]));
                    }
                }
                $task = $commonGroundService->saveResource($task, ['component' => 'tc', 'type' => 'tasks']);
            }
            if (array_key_exists('newProp', $resource)) {
                $item = $commonGroundService->getResource(['component'=>'vrc', 'type'=>'requests', 'id'=>$id], [], true);
                $item['properties']['temp'] = 'temp';
                $item['properties'][$resource['newPropName']] = $resource['newProp'];
                unset($item['properties']['temp']);
                $resource['properties'] = $item['properties'];
            }

            // Fix for properties not being nullabe @todo long term fix should be implemented
            if (!array_key_exists('properties', $resource)) {
                $resource['properties'] = [];
            }

            // If there are any sub data sources the need to be removed below in order to save the resource

            // Lets see if we also need to add an slug
            if (array_key_exists('unsetProperty', $resource) && is_array($resource['unsetProperty'])) {
//                foreach($resource['properties'] as $property => $value){
//
//                }
                echo var_dump($resource);
            }
            if (array_key_exists('setProperty', $resource) && is_array($resource['setProperty'])) {
            }

            $variables['resource'] = $commonGroundService->saveResource($resource, (['component' => 'vrc', 'type' => 'requests']));

            /* @to this redirect is a hotfix */
            if (array_key_exists('id', $variables['resource'])) {
                return $this->redirect($this->generateUrl('app_vrc_request', ['id' => $variables['resource']['id']]));
            }
        }
        //$variables['casetypes'] = $commonGroundService->getResourceList(['component' => 'ztc', 'type' => 'zaaktypen'])["results"];

        /* If we have specific view for this request type use that instead */
        if (array_key_exists('requestType', $variables['resource']) && $this->get('twig')->getLoader()->exists('vrc/request_templates/' . $variables['requestType']['id'] . '.html.twig')) {
            return $this->render('vrc/request_templates/' . $variables['requestType']['id'] . '.html.twig', $variables);
        } else {
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
        $variables['subtitle'] = $translator->trans('all') . ' ' . $translator->trans('labels');
        $variables['resources'] = $commonGroundService->getResourceList(['component' => 'vrc', 'type' => 'labels'])['hydra:member'];

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
        if ($id == 'new') {
            $variables['resource'] = ['@id' => null, 'id' => 'new', 'name' => 'label'];
        } else {
            $variables['resource'] = $commonGroundService->getResource(['component' => 'vrc', 'type' => 'labels', 'id' => $id]);
        }

        // If it is a delete action we can stop right here
        if ($request->query->get('action') == 'delete') {
            $commonGroundService->deleteResource($variables['resource']);

            return $this->redirect($this->generateUrl('app_vrc_labels'));
        }

        $variables['organizations'] = $commonGroundService->getResourceList(['component' => 'wrc', 'type' => 'organizations'])['hydra:member'];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])

            $variables['resource'] = $commonGroundService->saveResource($resource, (['component' => 'vrc', 'type' => 'labels']));
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
        $variables['subtitle'] = $translator->trans('all') . ' ' . $translator->trans('roles');
        $variables['resources'] = $commonGroundService->getResourceList(['component' => 'vrc', 'type' => 'roles'])['hydra:member'];

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
        if ($id == 'new') {
            $variables['resource'] = ['@id' => null, 'id' => 'new'];
        } else {
            $variables['resource'] = $commonGroundService->getResource(['component' => 'vrc', 'type' => 'roles', 'id' => $id]);
        }

        // If it is a delete action we can stop right here
        if ($request->query->get('action') == 'delete') {
            $commonGroundService->deleteResource($variables['resource']);

            return $this->redirect($this->generateUrl('app_vrc_roles'));
        }

        $variables['organizations'] = $commonGroundService->getResourceList(['component' => 'vrc', 'type' => 'organizations'])['hydra:member'];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])

            $variables['resource'] = $commonGroundService->saveResource($resource, (['component' => 'vrc', 'type' => 'roles', 'id' => $id]));
        }

        return $variables;
    }
}
