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
 * Class PtcController.
 *
 * @Route("/ptc")
 */
class PtcController extends AbstractController
{
    /**
     * @Route("/")
     * @Template
     */
    public function indexAction(TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('process type catalogue');
        $variables['subtitle'] = $translator->trans('catalogues process types');

        return $variables;
    }

    /**
     * @Route("/stages")
     * @Template
     */
    public function StagesAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('stages');
        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('stages');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'ptc', 'type'=>'stages'])['hydra:member'];

        return $variables;
    }

    /**
     * @Route("/stages/{id}")
     * @Template
     */
    public function StageAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
        $variables = [];

        // Lets see if we need to create
        if ($id == 'new') {
            $variables['resource'] = ['@id' => null, 'id'=>'new', 'name'=>'new'];
        } else {
            $variables['resource'] = $commonGroundService->getResource(['component'=>'ptc', 'type'=>'stage', 'id'=> $id]);
        }

        // If it is a delete action we can stop right here
        if ($request->query->get('action') == 'delete') {
            $commonGroundService->deleteResource($variables['resource']);

            return $this->redirect($this->generateUrl('app_ptc_stages'));
        }

        $variables['title'] = $translator->trans('stage');
        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('stage');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'ptc', 'type'=>'organizations'])['hydra:member'];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])

            $variables['resource'] = $commonGroundService->saveResource($resource, (['component'=>'ptc', 'type'=>'stage']));

            /* @to this redirect is a hotfix */
            if (array_key_exists('id', $variables['resource'])) {
                return $this->redirect($this->generateUrl('app_ptc_stages', ['id' =>  $variables['resource']['id']]));
            }
        }

        return $variables;
    }

    /**
     * @Route("/process_types")
     * @Template
     */
    public function ProcessTypesAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('process types');
        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('proces types');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'ptc', 'type'=>'process_types'])['hydra:member'];

        return $variables;
    }

    /**
     * @Route("/process_types/{id}")
     * @Template
     */
    public function ProcessTypeAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
        $variables = [];

        // Lets see if we need to create
        if ($id == 'new') {
            $variables['resource'] = ['@id' => null, 'id'=>'new', 'name'=>'new'];
        } else {
            $variables['resource'] = $commonGroundService->getResource(['component'=>'ptc', 'type'=>'process_types', 'id'=> $id]);
        }

        // If it is a delete action we can stop right here
        if ($request->query->get('action') == 'delete') {
            $commonGroundService->deleteResource($variables['resource']);

            return $this->redirect($this->generateUrl('app_ptc_processtypes'));
        }

        $variables['title'] = $translator->trans('process type');
        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('process type');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'wrc', 'type'=>'organizations'])['hydra:member'];
        $variables['requestTypes'] = $commonGroundService->getResourceList(['component' => 'vtc', 'type' => 'request_types'])['hydra:member'];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            if (isset($resource['requireLogin'])) {
                $resource['requireLogin'] = true;
            } else {
                $resource['requireLogin'] = false;
            }

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])

            $variables['resource'] = $commonGroundService->saveResource($resource, (['component'=>'ptc', 'type'=>'process_types']));

            /* @to this redirect is a hotfix */
            if (array_key_exists('id', $variables['resource'])) {
                return $this->redirect($this->generateUrl('app_ptc_processtypes', ['id' =>  $variables['resource']['id']]));
            }
        }

        return $variables;
    }

    /**
     * @Route("/sections")
     * @Template
     */
    public function SectionsAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('stages');
        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('stages');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'ptc', 'type'=>'stages'])['hydra:member'];

        return $variables;
    }

    /**
     * @Route("/sections/{id}")
     * @Template
     */
    public function SectionAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
        $variables = [];

        // Lets see if we need to create
        if ($id == 'new') {
            $variables['resource'] = ['@id' => null, 'id'=>'new', 'name'=>'new'];
        } else {
            $variables['resource'] = $commonGroundService->getResource(['component'=>'ptc', 'type'=>'section', 'id'=> $id]);
        }

        // If it is a delete action we can stop right here
        if ($request->query->get('action') == 'delete') {
            $commonGroundService->deleteResource($variables['resource']);

            return $this->redirect($this->generateUrl('app_ptc_sections'));
        }

        $variables['title'] = $translator->trans('section');
        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('section');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'wrc', 'type'=>'organizations'])['hydra:member'];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])

            $variables['resource'] = $commonGroundService->saveResource($resource, (['component'=>'ptc', 'type'=>'section']));

            /* @to this redirect is a hotfix */
            if (array_key_exists('id', $variables['resource'])) {
                return $this->redirect($this->generateUrl('app_ptc_sections', ['id' =>  $variables['resource']['id']]));
            }
        }

        return $variables;
    }
}
