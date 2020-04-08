<?php

// src/Controller/DashboardController.php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatorInterface;

use App\Service\CommonGroundService;

/**
 * Class DashboardController
 * @package App\Controller
 * @Route("/wrc")
 */
class WrcController extends AbstractController
{
    /**
     * @Route("/")
     * @Template
     */
    public function indexAction(Request $request, CommonGroundService $commonGroundService)
    {
    	$variables = [];
    	$variables['title'] = $translator->trans('webresources');
    	$variables['subtitle'] = $translator->trans('the webresources consist of all items that are needed to render the application web interface');

    	return $variables;
    }

    /**
     * @Route("/templates")
     * @Template
     */
    public function templatesAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {

    	$variables = [];
    	$variables['title'] = $translator->trans('templates');
    	$variables['subtitle'] = $translator->trans('all').' '.$translator->trans('templates');
    	$variables['resources'] = $commonGroundService->getResourceList('https://wrc.huwelijksplanner.online/templates')["hydra:member"];

    	return $variables;

    }

    /**
     * @Route("/templates/{id}")
     * @Template
     */
    public function templateAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
    	$variables = [];

    	// Lets see if we need to create
    	if($id == 'new'){
    		$variables['resource'] = ['@id' => null,'name'=>'new','id'=>'new'];
    	}
    	else{
    		$variables['resource'] = $commonGroundService->getResource('https://wrc.huwelijksplanner.online/templates/'.$id);
    	}

    	// If it is a delete action we can stop right here
    	if($request->query->get('action') == 'delete'){
    		$commonGroundService->deleteResource($variables['resource']);
    		return $this->redirect($this->generateUrl('app_wrc_groups'));
    	}

    	$variables['title'] = $translator->trans('template');
    	$variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('template');
    	$variables['organizations'] = $commonGroundService->getResourceList('https://wrc.huwelijksplanner.online/organizations')["hydra:member"];

