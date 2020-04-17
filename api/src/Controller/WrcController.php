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
    public function indexAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator)
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
    	$variables['resources'] = $commonGroundService->getResourceList(['component'=>'wrc','type'=>'templates'])["hydra:member"];

    	return $variables;

    }

    /**
     * @Route("/templates/{id}")
     * @Template
     */
    public function templateAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
        // If it is a delete action we can stop right here
        if($request->query->get('action') == 'delete'){
            $commonGroundService->deleteResource(['component'=>'wrc','type'=>'templates','id'=> $id]);
            return $this->redirect($this->generateUrl('app_wrc_groups'));
        }

    	$variables = [];

    	// Lets see if we need to create
    	if($id == 'new'){
    		$variables['resource'] = ['@id' => null,'name'=>'new','id'=>'new'];
    	}
    	else{
    		$variables['resource'] = $commonGroundService->getResource(['component'=>'wrc','type'=>'templates','id'=> $id]);
            $variables['slugs'] = $commonGroundService->getResourceList(['component'=>'wrc','type'=>'slugs'],['template.id'=>$id])["hydra:member"];
    	}

    	$variables['title'] = $translator->trans('template');
    	$variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('template');
    	$variables['organizations'] = $commonGroundService->getResourceList(['component'=>'wrc','type'=>'organizations'])["hydra:member"];
        $variables['applications'] = $commonGroundService->getResourceList(['component'=>'wrc','type'=>'applications'])["hydra:member"];

    	// Lets see if there is a post to procces
    	if ($request->isMethod('POST')) {

    		// Passing the variables to the resource
    		$resource = $request->request->all();
    		$resource['@id'] = $variables['resource']['@id'];
    		$resource['id'] = $variables['resource']['id'];


    		$variables['resource'] = $commonGroundService->saveResource($resource, ['component'=>'wrc','type'=>'templates']);

            // Lets see if we also need to add an slug
            if(array_key_exists('slug', $resource)){
                $slug = $resource['slug'];
                $slug['template'] = $variables['resource']['@id'];
                $slug['name'] = $variables['resource']['name'];
                $slug = $commonGroundService->saveResource($slug, ['component'=>'wrc','type'=>'slugs']);
            }
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
    	$variables['resources'] = $commonGroundService->getResourceList(['component'=>'wrc','type'=>'slugs'])["hydra:member"];

    	return $variables;

    }

    /**
     * @Route("/slugs/{id}")
     * @Template
     */
    public function slugAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {

        // If it is a delete action we can stop right here
        if($request->query->get('action') == 'delete'){
            $commonGroundService->deleteResource(['component'=>'wrc','type'=>'slugs','id'=>$id]);
            return $this->redirect($this->generateUrl('app_wrc_slugs'));
        }

    	$variables = [];

    	// Lets see if we need to create
    	if($id == 'new'){
    		$variables['resource'] = ['@id' => null,'name'=>'new','id'=>'new'];
    	}
    	else{
    		$variables['resource'] = $commonGroundService->getResource(['component'=>'wrc','type'=>'slugs','id'=>$id]);
    	}

    	$variables['title'] = $translator->trans('slug');
    	$variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('slug');
    	$variables['organizations'] = $commonGroundService->getResourceList(['component'=>'wrc','type'=>'organizations'])["hydra:member"];

    	// Lets see if there is a post to procces
    	if ($request->isMethod('POST')) {

    		// Passing the variables to the resource
    		$resource = $request->request->all();
    		$resource['@id'] = $variables['resource']['@id'];
    		$resource['id'] = $variables['resource']['id'];

    		// If there are any sub data sources the need to be removed below in order to save the resource
    		// unset($resource['somedatasource'])

    		$variables['resource'] = $commonGroundService->saveResource($resource,['component'=>'wrc','type'=>'slugs']);
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
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'wrc','type'=>'organizations'])["hydra:member"];

    	return $variables;

    }

    /**
     * @Route("/organizations/{id}")
     * @Template
     */
    public function organizationAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
        // If it is a delete action we can stop right here
        if($request->query->get('action') == 'delete'){
            $commonGroundService->deleteResource(['component'=>'wrc','type'=>'organizations','id'=>$id);
            return $this->redirect($this->generateUrl('app_wrc_organizations'));
        }

    	$variables = [];

    	// Lets see if we need to create
    	if($id == 'new'){
    		$variables['resource'] = ['@id' => null,'name'=>'new','id'=>'new'];
    	}
    	else{
            $variables['resource'] = $commonGroundService->getResource(['component'=>'wrc','type'=>'organizations','id'=>$id]);
            $variables['styles'] = $commonGroundService->getResourceList(['component'=>'wrc','type'=>'styles'],['organization.id'=>$id])["hydra:member"];
    	}

    	$variables['title'] = $translator->trans('organization');
    	$variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('organization');
    	$variables['organizations'] = $commonGroundService->getResourceList(['component'=>'wrc','type'=>'organizations'])["hydra:member"];

    	// Lets see if there is a post to procces
    	if ($request->isMethod('POST')) {

    		// Passing the variables to the resource
    		$resource = $request->request->all();
    		$resource['@id'] = $variables['resource']['@id'];
    		$resource['id'] = $variables['resource']['id'];

    		// If there are any sub data sources the need to be removed below in order to save the resource
    		// unset($resource['somedatasource'])

    		$variables['resource'] = $commonGroundService->saveResource($resource,['component'=>'wrc','type'=>'organizations']);
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
    	$variables['resources'] = $commonGroundService->getResourceList(['component'=>'wrc','type'=>'images'])["hydra:member"];

    	return $variables;

    }

    /**
     * @Route("/images/{id}")
     * @Template
     */
    public function imageAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
        // If it is a delete action we can stop right here
        if($request->query->get('action') == 'delete'){
            $commonGroundService->deleteResource(['component'=>'wrc','type'=>'imgaes','id'=>$id]);
            return $this->redirect($this->generateUrl('app_wrc_groups'));
        }

    	$variables = [];

    	// Lets see if we need to create
    	if($id == 'new'){
    		$variables['resource'] = ['@id' => null,'name'=>'new','id'=>'new'];
    	}
    	else{
    		$variables['resource'] = $commonGroundService->getResource(['component'=>'wrc','type'=>'imgaes','id'=>$id]);
    	}

    	$variables['title'] = $translator->trans('image');
    	$variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('image');
    	$variables['organizations'] = $commonGroundService->getResourceList(['component'=>'wrc','type'=>'organizations'])["hydra:member"];

    	// Lets see if there is a post to procces
    	if ($request->isMethod('POST')) {

    		// Passing the variables to the resource
    		$resource = $request->request->all();
    		$resource['@id'] = $variables['resource']['@id'];
    		$resource['id'] = $variables['resource']['id'];

    		// If there are any sub data sources the need to be removed below in order to save the resource
    		// unset($resource['somedatasource'])

    		$variables['resource'] = $commonGroundService->saveResource($resource,['component'=>'wrc','type'=>'images']);
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
    	$variables['resources'] = $commonGroundService->getResourceList(['component'=>'wrc','type'=>'styles'])["hydra:member"];

    	return $variables;

    }

    /**
     * @Route("/styles/{id}")
     * @Template
     */
    public function styleAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
        // If it is a delete action we can stop right here
        if($request->query->get('action') == 'delete'){
            $commonGroundService->deleteResource(['component'=>'wrc','type'=>'styles','id'=>$id]);
            return $this->redirect($this->generateUrl('app_wrc_styles'));
        }

    	$variables = [];

    	// Lets see if we need to create
    	if($id == 'new'){
    		$variables['resource'] = ['@id' => null,'name'=>'new','id'=>'new'];
    	}
    	else{
    		$variables['resource'] = $commonGroundService->getResource(['component'=>'wrc','type'=>'styles','id'=>$id]);
    	}

    	$variables['title'] = $translator->trans('style');
    	$variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('style');
    	$variables['organizations'] = $commonGroundService->getResourceList(['component'=>'wrc','type'=>'organizations'])["hydra:member"];

    	// Lets see if there is a post to procces
    	if ($request->isMethod('POST')) {

    		// Passing the variables to the resource
    		$resource = $request->request->all();
    		$resource['@id'] = $variables['resource']['@id'];
    		$resource['id'] = $variables['resource']['id'];

    		// If there are any sub data sources the need to be removed below in order to save the resource
    		// unset($resource['somedatasource'])

    		$variables['resource'] = $commonGroundService->saveResource($resource, ['component'=>'wrc','type'=>'styles']);
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
    	$variables['resources'] = $commonGroundService->getResourceList(['component'=>'wrc','type'=>'applications'])["hydra:member"];

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
            $commonGroundService->deleteResource(['component'=>'wrc','type'=>'applications','id'=>$id]);
            return $this->redirect($this->generateUrl('app_wrc_applications'));
        }

    	$variables = [];

    	// Lets see if we need to create
    	if($id == 'new'){
    		$variables['resource'] = ['@id' => null,'name'=>'new','id'=>'new'];
    	}
    	else{
    		$variables['resource'] = $commonGroundService->getResource(['component'=>'wrc','type'=>'applications','id'=>$id]);
            $variables['configurations'] = $commonGroundService->getResourceList(['component'=>'wrc','type'=>'configurations'],['application.id'=>$id])["hydra:member"];
            $variables['templates'] = $commonGroundService->getResourceList(['component'=>'wrc','type'=>'templates'],['application.id'=>$id])["hydra:member"];
            $variables['slugs'] = $commonGroundService->getResourceList(['component'=>'wrc','type'=>'slugs'],['application.id'=>$id])["hydra:member"];
            $variables['menus'] = $commonGroundService->getResourceList(['component'=>'wrc','type'=>'menus'],['application.id'=>$id])["hydra:member"];
    	}

    	$variables['title'] = $translator->trans('application');
    	$variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('application');

    	$variables['organizations'] = $commonGroundService->getResourceList(['component'=>'wrc','type'=>'organizations'])["hydra:member"];

        // Als we een organisatie hebben kunnen we ook de style ophalen @to engels!
        if(array_key_exists('organization', $variables['resource'])){
            $variables['styles'] = $commonGroundService->getResourceList(['component'=>'wrc','type'=>'styles'],['organization.id'=>$variables['resource']['organization']])["hydra:member"];
        }

    	// Lets see if there is a post to procces
    	if ($request->isMethod('POST')) {

    		// Passing the variables to the resource
    		$resource = $request->request->all();
    		$resource['@id'] = $variables['resource']['@id'];
    		$resource['id'] = $variables['resource']['id'];

    		// If there are any sub data sources the need to be removed below in order to save the resource
    		// unset($resource['somedatasource'])

            // Lets see if we also need to add an configuration
            if(array_key_exists('configuration', $resource)){
                $configuration = $resource['configuration'];
                $configuration['application'] = $resource['@id'];
                if(in_array('id',$configuration)){
                    $configuration['@id'] = $configuration['id'];
                }
                $configuration = $commonGroundService->saveResource($configuration, ['component'=>'wrc','type'=>'configurations']);
            }

            // Lets see if we also need to add an configuration
            if(array_key_exists('template', $resource)){
                $template = $resource['template'];
                $template['application'] = $resource['@id'];
                if(in_array('id',$template)){
                    $template['@id'] = $template['id'];
                }
                $template = $commonGroundService->saveResource($template, ['component'=>'wrc','type'=>'templates']);
            }

            // Lets see if we also need to add an configuration
            if(array_key_exists('slug', $resource)){
                $slug = $resource['slug'];
                $slug['application'] = $resource['@id'];
                if(in_array('id',$slug)){
                    $slug['@id'] = $slug['id'];
                }
                $slug = $commonGroundService->saveResource($slug, ['component'=>'wrc','type'=>'slugs']);
            }

            // Lets see if we also need to add an configuration
            if(array_key_exists('menu', $resource)){
                $menu = $resource['menu'];
                $menu['application'] = $resource['@id'];
                if(in_array('id',$menu)){
                    $menu['@id'] = $menu['id'];
                }
                $menu = $commonGroundService->saveResource($menu, ['component'=>'wrc','type'=>'menus']);
            }

    		$variables['resource'] = $commonGroundService->saveResource($resource,['component'=>'wrc','type'=>'applications']);
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
    	$variables['resources'] = $commonGroundService->getResourceList(['component'=>'wrc','type'=>'menus'])["hydra:member"];

    	return $variables;

    }

    /**
     * @Route("/menus/{id}")
     * @Template
     */
    public function menuAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
        // If it is a delete action we can stop right here
        if($request->query->get('action') == 'delete'){
            $commonGroundService->deleteResource(['component'=>'wrc','type'=>'menus','id'=>$id]);
            return $this->redirect($this->generateUrl('app_wrc_menus'));
        }

    	$variables = [];

    	// Lets see if we need to create
    	if($id == 'new'){
    		$variables['resource'] = ['@id' => null,'name'=>'new','id'=>'new'];
    	}
    	else{
    		$variables['resource'] = $commonGroundService->getResource(['component'=>'wrc','type'=>'menus','id'=>$id]);
    	}

    	$variables['title'] = $translator->trans('menu');
    	$variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('menu');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'wrc','type'=>'organizations'])["hydra:member"];
        $variables['applications'] = $commonGroundService->getResourceList(['component'=>'wrc','type'=>'applications'])["hydra:member"];
        $variables['slugs'] = $commonGroundService->getResourceList(['component'=>'wrc','type'=>'slugs'])["hydra:member"];

    	// Lets see if there is a post to procces
    	if ($request->isMethod('POST')) {

    		// Passing the variables to the resource
    		$resource = $request->request->all();
    		$resource['@id'] = $variables['resource']['@id'];
    		$resource['id'] = $variables['resource']['id'];

    		// If there are any sub data sources the need to be removed below in order to save the resource
    		// unset($resource['somedatasource'])

            // Lets see if we also need to add an slug
            if(array_key_exists('menuItem', $resource)){
                // Schecker de check

                //if(array_key_exists('menuItems', $resource)){
                //    $resource['menuItems'] = [];
                //}

                $menuItem = $resource['menuItem'];
                $menuItem['menu'] = $resource['@id'];
                $menuItem = $commonGroundService->saveResource($menuItem, ['component'=>'wrc','type'=>'menu_items']);

            }

            $variables['resource'] = $commonGroundService->saveResource($resource,['component'=>'wrc','type'=>'menus']);
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
    	$variables['resources'] = $commonGroundService->getResourceList(['component'=>'wrc','type'=>'configurations'])["hydra:member"];

    	return $variables;
    }

    /**
     * @Route("/configurations/{id}")
     * @Template
     */
    public function configurationAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
        // If it is a delete action we can stop right here
        if($request->query->get('action') == 'delete'){
            $commonGroundService->deleteResource(['component'=>'wrc','type'=>'configurations','id'=>$id]);
            return $this->redirect($this->generateUrl('app_wrc_configurations'));
        }

    	$variables = [];

    	// Lets see if we need to create
    	if($id == 'new'){
    		$variables['resource'] = ['@id' => null,'name'=>'new','id'=>'new'];
    	}
    	else{
    		$variables['resource'] = $commonGroundService->getResource(['component'=>'wrc','type'=>'configurations','id'=>$id]);
    	}

    	$variables['title'] = $translator->trans('configuration');
    	$variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('configuration');
    	$variables['organizations'] = $commonGroundService->getResourceList(['component'=>'wrc','type'=>'organizations'])["hydra:member"];

    	// Lets see if there is a post to procces
    	if ($request->isMethod('POST')) {

    		// Passing the variables to the resource
    		$resource = $request->request->all();
    		$resource['@id'] = $variables['resource']['@id'];
    		$resource['id'] = $variables['resource']['id'];

    		// If there are any sub data sources the need to be removed below in order to save the resource
    		// unset($resource['somedatasource'])

    		$variables['resource'] = $commonGroundService->saveResource($resource,['component'=>'wrc','type'=>'configurations']);
    	}
    	return $variables;
    }
}
