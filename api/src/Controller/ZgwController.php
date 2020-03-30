<?php
// src/Controller/DefaultController.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Translation\TranslatorInterface;

use App\Service\ZgwService;
use App\Service\CommonGroundService;

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
	public function indexAction(Request $request, ZgwService $zgwService)
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
	public function casesAction(ZgwService $zgwService, TranslatorInterface $translator)
	{
		
		$variables = [];
		$variables['title'] = $translator->trans('cases');
		$variables['subtitle'] = $translator->trans('all').' '.$translator->trans('cases');
		$variables['resources'] = $zgwService->getResourceList('http://openzaak.utrechtproeftuin.nl/zaken/api/v1/zaken')["results"];
				
		return $variables;
	}
	
	/**
	 * @Route("/cases/{id}")
	 * @Template
	 */
	public function caseAction(Request $request, CommonGroundService $commonGroundService, ZgwService $zgwService, TranslatorInterface $translator, $id)
	{
		$variables = [];
		
		// Lets see if we need to create
		if($id == 'new'){
			$variables['resource'] = ['@id' => null,'omschrijving'=>'nieuwe zaak','id'=>'new'];
		}
		else{
			$variables['resource'] = $zgwService->getResource('https://openzaak.utrechtproeftuin.nl/zaken/api/v1/zaken/'.$id);
		}
		
		
		// If it is a delete action we can stop right here
		if($request->query->get('action') == 'delete'){
			$commonGroundService->deleteResource($variables['resource']);
			var_dump($request->query->get('action'));
			die;
			return $this->redirect($this->generateUrl('app_uc_users'));
		}
		
		$variables['title'] = $translator->trans('case');
		$variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('case');
		$variables['organizations'] = $commonGroundService->getResourceList('https://wrc.huwelijksplanner.online/organizations')["hydra:member"];
		$variables['casetypes'] = $zgwService->getResourceList('https://openzaak.utrechtproeftuin.nl/catalogi/api/v1/zaaktypen')["results"];
		
		// Lets see if there is a post to procces
		if ($request->isMethod('POST')) {
			
			// Passing the variables to the resource
			$resource = $request->request->all();
			$resource['@id'] = $variables['resource']['@id'];
			
			// Zaak specifieke shizle
			if(!key_exists('startdatum', $resource)){
				$resource['startdatum'] = date('Y-m-d');
			}
			
			// If there are any sub data sources the need to be removed below in order to save the resource
			// unset($resource['somedatasource'])
			
			$variables['resource'] = $zgwService->saveResource($resource,'https://openzaak.utrechtproeftuin.nl/zaken/api/v1/zaken', false);
		}
		
		return $variables;
	}	
	
	/**
	 * @Route("/case_types")
	 * @Template
	 */
	public function caseTypesAction(ZgwService $zgwService, TranslatorInterface $translator)
	{
		
		$variables = [];
		$variables['title'] = $translator->trans('cases');
		$variables['subtitle'] = $translator->trans('all').' '.$translator->trans('cases');
		$variables['resources'] = $zgwService->getResourceList('http://openzaak.utrechtproeftuin.nl/catalogi/api/v1/zaaktypen')["results"];
		
		//var_dump($variables['resources']);
		
		return $variables;
	}
	
	/**
	 * @Route("/case_types/{id}")
	 * @Template
	 */
	public function caseTypeAction(Request $request, ZgwService $zgwService, TranslatorInterface $translator, $id)
	{
		$variables = [];
		$variables['title'] = $translator->trans('case type');
		$variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('case type');
		
		// Lets see if we need to create
		if($id == 'new'){
			$variables['resource'] = ['@id' => null];
		}
		else{
			$variables['resource'] = $zgwService->getResource('http://openzaak.utrechtproeftuin.nl/catalogi/api/v1/zaaktypen/'.$id);
		}
		
		// Lets see if there is a post to procces
		if ($request->isMethod('POST')) {
			
			// Passing the variables to the resource
			$resource = $request->request->all();
			$resource['@id'] = $variables['resource']['@id'];
			
			// If there are any sub data sources the need to be removed below in order to save the resource
			// unset($resource['somedatasource'])
			
			$variables['resource'] = $zgwService->saveResource($resource,'http://openzaak.utrechtproeftuin.nl/catalogi/api/v1/zaaktypen');
		}
		
		return $variables;
	}
		
	/**
	 * @Route("/documents")
	 * @Template
	 */
	public function documentsAction(ZgwService $zgwService, TranslatorInterface $translator)
	{
		
		$variables = [];
		$variables['title'] = $translator->trans('case types');
		$variables['subtitle'] = $translator->trans('all').' '.$translator->trans('case types');
		$variables['resources'] = $zgwService->getResourceList('http://openzaak.utrechtproeftuin.nl/catalogi/api/v1/zaaktypen')["results"];
		
		return $variables;
	}
	
	/**
	 * @Route("/documents/{id}")
	 * @Template
	 */
	public function documentAction(Request $request, ZgwService $zgwService, TranslatorInterface $translator, $id)
	{
		$variables = [];
		$variables['title'] = $translator->trans('document');
		$variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('documents');
		
		// Lets see if we need to create
		if($id == 'new'){
			$variables['resource'] = ['@id' => null];
		}
		else{
			$variables['resource'] = $zgwService->getResource('http://openzaak.utrechtproeftuin.nl/catalogi/api/v1/zaaktypen'.$id);
		}
		
		// Lets see if there is a post to procces
		if ($request->isMethod('POST')) {
			
			// Passing the variables to the resource
			$resource = $request->request->all();
			$resource['@id'] = $variables['resource']['@id'];
			
			// If there are any sub data sources the need to be removed below in order to save the resource
			// unset($resource['somedatasource'])
			
			$variables['resource'] = $zgwService->saveResource($resource,'http://openzaak.utrechtproeftuin.nl/catalogi/api/v1/zaaktypen');
		}
		
		return $variables;
	}
	
	/**
	 * @Route("/verdicts")
	 * @Template
	 */
	public function verdictsAction(ZgwService $zgwService, TranslatorInterface $translator)
	{
		
		$variables = [];
		$variables['title'] = $translator->trans('verdict');
		$variables['subtitle'] = $translator->trans('all').' '.$translator->trans('verdicts');
		$variables['resources'] = $zgwService->getResourceList('http://openzaak.utrechtproeftuin.nl/catalogi/api/v1/zaaktypen')["results"];
		
		return $variables;
	}
	
	/**
	 * @Route("/verdicts/{id}")
	 * @Template
	 */
	public function verdictAction(Request $request, ZgwService $zgwService, TranslatorInterface $translator, $id)
	{
		$variables = [];
		$variables['title'] = $translator->trans('verdict');
		$variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('verdict');
		
		// Lets see if we need to create
		if($id == 'new'){
			$variables['resource'] = ['@id' => null];
		}
		else{
			$variables['resource'] = $zgwService->getResource('http://openzaak.utrechtproeftuin.nl/catalogi/api/v1/zaaktypen'.$id);
		}
		
		// Lets see if there is a post to procces
		if ($request->isMethod('POST')) {
			
			// Passing the variables to the resource
			$resource = $request->request->all();
			$resource['@id'] = $variables['resource']['@id'];
			
			// If there are any sub data sources the need to be removed below in order to save the resource
			// unset($resource['somedatasource'])
			
			$variables['resource'] = $zgwService->saveResource($resource,'http://openzaak.utrechtproeftuin.nl/catalogi/api/v1/zaaktypen');
		}
		
		return $variables;
	}
}
