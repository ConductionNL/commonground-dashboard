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
 * Class VsbeController.
 *
 * @Route("/vsbe")
 */
class VsbeController extends AbstractController
{
    /**
     * @Route("/")
     * @Template
     */
    public function indexAction(TranslatorInterface $translator)
    {
    }

    /**
     * @Route("/rules")
     * @Template
     */
    public function rulesAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('employees');
        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('employees');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'vsbe', 'type'=>'rules'])['hydra:member'];

        return $variables;
    }

    /**
     * @Route("/rules/{id}")
     * @Template
     */
    public function ruleAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
        $variables = [];

        // Lets see if we need to create
        if ($id == 'new') {
            $variables['resource'] = ['@id' => null, 'id'=>'new'];
        } else {
            $variables['resource'] = $commonGroundService->getResource(['component'=>'vsbe', 'type'=>'rules', 'id'=> $id]);
        }

        // If it is a delete action we can stop right here
        if ($request->query->get('action') == 'delete') {
            $commonGroundService->deleteResource($variables['resource']);

            return $this->redirect($this->generateUrl('app_vsbe_rules'));
        }

        $variables['title'] = $translator->trans('employee');
        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('employee');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'wrc', 'type'=>'organizations'])['hydra:member'];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            $variables['resource'] = $commonGroundService->saveResource($resource, (['component'=>'vsbe', 'type' => 'rules']));

            /* @to this redirect is a hotfix */
            if (array_key_exists('id', $variables['resource'])) {
                return $this->redirect($this->generateUrl('app_vsbe_rules', ['id' =>  $variables['resource']['id']]));
            }
        }

        return $variables;
    }
}
