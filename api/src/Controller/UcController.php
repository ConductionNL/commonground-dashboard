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
	public function indexAction(Request $request, CommonGroundService $commonGroundService)
	{
		$variables = [];
		$variables['title'] = $translator->trans('user configurtion');
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
		$variables['resources'] = $commonGroundService->getResourceList('https://uc.huwelijksplanner.online/users')["hydra:member"];

		return $variables;

	}

	/**
	 * @Route("/users/{id}")
	 * @Template
	 */
	public function userAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
	{
		$variables = [];

		// Lets see if we need to create
		if($id == 'new'){
			$variables['resource'] = ['@id' => null,'name'=>'new','id'=>'new'];
		}
		else{
			$variables['resource'] = $commonGroundService->getResource('https://uc.huwelijksplanner.online/users/'.$id);
		}

		// If it is a delete action we can stop right here
		if($request->query->get('action') == 'delete'){
			$commonGroundService->deleteResource($variables['resource']);
			return $this->redirect($this->generateUrl('app_uc_users'));
		}

		$variables['title'] = $translator->trans('users');
		$variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('users');
		$variables['organizations'] = $commonGroundService->getResourceList('https://wrc.huwelijksplanner.online/organizations')["hydra:member"];

		// Lets see if there is a post to procces
		if ($request->isMethod('POST')) {

			// Passing the variables to the resource
			$resource = $request->request->all();
			$resource['@id'] = $variables['resource']['@id'];
			$resource['id'] = $variables['resource']['id'];

			// If there are any sub data sources the need to be removed below in order to save the resource
			// unset($resource['somedatasource'])

			$variables['resource'] = $commonGroundService->saveResource($resource,'https://uc.huwelijksplanner.online/users/');
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
		$variables['resources'] = $commonGroundService->getResourceList('https://uc.huwelijksplanner.online/groups')["hydra:member"];

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
		if($id == 'new'){
			$variables['resource'] = ['@id' => null,'name'=>'new','id'=>'new'];
		}
		else{
			$variables['resource'] = $commonGroundService->getResource('https://uc.huwelijksplanner.online/groups/'.$id);
		}

		// If it is a delete action we can stop right here
		if($request->query->get('action') == 'delete'){
			$commonGroundService->deleteResource($variables['resource']);
			return $this->redirect($this->generateUrl('app_uc_groups'));
		}

		$variables['title'] = $translator->trans('group');
		$variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('group');
		$variables['organizations'] = $commonGroundService->getResourceList('https://wrc.huwelijksplanner.online/organizations')["hydra:member"];

		// Lets see if there is a post to procces
		if ($request->isMethod('POST')) {

			// Passing the variables to the resource
			$resource = $request->request->all();
			$resource['@id'] = $variables['resource']['@id'];
			$resource['id'] = $variables['resource']['id'];

			// If there are any sub data sources the need to be removed below in order to save the resource
			// unset($resource['somedatasource'])

			$variables['resource'] = $commonGroundService->saveResource($resource,'https://uc.huwelijksplanner.online/groups/');
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
		$variables['resources'] = $commonGroundService->getResourceList('https://uc.huwelijksplanner.online/scopes')["hydra:member"];

		return $variables;

	}

	/**
	 * @Route("/scopes/{id}")
	 * @Template
	 */
	public function scopeAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
	{
		$variables = [];

		// Lets see if we need to create
		if($id == 'new'){
			$variables['resource'] = ['@id' => null,'name'=>'new','id'=>'new'];
		}
		else{
			$variables['resource'] = $commonGroundService->getResource('https://uc.huwelijksplanner.online/scopes/'.$id);
		}

		// If it is a delete action we can stop right here
		if($request->query->get('action') == 'delete'){
			$commonGroundService->deleteResource($variables['resource']);
			return $this->redirect($this->generateUrl('app_uc_scopes'));
		}

		$variables['title'] = $translator->trans('scope');
		$variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('scope');
		$variables['organizations'] = $commonGroundService->getResourceList('https://wrc.huwelijksplanner.online/organizations')["hydra:member"];

		// Lets see if there is a post to procces
		if ($request->isMethod('POST')) {

			// Passing the variables to the resource
			$resource = $request->request->all();
			$resource['@id'] = $variables['resource']['@id'];
			$resource['id'] = $variables['resource']['id'];

			// If there are any sub data sources the need to be removed below in order to save the resource
			// unset($resource['somedatasource'])

			$variables['resource'] = $commonGroundService->saveResource($resource,'https://uc.huwelijksplanner.online/scopes/');
		}
		return $variables;
	}
}
