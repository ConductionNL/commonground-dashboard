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
 * Class ChrcController.
 *
 * @Route("/chrc")
 */
class ChrcController extends AbstractController
{
    /**
     * @Route("/")
     * @Template
     */
    public function indexAction(TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('Challenge Component');
        $variables['subtitle'] = $translator->trans('Component for adding challenges');

        return $variables;
    }

    /**
     * @Route("/pitches")
     * @Template
     */
    public function pitchesAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('pitches');
        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('pitches');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'chrc', 'type'=>'pitches'])['hydra:member'];

        return $variables;
    }

    /**
     * @Route("/pitches/{id}")
     * @Template
     */
    public function pitchAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
        $variables = [];

        // Lets see if we need to create
        if ($id == 'new') {
            $variables['resource'] = ['@id' => null, 'id'=>'new', 'name'=>'new'];
        } else {
            $variables['resource'] = $commonGroundService->getResource(['component'=>'chrc', 'type'=>'pitches', 'id'=> $id]);
        }

        // If it is a delete action we can stop right here
        if ($request->query->get('action') == 'delete') {
            $commonGroundService->deleteResource($variables['resource']);

            return $this->redirect($this->generateUrl('app_chrc_pitches'));
        }

        $variables['title'] = $translator->trans('pitch');
        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('pitches');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'chrc', 'type'=>'slugs'], ['organization.id'=>$id]);

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])

            $variables['resource'] = $commonGroundService->saveResource($resource, (['component'=>'chrc', 'type'=>'pitches']));

            /* @to this redirect is a hotfix */
            if (array_key_exists('id', $variables['resource'])) {
                return $this->redirect($this->generateUrl('app_chrc_pitches', ['id' =>  $variables['resource']['id']]));
            }
        }

        return $variables;
    }

    /**
     * @Route("/tenders")
     * @Template
     */
    public function tendersAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('challenges');
        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('challenges');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'chrc', 'type'=>'tenders'])['hydra:member'];

        return $variables;
    }

    /**
     * @Route("/tenders/{id}")
     * @Template
     */
    public function tenderAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
        $variables = [];

        // Lets see if we need to create
        if ($id == 'new') {
            $variables['resource'] = ['@id' => null, 'id'=>'new', 'name'=>'new'];
        } else {
            $variables['resource'] = $commonGroundService->getResource(['component'=>'chrc', 'type'=>'tenders', 'id'=> $id]);
        }

        // If it is a delete action we can stop right here
        if ($request->query->get('action') == 'delete') {
            $commonGroundService->deleteResource($variables['resource']);

            return $this->redirect($this->generateUrl('app_chrc_tenders'));
        }

        $variables['title'] = $translator->trans('challenge');
        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('tenders');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'chrc', 'type'=>'slugs'], ['organization.id'=>$id]);

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

