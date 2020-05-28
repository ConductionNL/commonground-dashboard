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
 * Class PdcController
 * @package App\Controller
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
		$variables['resources'] = $commonGroundService->getResourceList(['component'=>'uc','type'=>'users'])["hydra:member"];

		return $variables;

	}

	/**
	 * @Route("/users/{id}")
	 * @Template
	 */
	public function userAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
	{
        // If it is a delete action we can stop right here
        if($request->query->get('action') == 'delete'){
            $commonGroundService->deleteResource(null,['component'=>'uc','type'=>'users','id'=>$id]);
            return $this->redirect($this->generateUrl('app_uc_users'));
        }

		$variables = [];

		// Lets see if we need to create
		if($id == 'new'){
			$variables['resource'] = ['@id' => null,'name'=>'new','id'=>'new'];
		}
		else{
			$variables['resource'] = $commonGroundService->getResource(['component'=>'uc','type'=>'users','id'=>$id]);
		}

		$variables['title'] = $translator->trans('users');
		$variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('users');
		$variables['organizations'] = $commonGroundService->getResourceList(['component'=>'wrc','type'=>'organizations'])["hydra:member"];
        $variables['groups'] = $commonGroundService->getResourceList(['component'=>'uc','type'=>'groups'])["hydra:member"];

		// Lets see if there is a post to procces
		if ($request->isMethod('POST')) {

			// Passing the variables to the resource
			$resource = $request->request->all();
			$resource['@id'] = $variables['resource']['@id'];
			$resource['id'] = $variables['resource']['id'];

			// If there are any sub data sources the need to be removed below in order to save the resource
			// unset($resource['somedatasource'])

			$variables['resource'] = $commonGroundService->saveResource($resource,['component'=>'uc','type'=>'users']);
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
		$variables['resources'] = $commonGroundService->getResourceList(['component'=>'uc','type'=>'groups'])["hydra:member"];

		return $variables;

	}

	/**
	 * @Route("/groups/{id}")
	 * @Template
	 */
	public function groupAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
	{
        // If it is a delete action we can stop right here
        if($request->query->get('action') == 'delete'){
            $commonGroundService->deleteResource(null,['component'=>'uc','type'=>'groups','id'=>$id]);
            return $this->redirect($this->generateUrl('app_uc_groups'));
        }
		$variables = [];

		// Lets see if we need to create
		if($id == 'new'){
			$variables['resource'] = ['@id' => null,'name'=>'new','id'=>'new'];
		}
		else{
			$variables['resource'] = $commonGroundService->getResource(['component'=>'uc','type'=>'groups','id'=>$id]);
		}

		$variables['title'] = $translator->trans('group');
		$variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('group');
		$variables['organizations'] = $commonGroundService->getResourceList(['component'=>'wrc','type'=>'organizations'])["hydra:member"];
        $variables['scopes'] = $commonGroundService->getResourceList(['component'=>'uc','type'=>'scopes'])["hydra:member"];

		// Lets see if there is a post to procces
		if ($request->isMethod('POST')) {

			// Passing the variables to the resource
			$resource = $request->request->all();
			$resource['@id'] = $variables['resource']['@id'];
			$resource['id'] = $variables['resource']['id'];


            // Lets see if we also need to add an configuration
            if(array_key_exists('scope', $resource)){
                $scope = $resource['scope'];

                // The resource action section
                if(key_exists("@id",$scope) && key_exists("action",$scope)){


                }

                $scope = $commonGroundService->saveResource($scope, ['component'=>'wrc','type'=>'scope']);
                $variables['templates'] = $commonGroundService->getResourceList(['component'=>'wrc','type'=>'templates'],['application.id'=>$id])["hydra:member"];
            }

			$variables['resource'] = $commonGroundService->saveResource($resource,['component'=>'uc','type'=>'groups']);
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
		$variables['resources'] = $commonGroundService->getResourceList(['component'=>'uc','type'=>'scopes'])["hydra:member"];

		return $variables;

	}

	/**
	 * @Route("/scopes/{id}")
	 * @Template
	 */
	public function scopeAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
	{
        // If it is a delete action we can stop right here
        if($request->query->get('action') == 'delete'){
            $commonGroundService->deleteResource(null,['component'=>'uc','type'=>'scopes','id'=>$id]);
            return $this->redirect($this->generateUrl('app_uc_scopes'));
        }

		$variables = [];

		// Lets see if we need to create
		if($id == 'new'){
			$variables['resource'] = ['@id' => null,'name'=>'new','id'=>'new'];
		}
		else{
			$variables['resource'] = $commonGroundService->getResource(['component'=>'uc','type'=>'scopes','id'=>$id]);
		}

		// If it is a delete action we can stop right here
		if($request->query->get('action') == 'delete'){
			$commonGroundService->deleteResource($variables['resource']);
			return $this->redirect($this->generateUrl('app_uc_scopes'));
		}

		$variables['title'] = $translator->trans('scope');
		$variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('scope');
		$variables['organizations'] = $commonGroundService->getResourceList(['component'=>'wrc','type'=>'organizations'])["hydra:member"];
        $variables['applications'] = $commonGroundService->getResourceList(['component'=>'wrc','type'=>'applications'])["hydra:member"];

		// Lets see if there is a post to procces
		if ($request->isMethod('POST')) {

			// Passing the variables to the resource
			$resource = $request->request->all();
			$resource['@id'] = $variables['resource']['@id'];
			$resource['id'] = $variables['resource']['id'];

			// If there are any sub data sources the need to be removed below in order to save the resource
			// unset($resource['somedatasource'])

			$variables['resource'] = $commonGroundService->saveResource($resource,['component'=>'uc','type'=>'scopes']);
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
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'uc','type'=>'applications'])["hydra:member"];

        return $variables;

    }

    /**
     * @Route("/applications/{id}")
     * @Template
     */
    public function applicationAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
        // If it is a delete action we can stop right here
        if($request->query->get('action') == 'delete'){
            $commonGroundService->deleteResource(null,['component'=>'uc','type'=>'applications','id'=>$id]);
            return $this->redirect($this->generateUrl('app_uc_applications'));
        }
        $variables = [];

        // Lets see if we need to create
        if($id == 'new'){
            $variables['resource'] = ['@id' => null,'name'=>'new','id'=>'new'];
        }
        else{
            $variables['resource'] = $commonGroundService->getResource(['component'=>'uc','type'=>'applications','id'=>$id]);
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


            // Lets see if we also need to add an configuration

            $variables['resource'] = $commonGroundService->saveResource($resource,['component'=>'uc','type'=>'applications']);
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
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'uc','type'=>'providers'])["hydra:member"];

        return $variables;

    }

    /**
     * @Route("/providers/{id}")
     * @Template
     */
    public function providerAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
        // If it is a delete action we can stop right here
        if($request->query->get('action') == 'delete'){
            $commonGroundService->deleteResource(null,['component'=>'uc','type'=>'providers','id'=>$id]);
            return $this->redirect($this->generateUrl('app_uc_providers'));
        }
        $variables = [];

        // Lets see if we need to create
        if($id == 'new'){
            $variables['resource'] = ['@id' => null,'name'=>'new','id'=>'new'];
        }
        else{
            $variables['resource'] = $commonGroundService->getResource(['component'=>'uc','type'=>'providers','id'=>$id]);
        }

        $variables['title'] = $translator->trans('provider');
        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('provider');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'wrc','type'=>'organizations'])["hydra:member"];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];


            // Lets see if we also need to add an configuration

            $variables['resource'] = $commonGroundService->saveResource($resource,['component'=>'uc','type'=>'providers']);
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
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'uc','type'=>'tokens'])["hydra:member"];

        return $variables;

    }

    /**
     * @Route("/tokens/{id}")
     * @Template
     */
    public function tokenAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
        // If it is a delete action we can stop right here
        if($request->query->get('action') == 'delete'){
            $commonGroundService->deleteResource(null,['component'=>'uc','type'=>'tokens','id'=>$id]);
            return $this->redirect($this->generateUrl('app_uc_tokens'));
        }
        $variables = [];

        // Lets see if we need to create
        if($id == 'new'){
            $variables['resource'] = ['@id' => null,'name'=>'new','id'=>'new'];
        }
        else{
            $variables['resource'] = $commonGroundService->getResource(['component'=>'uc','type'=>'tokens','id'=>$id]);
        }

        $variables['title'] = $translator->trans('token');
        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('token');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'wrc','type'=>'organizations'])["hydra:member"];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];


            // Lets see if we also need to add an configuration

            $variables['resource'] = $commonGroundService->saveResource($resource,['component'=>'uc','type'=>'tokens']);
        }
        return $variables;
    }
}
