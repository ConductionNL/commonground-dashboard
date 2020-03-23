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
 * @Route("/zgw")
 */
class ZgwController extends AbstractController
{
	
	/**
	 * @Route("/")
	 * @Template
	 */
	public function indexAction(Request $request, CommonGroundService $commonGroundService)
	{
		$variables = [];
		$variables['title'] = $translator->trans('cases');
		$variables['subtitle'] = $translator->trans('the user component holds all users, user groups and acces rights');
		
		return $variables;
	}
	
	/**
	 * @Route("/cases")
	 * @Template
	 */
	public function casesAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
	{
		
		$variables = [];
		$variables['title'] = $translator->trans('cases');
		$variables['subtitle'] = $translator->trans('all').' '.$translator->trans('cases');
		$variables['resources'] = []; //$commonGroundService->getResourceList('https://uc.huwelijksplanner.online/users')["hydra:member"];
		
		return $variables;
	}
	
	/**
	 * @Route("/cases/{id}")
	 * @Template
	 */
	public function caseAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
	{
		$variables = [];
		$variables['title'] = $translator->trans('case');
		$variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('case');
		
		// Lets see if we need to create
		if($id == 'new'){
			$variables['resource'] = ['@id' => null];
		}
		else{
			$variables['resource'] = $commonGroundService->getResource('https://uc.huwelijksplanner.online/users/'.$id);
		}
		
		// Lets see if there is a post to procces
		if ($request->isMethod('POST')) {
			
			// Passing the variables to the resource
			$resource = $request->request->all();
			$resource['@id'] = $variables['resource']['@id'];
			
			// If there are any sub data sources the need to be removed below in order to save the resource
			// unset($resource['somedatasource'])
			
			$variables['resource'] = $commonGroundService->saveResource($resource,'https://uc.huwelijksplanner.online/users/');
		}
		
		return $variables;
	}
	
}
