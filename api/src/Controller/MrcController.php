<?php
// src/Controller/DefaultController.php
namespace App\Controller;

use Conduction\CommonGroundBundle\Service\CommonGroundService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use Knp\Bundle\MarkdownBundle\MarkdownParserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Conduction\CommonGroundBundle\Security\User\CommongroundUser;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class MrcController
 * @package App\Controller
 * @Route("/mrc")
 */
class MrcController extends AbstractController
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
     * @Route("/employees")
     * @Template
     */
	public function employeesAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
    	$variables = [];
    	$variables['title'] = $translator->trans('employees');
    	$variables['subtitle'] = $translator->trans('all').' '.$translator->trans('employees');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'mrc','type'=>'employees'])["hydra:member"];

        return $variables;
    }

    /**
     * @Route("/employees/{id}")
     * @Template
     */
    public function employeeAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {

    	$variables = [];

        // Lets see if we need to create
        if($id == 'new'){
            $variables['resource'] = ['@id' => null,'id'=>'new'];
        }
        else{
            $variables['resource'] = $commonGroundService->getResource(['component'=>'mrc','type'=>'employees','id'=> $id]);

        }

        // If it is a delete action we can stop right here
        if($request->query->get('action') == 'delete'){
            $commonGroundService->deleteResource($variables['resource']);
            return $this->redirect($this->generateUrl('app_mrc_employees'));
        }

        $variables['title'] = $translator->trans('employee');
    	$variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('employee');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'wrc','type'=>'organizations'])["hydra:member"];
        $variables['people'] = $commonGroundService->getResourceList(['component'=>'cc','type'=>'people'])["hydra:member"];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])

            $variables['resource'] = $commonGroundService->saveResource($resource,(['component'=>'mrc','type'=>'employees']));

            /* @to this redirect is a hotfix */
            if(array_key_exists('id', $variables['resource'])){
                return $this->redirect($this->generateUrl('app_mrc_employees', ["id" =>  $variables['resource']['id']]));
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
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'mrc','type'=>'applications'])["hydra:member"];
        return $variables;
    }

    /**
     * @Route("/applications/{id}")
     * @Template
     */
    public function applicationAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {

        $variables = [];

        // Lets see if we need to create
        if($id == 'new'){
            $variables['resource'] = ['@id' => null,'id'=>'new','name'=>'new'];
        }
        else{
            $variables['resource'] = $commonGroundService->getResource(['component'=>'mrc','type'=>'applications','id'=> $id]);        }

        // If it is a delete action we can stop right here
        if($request->query->get('action') == 'delete'){
            $commonGroundService->deleteResource($variables['resource']);
            return $this->redirect($this->generateUrl('app_mrc_applications'));
        }

        $variables['title'] = $translator->trans('application');
        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('application');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'wrc','type'=>'organizations'])["hydra:member"];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])


            $variables['resource'] = $commonGroundService->saveResource($resource,(['component'=>'mrc','type'=>'applications']));

            /* @to this redirect is a hotfix */
            if(array_key_exists('id', $variables['resource'])){
                return $this->redirect($this->generateUrl('app_mrc_applications', ["id" =>  $variables['resource']['id']]));
            }
        }


        return $variables;
    }

    /**
     * @Route("/competences")
     * @Template
     */
    public function competencesAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('competences');
        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('competences');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'mrc','type'=>'competences'])["hydra:member"];
        return $variables;
    }

    /**
     * @Route("/competences/{id}")
     * @Template
     */
    public function competenceAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {

        $variables = [];

        // Lets see if we need to create
        if($id == 'new'){
            $variables['resource'] = ['@id' => null,'id'=>'new','name'=>'new'];
        }
        else{
            $variables['resource'] = $commonGroundService->getResource(['component'=>'mrc','type'=>'competences','id'=> $id]);        }

        // If it is a delete action we can stop right here
        if($request->query->get('action') == 'delete'){
            $commonGroundService->deleteResource($variables['resource']);
            return $this->redirect($this->generateUrl('app_mrc_competences'));
        }

        $variables['title'] = $translator->trans('competence');
        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('competence');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'wrc','type'=>'organizations'])["hydra:member"];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])


            $variables['resource'] = $commonGroundService->saveResource($resource,(['component'=>'mrc','type'=>'competences']));

            /* @to this redirect is a hotfix */
            if(array_key_exists('id', $variables['resource'])){
                return $this->redirect($this->generateUrl('app_mrc_competences', ["id" =>  $variables['resource']['id']]));
            }
        }


        return $variables;
    }

    /**
     * @Route("/contracts")
     * @Template
     */
    public function contractsAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('contracts');
        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('contracts');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'mrc','type'=>'contracts'])["hydra:member"];
        return $variables;
    }

    /**
     * @Route("/contracts/{id}")
     * @Template
     */
    public function contractAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {

        $variables = [];

        // Lets see if we need to create
        if($id == 'new'){
            $variables['resource'] = ['@id' => null,'id'=>'new','name'=>'new'];
        }
        else{
            $variables['resource'] = $commonGroundService->getResource(['component'=>'mrc','type'=>'contracts','id'=> $id]);        }

        // If it is a delete action we can stop right here
        if($request->query->get('action') == 'delete'){
            $commonGroundService->deleteResource($variables['resource']);
            return $this->redirect($this->generateUrl('app_mrc_contracts'));
        }

        $variables['title'] = $translator->trans('contract');
        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('contract');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'wrc','type'=>'organizations'])["hydra:member"];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])


            $variables['resource'] = $commonGroundService->saveResource($resource,(['component'=>'mrc','type'=>'contracts']));

            /* @to this redirect is a hotfix */
            if(array_key_exists('id', $variables['resource'])){
                return $this->redirect($this->generateUrl('app_mrc_contracts', ["id" =>  $variables['resource']['id']]));
            }
        }


        return $variables;
    }

    /**
     * @Route("/interests")
     * @Template
     */
    public function interestsAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('applications');
        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('interests');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'mrc','type'=>'interests'])["hydra:member"];
        return $variables;
    }

    /**
     * @Route("/interests/{id}")
     * @Template
     */
    public function interestAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {

        $variables = [];

        // Lets see if we need to create
        if($id == 'new'){
            $variables['resource'] = ['@id' => null,'id'=>'new','name'=>'new'];
        }
        else{
            $variables['resource'] = $commonGroundService->getResource(['component'=>'mrc','type'=>'interests','id'=> $id]);        }

        // If it is a delete action we can stop right here
        if($request->query->get('action') == 'delete'){
            $commonGroundService->deleteResource($variables['resource']);
            return $this->redirect($this->generateUrl('app_mrc_interests'));
        }

        $variables['title'] = $translator->trans('interest');
        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('interest');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'wrc','type'=>'organizations'])["hydra:member"];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])


            $variables['resource'] = $commonGroundService->saveResource($resource,(['component'=>'mrc','type'=>'interests']));

            /* @to this redirect is a hotfix */
            if(array_key_exists('id', $variables['resource'])){
                return $this->redirect($this->generateUrl('app_mrc_interests', ["id" =>  $variables['resource']['id']]));
            }
        }


        return $variables;
    }

    /**
     * @Route("/job_functions")
     * @Template
     */
    public function jobFunctionsAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('job functions');
        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('job functions');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'mrc','type'=>'job_functions'])["hydra:member"];
        return $variables;
    }

    /**
     * @Route("/job_functions/{id}")
     * @Template
     */
    public function jobFunctionAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {

        $variables = [];

        // Lets see if we need to create
        if($id == 'new'){
            $variables['resource'] = ['@id' => null,'id'=>'new','name'=>'new'];
        }
        else{
            $variables['resource'] = $commonGroundService->getResource(['component'=>'mrc','type'=>'job_functions','id'=> $id]);        }

        // If it is a delete action we can stop right here
        if($request->query->get('action') == 'delete'){
            $commonGroundService->deleteResource($variables['resource']);
            return $this->redirect($this->generateUrl('app_mrc_jobfunctions'));
        }

        $variables['title'] = $translator->trans('job function');
        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('job function');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'wrc','type'=>'organizations'])["hydra:member"];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])


            $variables['resource'] = $commonGroundService->saveResource($resource,(['component'=>'mrc','type'=>'job_functions']));

            /* @to this redirect is a hotfix */
            if(array_key_exists('id', $variables['resource'])){
                return $this->redirect($this->generateUrl('app_mrc_jobfunctions', ["id" =>  $variables['resource']['id']]));
            }
        }


        return $variables;
    }

    /**
     * @Route("/job_postings")
     * @Template
     */
    public function JobPostingsAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('job postings');
        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('job postings');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'mrc','type'=>'job_postings'])["hydra:member"];
        return $variables;
    }

    /**
     * @Route("/job_postings/{id}")
     * @Template
     */
    public function jobPostingAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {

        $variables = [];

        // Lets see if we need to create
        if($id == 'new'){
            $variables['resource'] = ['@id' => null,'id'=>'new','name'=>'new'];
        }
        else{
            $variables['resource'] = $commonGroundService->getResource(['component'=>'mrc','type'=>'job_postings','id'=> $id]);        }

        // If it is a delete action we can stop right here
        if($request->query->get('action') == 'delete'){
            $commonGroundService->deleteResource($variables['resource']);
            return $this->redirect($this->generateUrl('app_mrc_jobpostings'));
        }

        $variables['title'] = $translator->trans('job Posting');
        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('job Posting');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'wrc','type'=>'organizations'])["hydra:member"];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])


            $variables['resource'] = $commonGroundService->saveResource($resource,(['component'=>'mrc','type'=>'job_postings']));

            /* @to this redirect is a hotfix */
            if(array_key_exists('id', $variables['resource'])){
                return $this->redirect($this->generateUrl('app_mrc_jobpostings', ["id" =>  $variables['resource']['id']]));
            }
        }


        return $variables;
    }

    /**
     * @Route("/skills")
     * @Template
     */
    public function skillsAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('skills');
        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('skills');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'mrc','type'=>'skills'])["hydra:member"];
        return $variables;
    }

    /**
     * @Route("/skills/{id}")
     * @Template
     */
    public function skillAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {

        $variables = [];

        // Lets see if we need to create
        if($id == 'new'){
            $variables['resource'] = ['@id' => null,'id'=>'new','name'=>'new'];
        }
        else{
            $variables['resource'] = $commonGroundService->getResource(['component'=>'mrc','type'=>'skills','id'=> $id]);        }

        // If it is a delete action we can stop right here
        if($request->query->get('action') == 'delete'){
            $commonGroundService->deleteResource($variables['resource']);
            return $this->redirect($this->generateUrl('app_mrc_skills'));
        }

        $variables['title'] = $translator->trans('skill');
        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('skill');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'wrc','type'=>'organizations'])["hydra:member"];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])


            $variables['resource'] = $commonGroundService->saveResource($resource,(['component'=>'mrc','type'=>'skills']));

            /* @to this redirect is a hotfix */
            if(array_key_exists('id', $variables['resource'])){
                return $this->redirect($this->generateUrl('app_mrc_skills', ["id" =>  $variables['resource']['id']]));
            }
        }


        return $variables;
    }
}