    	// Lets see if there is a post to procces
    	if ($request->isMethod('POST')) {

    		// Passing the variables to the resource
    		$resource = $request->request->all();
    		$resource['@id'] = $variables['resource']['@id'];
    		$resource['id'] = $variables['resource']['id'];

    		// If there are any sub data sources the need to be removed below in order to save the resource
    		// unset($resource['somedatasource'])

    		$variables['resource'] = $commonGroundService->saveResource($resource,'https://wrc.huwelijksplanner.online/templates/');
    	}
    	return $variables;
    }


    /**
     * @Route("/pages")
     * @Template
     */
    public function pagesAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {

    	$variables = [];
    	$variables['title'] = $translator->trans('pages');
    	$variables['subtitle'] = $translator->trans('all').' '.$translator->trans('pages');
    	$variables['resources'] = $commonGroundService->getResourceList('https://wrc.huwelijksplanner.online/pages')["hydra:member"];

    	return $variables;

    }

    /**
     * @Route("/pages/{id}")
     * @Template
     */
    public function pageAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
    	$variables = [];

    	// Lets see if we need to create
    	if($id == 'new'){
    		$variables['resource'] = ['@id' => null,'name'=>'new','id'=>'new'];
    	}
    	else{
    		$variables['resource'] = $commonGroundService->getResource('https://wrc.huwelijksplanner.online/pages/'.$id);
    	}

    	// If it is a delete action we can stop right here
    	if($request->query->get('action') == 'delete'){
    		$commonGroundService->deleteResource($variables['resource']);
    		return $this->redirect($this->generateUrl('app_wrc_pages'));
    	}

    	$variables['title'] = $translator->trans('page');
    	$variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('page');
    	$variables['organizations'] = $commonGroundService->getResourceList('https://wrc.huwelijksplanner.online/organizations')["hydra:member"];

    	// Lets see if there is a post to procces
    	if ($request->isMethod('POST')) {

    		// Passing the variables to the resource
    		$resource = $request->request->all();
    		$resource['@id'] = $variables['resource']['@id'];
    		$resource['id'] = $variables['resource']['id'];

    		// If there are any sub data sources the need to be removed below in order to save the resource
    		// unset($resource['somedatasource'])

    		$variables['resource'] = $commonGroundService->saveResource($resource,'https://wrc.huwelijksplanner.online/pages/');
    	}
    	return $variables;
    }


    /**
     * @Route("/slugs")
     * @Template
     */
    public function slugsAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {

    	$variables = [];
    	$variables['title'] = $translator->trans('slugs');
    	$variables['subtitle'] = $translator->trans('all').' '.$translator->trans('slugs');
    	$variables['resources'] = $commonGroundService->getResourceList('https://wrc.huwelijksplanner.online/slugs')["hydra:member"];

    	return $variables;

    }

    /**
     * @Route("/slugs/{id}")
     * @Template
     */
    public function slugAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
    	$variables = [];

    	// Lets see if we need to create
    	if($id == 'new'){
    		$variables['resource'] = ['@id' => null,'name'=>'new','id'=>'new'];
    	}
    	else{
    		$variables['resource'] = $commonGroundService->getResource('https://wrc.huwelijksplanner.online/slugs/'.$id);
    	}

    	// If it is a delete action we can stop right here
    	if($request->query->get('action') == 'delete'){
    		$commonGroundService->deleteResource($variables['resource']);
    		return $this->redirect($this->generateUrl('app_wrc_slugs'));
    	}

    	$variables['title'] = $translator->trans('slug');
    	$variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('slug');
    	$variables['organizations'] = $commonGroundService->getResourceList('https://wrc.huwelijksplanner.online/organizations')["hydra:member"];

    	// Lets see if there is a post to procces
    	if ($request->isMethod('POST')) {

    		// Passing the variables to the resource
    		$resource = $request->request->all();
    		$resource['@id'] = $variables['resource']['@id'];
    		$resource['id'] = $variables['resource']['id'];

    		// If there are any sub data sources the need to be removed below in order to save the resource
    		// unset($resource['somedatasource'])

    		$variables['resource'] = $commonGroundService->saveResource($resource,'https://wrc.huwelijksplanner.online/slugs/');
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
    	$variables['resources'] = $commonGroundService->getResourceList('https://wrc.huwelijksplanner.online/organizations')["hydra:member"];

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
    		$variables['resource'] = ['@id' => null,'name'=>'new','id'=>'new'];
    	}
    	else{
    		$variables['resource'] = $commonGroundService->getResource('https://wrc.huwelijksplanner.online/organizations/'.$id);
    	}

    	// If it is a delete action we can stop right here
    	if($request->query->get('action') == 'delete'){
    		$commonGroundService->deleteResource($variables['resource']);
    		return $this->redirect($this->generateUrl('app_wrc_organizations'));
    	}

    	$variables['title'] = $translator->trans('organization');
    	$variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('organization');
    	$variables['organizations'] = $commonGroundService->getResourceList('https://wrc.huwelijksplanner.online/organizations')["hydra:member"];

    	// Lets see if there is a post to procces
    	if ($request->isMethod('POST')) {

    		// Passing the variables to the resource
    		$resource = $request->request->all();
    		$resource['@id'] = $variables['resource']['@id'];
    		$resource['id'] = $variables['resource']['id'];

    		// If there are any sub data sources the need to be removed below in order to save the resource
    		// unset($resource['somedatasource'])

    		$variables['resource'] = $commonGroundService->saveResource($resource,'https://pdc.huwelijksplanner.online/organizations/');
    	}
    	return $variables;
    }

    /**
     * @Route("/image")
     * @Template
     */
    public function imagesAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {

    	$variables = [];
    	$variables['title'] = $translator->trans('images');
    	$variables['subtitle'] = $translator->trans('all').' '.$translator->trans('images');
    	$variables['resources'] = $commonGroundService->getResourceList('https://wrc.huwelijksplanner.online/images')["hydra:member"];

    	return $variables;

    }

    /**
     * @Route("/images/{id}")
     * @Template
     */
    public function imageAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
    	$variables = [];

    	// Lets see if we need to create
    	if($id == 'new'){
    		$variables['resource'] = ['@id' => null,'name'=>'new','id'=>'new'];
    	}
    	else{
    		$variables['resource'] = $commonGroundService->getResource('https://pdc.huwelijksplanner.online/images/'.$id);
    	}

    	// If it is a delete action we can stop right here
    	if($request->query->get('action') == 'delete'){
    		$commonGroundService->deleteResource($variables['resource']);
    		return $this->redirect($this->generateUrl('app_wrc_groups'));
    	}

    	$variables['title'] = $translator->trans('image');
    	$variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('image');
    	$variables['organizations'] = $commonGroundService->getResourceList('https://wrc.huwelijksplanner.online/organizations')["hydra:member"];

    	// Lets see if there is a post to procces
    	if ($request->isMethod('POST')) {

    		// Passing the variables to the resource
    		$resource = $request->request->all();
    		$resource['@id'] = $variables['resource']['@id'];
    		$resource['id'] = $variables['resource']['id'];

    		// If there are any sub data sources the need to be removed below in order to save the resource
    		// unset($resource['somedatasource'])

    		$variables['resource'] = $commonGroundService->saveResource($resource,'https://wrc.huwelijksplanner.online/images/');
    	}
    	return $variables;
    }


    /**
     * @Route("/styles")
     * @Template
     */
    public function stylesAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {

    	$variables = [];
    	$variables['title'] = $translator->trans('styles');
    	$variables['subtitle'] = $translator->trans('all').' '.$translator->trans('styles');
    	$variables['resources'] = $commonGroundService->getResourceList('https://wrc.huwelijksplanner.online/styles')["hydra:member"];

    	return $variables;

    }

    /**
     * @Route("/styles/{id}")
     * @Template
     */
    public function styleAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
    	$variables = [];

    	// Lets see if we need to create
    	if($id == 'new'){
    		$variables['resource'] = ['@id' => null,'name'=>'new','id'=>'new'];
    	}
    	else{
    		$variables['resource'] = $commonGroundService->getResource('https://wrc.huwelijksplanner.online/styles/'.$id);
    	}

    	// If it is a delete action we can stop right here
    	if($request->query->get('action') == 'delete'){
    		$commonGroundService->deleteResource($variables['resource']);
    		return $this->redirect($this->generateUrl('app_wrc_styles'));
    	}

    	$variables['title'] = $translator->trans('style');
    	$variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('style');
    	$variables['organizations'] = $commonGroundService->getResourceList('https://wrc.huwelijksplanner.online/organizations')["hydra:member"];

    	// Lets see if there is a post to procces
    	if ($request->isMethod('POST')) {

    		// Passing the variables to the resource
    		$resource = $request->request->all();
    		$resource['@id'] = $variables['resource']['@id'];
    		$resource['id'] = $variables['resource']['id'];

    		// If there are any sub data sources the need to be removed below in order to save the resource
    		// unset($resource['somedatasource'])

    		$variables['resource'] = $commonGroundService->saveResource($resource,'https://wrc.huwelijksplanner.online/styles/');
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
    	$variables['resources'] = $commonGroundService->getResourceList('https://wrc.huwelijksplanner.online/applications')["hydra:member"];

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
    		$variables['resource'] = ['@id' => null,'name'=>'new','id'=>'new'];
    	}
    	else{
    		$variables['resource'] = $commonGroundService->getResource('https://wrc.huwelijksplanner.online/applications/'.$id);
    	}

    	// If it is a delete action we can stop right here
    	if($request->query->get('action') == 'delete'){
    		$commonGroundService->deleteResource($variables['resource']);
    		return $this->redirect($this->generateUrl('app_wrc_applications'));
    	}

    	$variables['title'] = $translator->trans('application');
    	$variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('application');
    	$variables['organizations'] = $commonGroundService->getResourceList('https://wrc.huwelijksplanner.online/organizations')["hydra:member"];

    	// Lets see if there is a post to procces
    	if ($request->isMethod('POST')) {

    		// Passing the variables to the resource
    		$resource = $request->request->all();
    		$resource['@id'] = $variables['resource']['@id'];
    		$resource['id'] = $variables['resource']['id'];

    		// If there are any sub data sources the need to be removed below in order to save the resource
    		// unset($resource['somedatasource'])

    		$variables['resource'] = $commonGroundService->saveResource($resource,'https://wrc.huwelijksplanner.online/applications/');
    	}
    	return $variables;
    }



    /**
     * @Route("/menus")
     * @Template
     */
    public function menusAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {

    	$variables = [];
    	$variables['title'] = $translator->trans('menus');
    	$variables['subtitle'] = $translator->trans('all').' '.$translator->trans('menus');
    	$variables['resources'] = $commonGroundService->getResourceList('https://wrc.huwelijksplanner.online/menus')["hydra:member"];

    	return $variables;

    }

    /**
     * @Route("/menus/{id}")
     * @Template
     */
    public function menuAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
    	$variables = [];

    	// Lets see if we need to create
    	if($id == 'new'){
    		$variables['resource'] = ['@id' => null,'name'=>'new','id'=>'new'];
    	}
    	else{
    		$variables['resource'] = $commonGroundService->getResource('https://wrc.huwelijksplanner.online/menus/'.$id);
    	}

    	// If it is a delete action we can stop right here
    	if($request->query->get('action') == 'delete'){
    		$commonGroundService->deleteResource($variables['resource']);
    		return $this->redirect($this->generateUrl('app_wrc_menus'));
    	}

    	$variables['title'] = $translator->trans('menu');
    	$variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('menu');
    	$variables['organizations'] = $commonGroundService->getResourceList('https://wrc.huwelijksplanner.online/organizations')["hydra:member"];

    	// Lets see if there is a post to procces
    	if ($request->isMethod('POST')) {

    		// Passing the variables to the resource
    		$resource = $request->request->all();
    		$resource['@id'] = $variables['resource']['@id'];
    		$resource['id'] = $variables['resource']['id'];

    		// If there are any sub data sources the need to be removed below in order to save the resource
    		// unset($resource['somedatasource'])

    		$variables['resource'] = $commonGroundService->saveResource($resource,'https://wrc.huwelijksplanner.online/menus/');
    	}
    	return $variables;
    }


    /**
     * @Route("/configurations")
     * @Template
     */
    public function configurationsAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {

    	$variables = [];
    	$variables['title'] = $translator->trans('configurations');
    	$variables['subtitle'] = $translator->trans('all').' '.$translator->trans('configurations');
    	$variables['resources'] = $commonGroundService->getResourceList('https://wrc.huwelijksplanner.online/configurations')["hydra:member"];

    	return $variables;

    }

    /**
     * @Route("/configurations/{id}")
     * @Template
     */
    public function configurationAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
    	$variables = [];

    	// Lets see if we need to create
    	if($id == 'new'){
    		$variables['resource'] = ['@id' => null,'name'=>'new','id'=>'new'];
    	}
    	else{
    		$variables['resource'] = $commonGroundService->getResource('https://wrc.huwelijksplanner.online/configurations/'.$id);
    	}

    	// If it is a delete action we can stop right here
    	if($request->query->get('action') == 'delete'){
    		$commonGroundService->deleteResource($variables['resource']);
    		return $this->redirect($this->generateUrl('app_wrc_configurations'));
    	}

    	$variables['title'] = $translator->trans('configuration');
    	$variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('configuration');
    	$variables['organizations'] = $commonGroundService->getResourceList('https://wrc.huwelijksplanner.online/organizations')["hydra:member"];

    	// Lets see if there is a post to procces
    	if ($request->isMethod('POST')) {

    		// Passing the variables to the resource
    		$resource = $request->request->all();
    		$resource['@id'] = $variables['resource']['@id'];
    		$resource['id'] = $variables['resource']['id'];

    		// If there are any sub data sources the need to be removed below in order to save the resource
    		// unset($resource['somedatasource'])

    		$variables['resource'] = $commonGroundService->saveResource($resource,'https://wrc.huwelijksplanner.online/configurations/');
    	}
    	return $variables;
    }
}
