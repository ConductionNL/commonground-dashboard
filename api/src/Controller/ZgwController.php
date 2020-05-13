<?php
// src/Controller/DefaultController.php
namespace App\Controller;

use Conduction\CommonGroundBundle\Service\CommonGroundService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Translation\TranslatorInterface;

use App\Service\ZgwService;

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
	public function indexAction(Request $request, CommonGroundService  $commonGroundService, TranslatorInterface $translator)
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
	public function casesAction(CommonGroundService  $commonGroundService, TranslatorInterface $translator)
	{

		$variables = [];
		$variables['title'] = $translator->trans('cases');
		$variables['subtitle'] = $translator->trans('all').' '.$translator->trans('cases');
		$variables['resources'] = $commonGroundService->getResourceList(['component'=>'zrc','type'=>'zaken'])["results"];

        return $variables;
	}

	/**
	 * @Route("/cases/{id}")
	 * @Template
	 */
	public function caseAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
	{
		$variables = [];

		// Lets see if we need to create
		if($id == 'new'){
			$variables['resource'] = ['@id' => null, 'omschrijving'=>'nieuwe zaak','id'=>'new'];
		}
		else{
            $variables['resource'] = $commonGroundService->getResource(['component'=>'zrc','type'=>'zaken','id'=>$id]);
		}


		// If it is a delete action we can stop right here
		if($request->query->get('action') == 'delete'){
			$commonGroundService->deleteResource($variables['resource']);
			return $this->redirect($this->generateUrl('app_zgw_cases'));
		}

		$variables['title'] = $translator->trans('case');
		$variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('case');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'wrc','type'=>'organizations'])["hydra:member"];
        $variables['casetypes'] = $commonGroundService->getResourceList(['component'=>'ztc','type'=>'zaaktypen'])["results"];

        if(array_key_exists('@id',$variables['resource'])){
			$variables['statuses'] = $commonGroundService->getResourceList(['component'=>'vrc','type'=>'statussen'],['zaak'=>$variables['resource']['@id']])["results"];
			$variables['results'] = $commonGroundService->getResourceList(['component'=>'vrc','type'=>'resultaten'],['zaak'=>$variables['resource']['@id']])["results"];
			$variables['roles'] = $commonGroundService->getResourceList(['component'=>'vrc','type'=>'results'],['zaak'=>$variables['resource']['@id']])["results"];
			//$variables['verdics'] = $zgwService->getResourceList('https://openzaak.utrechtproeftuin.nl/besluiten/api/v1/besluiten',['zaak'=>$variables['resource']['@id']])["results"];
		}

		if(array_key_exists('zaaktype', $variables['resource'])){
            $variables['casestatuses'] = $commonGroundService->getResourceList(['component'=>'ztc','type'=>'statustypen'],['zaaktype'=>$variables['resource']['zaaktype']])["results"];
			$variables['caseresults'] = $commonGroundService->getResourceList(['component'=>'ztc','type'=>'resultaattypen'],['zaaktype'=>$variables['resource']['zaaktype']])["results"];
			$variables['caseroles'] = $commonGroundService->getResourceList(['component'=>'ztc','type'=>'roltypen'],['zaaktype'=>$variables['resource']['zaaktype']])["results"];
			$variables['caseverdics'] = $commonGroundService->getResourceList(['component'=>'ztc','type'=>'besluittypen'],['zaaktypen'=>$variables['resource']['zaaktype']])["results"];
            $variables['caseproperties'] = $commonGroundService->getResourceList(['component'=>'ztc','type'=>'eigenschappen'],['zaaktype'=>$variables['resource']['zaaktype']])["results"];
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
				$variables['resource'] = $commonGroundService->saveResource($resource,['component'=>'ztc','type'=>'zaken'], false);
			}

			// We might also want to create a zaakObject resource
			if(key_exists('zaakobject', $resource)){
				$zaakobject = $resource['zaakobject'];
				$zaakobject['zaak'] = $resource['@id'];

				$zaakobject = $commonGroundService->saveResource($zaakobject,['component'=>'ztc','type'=>'zaakobjecten'], false);
			}

			// We might also want to create a zaakObject resource
			if(key_exists('status', $resource)){
				$status = $resource['status'];
				$status['zaak'] = $resource['@id'];
				$status['datumStatusGezet'] = date('Y-m-d H:i:s');

				$status = $commonGroundService->saveResource($status,['component'=>'ztc','type'=>'statussen'], false);

			}

			// We might also want to create a zaakObject resource
			if(key_exists('result', $resource)){
				$result= $resource['result'];
				$result['zaak'] = $resource['@id'];

				$result= $commonGroundService->saveResource($result,['component'=>'ztc','type'=>'resultaten'], false);

			}

			// We might also want to create a zaakObject resource
			if(key_exists('role', $resource)){
				$role= $resource['result'];
				$role['zaak'] = $resource['@id'];

				$role= $commonGroundService->saveResource($role,['component'=>'ztc','type'=>'rollen'], false);

			}

			// We might also want to create a zaakObject resource
			if(key_exists('verdict', $resource)){
				$verdict= $resource['result'];
				$verdict['zaak'] = $resource['@id'];

				$verdict= $commonGroundService->saveResource($verdict,['component'=>'brc','type'=>'besluiten'], false);

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
