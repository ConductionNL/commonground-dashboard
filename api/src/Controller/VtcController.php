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
 * @Route("/vtc")
 */
class VtcController extends AbstractController
{

	/**
	 * @Route("/")
	 * @Template
	 */
	public function indexAction()
	{
		$variables = [];
		$variables['title'] = $translator->trans('product and service catalouge');
		$variables['subtitle'] = $translator->trans('the product and service catalouge holds al data concerning product, groups and offers');

		return $variables;
	}


	/**
	 * @Route("/request_types")
	 * @Template
	 */
	public function requestTypesAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
	{

		$variables = [];
		$variables['title'] = $translator->trans('request types');
		$variables['subtitle'] = $translator->trans('all').' '.$translator->trans('request types');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'vtc','type'=>'request_types'])["hydra:member"];

		return $variables;

	}

	/**
	 * @Route("/request_types/{id}")
	 * @Template
	 */
	public function requestTypeAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
	{
		$variables = [];

		// Lets see if we need to create
		if($id == 'new'){
			$variables['resource'] = ['@id' => null,'name'=>'new','id'=>'new'];
		}
		else{
			$variables['resource'] = $commonGroundService->getResource('https://vtc.huwelijksplanner.online/request_types/'.$id);
            $variables['changeLog'] = $commonGroundService->getResourceList('https://vtc.huwelijksplanner.online/request_types/'.$id.'/change_log');
            $variables['auditTrail'] = $commonGroundService->getResourceList('https://vtc.huwelijksplanner.online/request_types/'.$id.'/audit_trail');
		}

		// If it is a delete action we can stop right here
		if($request->query->get('action') == 'delete'){
			$commonGroundService->deleteResource($variables['resource']);
			return $this->redirect($this->generateUrl('app_vtc_requesttypes'));
		}

		$variables['title'] = $translator->trans('request type');
		$variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('request type');
		$variables['organizations'] = $commonGroundService->getResourceList('https://wrc.huwelijksplanner.online/organizations')["hydra:member"];

		// Lets see if there is a post to procces
		if ($request->isMethod('POST')) {

			// Passing the variables to the resource
			$resource = $request->request->all();
			$resource['@id'] = $variables['resource']['@id'];
			$resource['id'] = $variables['resource']['id'];

			// If there are any sub data sources the need to be removed below in order to save the resource
			// unset($resource['somedatasource'])

			$variables['resource'] = $commonGroundService->saveResource($resource,'https://vtc.huwelijksplanner.online/request_types/');
		}
		return $variables;
	}
}
