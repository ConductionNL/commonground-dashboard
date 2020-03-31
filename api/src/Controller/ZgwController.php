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
			$variables['resource'] = ['@id' => null, 'omschrijving'=>'nieuwe zaak','id'=>'new'];
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
		
		if(array_key_exists('@id',$variables['resource'])){
			$variables['statuses'] = $zgwService->getResourceList('https://openzaak.utrechtproeftuin.nl/zaken/api/v1/statussen',['zaak'=>$variables['resource']['@id']])["results"];
			$variables['results'] = $zgwService->getResourceList('https://openzaak.utrechtproeftuin.nl/zaken/api/v1/resultaten',['zaak'=>$variables['resource']['@id']])["results"];
			$variables['roles'] = $zgwService->getResourceList('https://openzaak.utrechtproeftuin.nl/zaken/api/v1/rollen',['zaak'=>$variables['resource']['@id']])["results"];
			//$variables['verdics'] = $zgwService->getResourceList('https://openzaak.utrechtproeftuin.nl/besluiten/api/v1/besluiten',['zaak'=>$variables['resource']['@id']])["results"];
			
		}
		
		if(array_key_exists('zaaktype', $variables['resource'])){			
			$variables['casestatuses'] = $zgwService->getResourceList('https://openzaak.utrechtproeftuin.nl/catalogi/api/v1/statustypen',['zaaktype'=>$variables['resource']['zaaktype']])["results"];
			$variables['caseresults'] = $zgwService->getResourceList('https://openzaak.utrechtproeftuin.nl/catalogi/api/v1/resultaattypen',['zaaktype'=>$variables['resource']['zaaktype']])["results"];
			$variables['caseroles'] = $zgwService->getResourceList('https://openzaak.utrechtproeftuin.nl/catalogi/api/v1/roltypen',['zaaktype'=>$variables['resource']['zaaktype']])["results"];
			$variables['caseverdics'] = $zgwService->getResourceList('https://openzaak.utrechtproeftuin.nl/catalogi/api/v1/besluittypen',['zaaktypen'=>$variables['resource']['zaaktype']])["results"];
			
			$variables['caseproperties'] = $zgwService->getResourceList('https://openzaak.utrechtproeftuin.nl/catalogi/api/v1/eigenschappen',['zaaktype'=>$variables['resource']['zaaktype']])["results"];
		}
		
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
			
			// @todo This wouldn't be necessary on an update request, then we could just run it
			if(!key_exists('zaakobject', $resource) && !key_exists('status', $resource) && !key_exists('result', $resource)){
				$variables['resource'] = $zgwService->saveResource($resource,'https://openzaak.utrechtproeftuin.nl/zaken/api/v1/zaken', false);
			}
			
			// We might also want to create a zaakObject resource			
			if(key_exists('zaakobject', $resource)){
				$zaakobject = $resource['zaakobject'];
				$zaakobject['zaak'] = $resource['@id'];
				
				$zaakobject = $zgwService->saveResource($zaakobject,'https://openzaak.utrechtproeftuin.nl/zaken/api/v1/zaakobject', false);
			}
			
			// We might also want to create a zaakObject resource
			if(key_exists('status', $resource)){
				$status = $resource['status'];
				$status['zaak'] = $resource['@id'];
				$status['datumStatusGezet'] = date('Y-m-d H:i:s');
				
				$status = $zgwService->saveResource($status,'https://openzaak.utrechtproeftuin.nl/zaken/api/v1/statussen', false);
				
			}
			
			// We might also want to create a zaakObject resource
			if(key_exists('result', $resource)){
				$result= $resource['result'];
				$result['zaak'] = $resource['@id'];
				
				$result= $zgwService->saveResource($result,'https://openzaak.utrechtproeftuin.nl/zaken/api/v1/resultaten', false);
				
			}			
			
			// We might also want to create a zaakObject resource
			if(key_exists('role', $resource)){
				$role= $resource['result'];
				$role['zaak'] = $resource['@id'];
				
				$role= $zgwService->saveResource($role,'https://openzaak.utrechtproeftuin.nl/zaken/api/v1/rollen', false);
				
			}			
			
			// We might also want to create a zaakObject resource
			if(key_exists('verdict', $resource)){
				$verdict= $resource['result'];
				$verdict['zaak'] = $resource['@id'];
				
				$verdict= $zgwService->saveResource($verdict,'https://openzaak.utrechtproeftuin.nl/besluiten/api/v1/besluiten', false);
				
			}
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
