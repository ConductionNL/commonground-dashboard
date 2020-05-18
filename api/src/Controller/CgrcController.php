<?php
// src/Controller/DefaultController.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use Knp\Bundle\MarkdownBundle\MarkdownParserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use App\Service\CommonGroundService;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use App\Security\User\CommongroundUser;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class CgrcController
 * @package App\Controller
 * @Route("/cgrc")
 */
class CgrcController extends AbstractController
{

	/**
	 * @Route("/")
	 * @Template
	 */
	public function indexAction(TranslatorInterface $translator)
	{
		$variables = [];
		$variables['title'] = $translator->trans('Common ground Registratie');
		$variables['subtitle'] = $translator->trans('Holds common ground registrations');

		return $variables;
	}

    /**
     * @Route("/components")
     * @Template
     */
	public function componentsAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
    	$variables = [];
    	$variables['title'] = $translator->trans('components');
    	$variables['subtitle'] = $translator->trans('all').' '.$translator->trans('components');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'cgrc','type'=>'components'])["hydra:member"];
        return $variables;
    }

    /**
     * @Route("/components/{id}")
     * @Template
     */
    public function componentAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {

    	$variables = [];

        // Lets see if we need to create
        if($id == 'new'){
            $variables['resource'] = ['@id' => null,'id'=>'new'];
        }
        else{
            $variables['resource'] = $commonGroundService->getResource(['component'=>'cgrc','type'=>'components','id'=> $id]);        }

        // If it is a delete action we can stop right here
        if($request->query->get('action') == 'delete'){
            $commonGroundService->deleteResource($variables['resource']);
            return $this->redirect($this->generateUrl('app_cgrc_components'));
        }

        $variables['title'] = $translator->trans('Component');
    	$variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('component');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'cgrc','type'=>'organizations'])["hydra:member"];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])


            $variables['resource'] = $commonGroundService->saveResource($resource,(['component'=>'cgrc','type'=>'components','id'=>$id]));
        }


        return $variables;
    }

    /**
     * @Route("/apis")
     * @Template
     */
    public function apisAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('apis');
        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('apis');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'cgrc','type'=>'apis'])["hydra:member"];
        return $variables;
    }

    /**
     * @Route("/apis/{id}")
     * @Template
     */
    public function apiAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {

        $variables = [];

        // Lets see if we need to create
        if($id == 'new'){
            $variables['resource'] = ['@id' => null,'id'=>'new'];
        }
        else{
            $variables['resource'] = $commonGroundService->getResource(['component'=>'cgrc','type'=>'apis','id'=> $id]);        }

        // If it is a delete action we can stop right here
        if($request->query->get('action') == 'delete'){
            $commonGroundService->deleteResource($variables['resource']);
            return $this->redirect($this->generateUrl('app_cgrc_apis'));
        }

        $variables['title'] = $translator->trans('Api');
        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('api');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'cgrc','type'=>'organizations'])["hydra:member"];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])


            $variables['resource'] = $commonGroundService->saveResource($resource,(['component'=>'cgrc','type'=>'apis','id'=>$id]));
        }


        return $variables;
    }

    /**
     * @Route("/component_files")
     * @Template
     */
    public function componentFilesAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('component files');
        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('component files');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'cgrc','type'=>'component_files'])["hydra:member"];
        return $variables;
    }

    /**
     * @Route("/component_files/{id}")
     * @Template
     */
    public function componentFileAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {

        $variables = [];

        // Lets see if we need to create
        if($id == 'new'){
            $variables['resource'] = ['@id' => null,'id'=>'new'];
        }
        else{
            $variables['resource'] = $commonGroundService->getResource(['component'=>'cgrc','type'=>'component_files','id'=> $id]);        }

        // If it is a delete action we can stop right here
        if($request->query->get('action') == 'delete'){
            $commonGroundService->deleteResource($variables['resource']);
            return $this->redirect($this->generateUrl('app_cgrc_component_files'));
        }

        $variables['title'] = $translator->trans('Component file');
        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('component file');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'cgrc','type'=>'organizations'])["hydra:member"];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])


            $variables['resource'] = $commonGroundService->saveResource($resource,(['component'=>'cgrc','type'=>'component_files','id'=>$id]));
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
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'cgrc','type'=>'organizations'])["hydra:member"];
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
        if($id == 'new'){
            $variables['resource'] = ['@id' => null,'id'=>'new'];
        }
        else{
            $variables['resource'] = $commonGroundService->getResource(['component'=>'cgrc','type'=>'organizations','id'=> $id]);        }

        // If it is a delete action we can stop right here
        if($request->query->get('action') == 'delete'){
            $commonGroundService->deleteResource($variables['resource']);
            return $this->redirect($this->generateUrl('app_cgrc_organizations'));
        }

        $variables['title'] = $translator->trans('Organization');
        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('organization');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'cgrc','type'=>'organizations'])["hydra:member"];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])


            $variables['resource'] = $commonGroundService->saveResource($resource,(['component'=>'cgrc','type'=>'organizations','id'=>$id]));
        }


        return $variables;
    }

}