//            var_dump($resource);
            //die;
            // If there are any sub data sources the need to be removed below in order to save the resource
            unset($resource['pitches']);

            $variables['resource'] = $commonGroundService->saveResource($resource, (['component'=>'chrc', 'type'=>'challenges', 'id'=>$id]));
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
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'chrc', 'type'=>'groups'])['hydra:member'];

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
            $variables['resource'] = ['@id' => null, 'id' => 'new', 'name' => 'new'];
        } else {
            $variables['resource'] = $commonGroundService->getResource(['component' => 'chrc', 'type' => 'groups', 'id' => $id]);
        }

        // If it is a delete action we can stop right here
        if ($request->query->get('action') == 'delete') {
            $commonGroundService->deleteResource($variables['resource']);

            return $this->redirect($this->generateUrl('app_chrc_groups'));
        }

        $variables['title'] = $translator->trans('group');
        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('groups');
        $variables['organizations'] = $commonGroundService->getResourceList(['component' => 'chrc', 'type' => 'slugs'], ['organization.id' => $id]);

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])

            $variables['resource'] = $commonGroundService->saveResource($resource, (['component' => 'chrc', 'type' => 'groups', 'id' => $id]));
        }

        return $variables;
    }

    /**
     * @Route("/tender-stages/{id}")
     * @Template
     */
    public function tenderStageAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
        $variables = [];

        // Lets see if we need to create
        if ($id == 'new') {
            $variables['resource'] = ['@id' => null, 'id'=>'new', 'name'=>'new'];
        } else {
            $variables['resource'] = $commonGroundService->getResource(['component'=>'chrc', 'type'=>'tenderStages', 'id'=> $id]);
        }

        // If it is a delete action we can stop right here
        if ($request->query->get('action') == 'delete') {
            $commonGroundService->deleteResource($variables['resource']);

            return $this->redirect($this->generateUrl('app_chrc_tenderstages'));
        }

        $variables['title'] = $translator->trans('group');
        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('tenderStages');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'chrc', 'type'=>'slugs'], ['organization.id'=>$id]);

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])

            $variables['resource'] = $commonGroundService->saveResource($resource, (['component'=>'chrc', 'type'=>'tenderStages', 'id'=>$id]));
        }

        return $variables;
    }

    /**
     * @Route("/tender-stages")
     * @Template
     */
    public function tenderStagesAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('groups');
        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('groups');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'chrc', 'type'=>'groups'])['hydra:member'];

        return $variables;
    }

    /**
     * @Route("/entries/{id}")
     * @Template
     */
    public function entryAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
        $variables = [];

        // Lets see if we need to create
        if ($id == 'new') {
            $variables['resource'] = ['@id' => null, 'id'=>'new', 'name'=>'new'];
        } else {
            $variables['resource'] = $commonGroundService->getResource(['component'=>'chrc', 'type'=>'entries', 'id'=> $id]);
        }

        // If it is a delete action we can stop right here
        if ($request->query->get('action') == 'delete') {
            $commonGroundService->deleteResource($variables['resource']);

            return $this->redirect($this->generateUrl('app_chrc_entries'));
        }

        $variables['title'] = $translator->trans('entry');
        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('entries');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'chrc', 'type'=>'slugs'], ['organization.id'=>$id]);

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])

            $variables['resource'] = $commonGroundService->saveResource($resource, (['component'=>'chrc', 'type'=>'entries', 'id'=>$id]));
        }

        return $variables;
    }

    /**
     * @Route("/entries")
     * @Template
     */
    public function entriesAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('entries');
        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('entries');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'chrc', 'type'=>'entries'])['hydra:member'];

        return $variables;
    }

    /**
     * @Route("/questions/{id}")
     * @Template
     */
    public function questionAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
        $variables = [];

        // Lets see if we need to create
        if ($id == 'new') {
            $variables['resource'] = ['@id' => null, 'id'=>'new', 'name'=>'new'];
        } else {
            $variables['resource'] = $commonGroundService->getResource(['component'=>'chrc', 'type'=>'questions', 'id'=> $id]);
        }

        // If it is a delete action we can stop right here
        if ($request->query->get('action') == 'delete') {
            $commonGroundService->deleteResource($variables['resource']);

            return $this->redirect($this->generateUrl('app_chrc_questions'));
        }

        $variables['title'] = $translator->trans('question');
        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('questions');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'chrc', 'type'=>'slugs'], ['organization.id'=>$id]);

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])

            $variables['resource'] = $commonGroundService->saveResource($resource, (['component'=>'chrc', 'type'=>'questions', 'id'=>$id]));
        }

        return $variables;
    }

    /**
     * @Route("/questions")
     * @Template
     */
    public function questionsAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('questions');
        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('questions');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'chrc', 'type'=>'questions'])['hydra:member'];

        return $variables;
    }

    /**
     * @Route("/proposals/{id}")
     * @Template
     */
    public function proposalAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
        $variables = [];

        // Lets see if we need to create
        if ($id == 'new') {
            $variables['resource'] = ['@id' => null, 'id'=>'new', 'name'=>'new'];
        } else {
            $variables['resource'] = $commonGroundService->getResource(['component'=>'chrc', 'type'=>'proposals', 'id'=> $id]);
        }

        // If it is a delete action we can stop right here
        if ($request->query->get('action') == 'delete') {
            $commonGroundService->deleteResource($variables['resource']);

            return $this->redirect($this->generateUrl('app_chrc_proposals'));
        }

        $variables['title'] = $translator->trans('proposal');
        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('proposals');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'chrc', 'type'=>'slugs'], ['organization.id'=>$id]);

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])

            $variables['resource'] = $commonGroundService->saveResource($resource, (['component'=>'chrc', 'type'=>'proposals', 'id'=>$id]));
        }

        return $variables;
    }

    /**
     * @Route("/proposals")
     * @Template
     */
    public function proposalsAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('proposals');
        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('proposals');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'chrc', 'type'=>'proposals'])['hydra:member'];

        return $variables;
    }

    /**
     * @Route("/deals/{id}")
     * @Template
     */
    public function dealAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
        $variables = [];

        // Lets see if we need to create
        if ($id == 'new') {
            $variables['resource'] = ['@id' => null, 'id'=>'new', 'name'=>'new'];
        } else {
            $variables['resource'] = $commonGroundService->getResource(['component'=>'chrc', 'type'=>'deals', 'id'=> $id]);
        }

        // If it is a delete action we can stop right here
        if ($request->query->get('action') == 'delete') {
            $commonGroundService->deleteResource($variables['resource']);

            return $this->redirect($this->generateUrl('app_chrc_deals'));
        }

        $variables['title'] = $translator->trans('deal');
        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('deals');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'chrc', 'type'=>'slugs'], ['organization.id'=>$id]);

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])

            $variables['resource'] = $commonGroundService->saveResource($resource, (['component'=>'chrc', 'type'=>'deals', 'id'=>$id]));
        }

        return $variables;
    }

    /**
     * @Route("/deals")
     * @Template
     */
    public function dealsAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('deals');
        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('deals');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'chrc', 'type'=>'deals'])['hydra:member'];

        return $variables;
    }

    /**
     * @Route("/pitch-stages/{id}")
     * @Template
     */
    public function pitchStageAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
        $variables = [];

        // Lets see if we need to create
        if ($id == 'new') {
            $variables['resource'] = ['@id' => null, 'id'=>'new', 'name'=>'new'];
        } else {
            $variables['resource'] = $commonGroundService->getResource(['component'=>'chrc', 'type'=>'pitchStages', 'id'=> $id]);
        }

        // If it is a delete action we can stop right here
        if ($request->query->get('action') == 'delete') {
            $commonGroundService->deleteResource($variables['resource']);

            return $this->redirect($this->generateUrl('app_chrc_pitchstages'));
        }

        $variables['title'] = $translator->trans('deal');
        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('pitch stages');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'chrc', 'type'=>'slugs'], ['organization.id'=>$id]);

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])

            $variables['resource'] = $commonGroundService->saveResource($resource, (['component'=>'chrc', 'type'=>'pitchStages', 'id'=>$id]));
        }

        return $variables;
    }

    /**
     * @Route("/pitch-stages")
     * @Template
     */
    public function pitchStagesAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('pitch stages');
        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('pitch stages');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'chrc', 'type'=>'pitchStages']);

        return $variables;
    }

    /**
     * @Route("/answers/{id}")
     * @Template
     */
    public function answerAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
        $variables = [];

        // Lets see if we need to create
        if ($id == 'new') {
            $variables['resource'] = ['@id' => null, 'id'=>'new', 'name'=>'new'];
        } else {
            $variables['resource'] = $commonGroundService->getResource(['component'=>'chrc', 'type'=>'answers', 'id'=> $id]);
        }

        // If it is a delete action we can stop right here
        if ($request->query->get('action') == 'delete') {
            $commonGroundService->deleteResource($variables['resource']);

            return $this->redirect($this->generateUrl('app_chrc_answers'));
        }

        $variables['title'] = $translator->trans('deal');
        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('answers');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'chrc', 'type'=>'slugs'], ['organization.id'=>$id]);

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])

            $variables['resource'] = $commonGroundService->saveResource($resource, (['component'=>'chrc', 'type'=>'answers', 'id'=>$id]));
        }

        return $variables;
    }

    /**
     * @Route("/answers")
     * @Template
     */
    public function answersAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('answers');
        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('answers');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'chrc', 'type'=>'answers'])['hydra:member'];

        return $variables;
    }
}
