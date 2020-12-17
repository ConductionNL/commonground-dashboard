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
 * Class TcController.
 *
 * @Route("/tc")
 */
class TcController extends AbstractController
{
    /**
     * @Route("/")
     * @Template
     */
    public function indexAction(TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('tasks');
        $variables['subtitle'] = $translator->trans('the tasks component holds tasks');

        return $variables;
    }

    /**
     * @Route("/tasks")
     * @Template
     */
    public function tasksAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('tasks');
        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('tasks');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'tc', 'type'=>'tasks'])['hydra:member'];

        return $variables;
    }

    /**
     * @Route("/tasks/{id}")
     * @Template
     */
    public function taskAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
        $variables = [];

        // Lets see if we need to create
        if ($id == 'new') {
            $variables['resource'] = ['@id' => null, 'id'=>'new', 'name'=>'new'];
        } else {
            $variables['resource'] = $commonGroundService->getResource(['component'=>'tc', 'type'=>'tasks', 'id'=> $id]);
        }

        // If it is a delete action we can stop right here
        if ($request->query->get('action') == 'delete') {
            $commonGroundService->deleteResource($variables['resource']);

            return $this->redirect($this->generateUrl('app_tc_tasks'));
        }

        $variables['title'] = $translator->trans('task');
        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('task');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'wrc', 'type'=>'organizations'])['hydra:member'];
        $variables['requests'] = $commonGroundService->getResourceList(['component'=>'vrc', 'type'=>'requests'])['hydra:member'];
        $variables['people'] = $commonGroundService->getResourceList(['component'=>'cc', 'type'=>'people'])['hydra:member'];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            $resource['priority'] = (int) $resource['priority'];
            $resource['percentageDone'] = (int) $resource['percentageDone'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])

            $variables['resource'] = $commonGroundService->saveResource($resource, (['component'=>'tc', 'type'=>'tasks']));

            /* @to this redirect is a hotfix */
            if (array_key_exists('id', $variables['resource'])) {
                return $this->redirect($this->generateUrl('app_tc_tasks', ['id' =>  $variables['resource']['id']]));
            }
        }

        return $variables;
    }
}
