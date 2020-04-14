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
 * @Route("/pdc")
 */
class PdcController extends AbstractController
{

	/**
	 * @Route("/")
	 * @Template
	 */
	public function indexAction(TranslatorInterface $translator)
	{
		$variables = [];
		$variables['title'] = $translator->trans('product and service catalouge');
		$variables['subtitle'] = $translator->trans('the product and service catalouge holds al data concerning product, groups and offers');

		return $variables;
	}

    /**
     * @Route("/products")
     * @Template
     */
	public function productsAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
    	$variables = [];
    	$variables['title'] = $translator->trans('products');
    	$variables['subtitle'] = $translator->trans('all').' '.$translator->trans('products');
    	$variables['resources'] = $commonGroundService->getResourceList('https://pdc.huwelijksplanner.online/products')["hydra:member"];

        return $variables;
    }

    /**
     * @Route("/products/{id}")
     * @Template
     */
    public function productAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {

    	$variables = [];

    	// Lets see if we need to create
    	if($id == 'new') {
            $variables['resource'] = ['@id' => null, 'name' => 'new', 'id' => 'new'];
        }
    	else{
    		$variables['resource'] = $commonGroundService->getResource('https://pdc.huwelijksplanner.online/products/'.$id);
    	}

        // If it is a delete action we can stop right here
        if($request->query->get('action') == 'delete'){
            $commonGroundService->deleteResource($variables['resource']);
            return $this->redirect($this->generateUrl('app_pdc_products'));
        }

        $variables['title'] = $translator->trans('catalogue');
        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('catalogue');
        $variables['groups'] = $commonGroundService->getResourceList('https://pdc.huwelijksplanner.online/groups')["hydra:member"];
        $variables['catalogues'] = $commonGroundService->getResourceList('https://pdc.huwelijksplanner.online/catalogues')["hydra:member"];
        $variables['organizations'] = $commonGroundService->getResourceList('https://wrc.huwelijksplanner.online/organizations')["hydra:member"];

    	// Lets see if there is a post to procces
    	if ($request->isMethod('POST')) {

    		// Passing the variables to the resource
    		$resource = $request->request->all();

    		foreach ($variables['resource']['groups'] as $group){
    			$resource['groups'][] = 'groups/'.$group['id'];
    		}

    		if($resource['addgroup'] != ""){
    			$resource['groups'][] = $resource['addgroup'];
    		}

    		if($resource['removegroup'] != ""){
    			foreach($resource['groups'] as $key=>$group){
    				if($group == $resource['removegroup']){
    					unset($resource['groups'][$key]);
    				}
    			}
    		}

    		$resource['@id'] = $variables['resource']['@id'];
    		$resource['id'] = $variables['resource']['id'];

    		// If there are any sub data sources the need to be removed below in order to save the resource
    		// unset($resource['somedatasource'])

    		$variables['resource'] = $commonGroundService->saveResource($resource,'https://pdc.huwelijksplanner.online/products/');
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
    	$variables['resources'] = $commonGroundService->getResourceList('https://pdc.huwelijksplanner.online/groups')["hydra:member"];

    	return $variables;

    }

    /**
     * @Route("/group/{id}")
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
    		$variables['resource'] = $commonGroundService->getResource('https://pdc.huwelijksplanner.online/groups/'.$id);
    	}

    	// If it is a delete action we can stop right here
    	if($request->query->get('action') == 'delete'){
    		$commonGroundService->deleteResource($variables['resource']);
    		return $this->redirect($this->generateUrl('app_pdc_groups'));
    	}

    	$variables['title'] = $translator->trans('group');
    	$variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('group');
    	$variables['catalogues'] = $commonGroundService->getResourceList('https://pdc.huwelijksplanner.online/catalogues')["hydra:member"];
    	$variables['organizations'] = $commonGroundService->getResourceList('https://wrc.huwelijksplanner.online/organizations')["hydra:member"];

    	// Lets see if there is a post to procces
    	if ($request->isMethod('POST')) {

    		// Passing the variables to the resource
    		$resource = $request->request->all();
    		$resource['@id'] = $variables['resource']['@id'];
    		$resource['id'] = $variables['resource']['id'];

    		// If there are any sub data sources the need to be removed below in order to save the resource
    		// unset($resource['somedatasource'])

    		$variables['resource'] = $commonGroundService->saveResource($resource,'https://pdc.huwelijksplanner.online/groups/');
    	}
    	return $variables;
    }

    /**
     * @Route("/offers")
     * @Template
     */
    public function offersAction( CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {

    	$variables = [];
    	$variables['title'] = $translator->trans('offers');
    	$variables['subtitle'] = $translator->trans('all').' '.$translator->trans('offers');
    	$variables['resources'] = $commonGroundService->getResourceList('https://pdc.huwelijksplanner.online/offers')["hydra:member"];

    	return $variables;
    }

    /**
     * @Route("/offer/{id}")
     * @Template
     */
    public function offerAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
    	$variables = [];

    	// Lets see if we need to create
    	if($id == 'new'){
    		$variables['resource'] = ['@id' => null,'name'=>'new','id'=>'new'];
    	}
    	else{
    		$variables['resource'] = $commonGroundService->getResource('https://pdc.huwelijksplanner.online/offers/'.$id);
    	}

    	// If it is a delete action we can stop right here
    	if($request->query->get('action') == 'delete'){
    		$commonGroundService->deleteResource($variables['resource']);
    		return $this->redirect($this->generateUrl('app_pdc_offers'));
    	}

    	$variables['title'] = $translator->trans('offer');
    	$variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('offer');
    	$variables['organizations'] = $commonGroundService->getResourceList('https://wrc.huwelijksplanner.online/organizations')["hydra:member"];

    	// Lets see if there is a post to procces
    	if ($request->isMethod('POST')) {

    		// Passing the variables to the resource
    		$resource = $request->request->all();
    		$resource['@id'] = $variables['resource']['@id'];
    		$resource['id'] = $variables['resource']['id'];

    		// If there are any sub data sources the need to be removed below in order to save the resource
    		// unset($resource['somedatasource'])

    		$variables['resource'] = $commonGroundService->saveResource($resource,'https://pdc.huwelijksplanner.online/offers/');
    	}

    	return $variables;
    }

    /**
     * @Route("/catalogues")
     * @Template
     */
    public function cataloguesAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {

    	$variables = [];
    	$variables['title'] = $translator->trans('catalogues');
    	$variables['subtitle'] = $translator->trans('all').' '.$translator->trans('catalogues');
    	$variables['resources'] = $commonGroundService->getResourceList('https://pdc.huwelijksplanner.online/catalogues')["hydra:member"];

    	return $variables;
    }

    /**
     * @Route("/catalogues/{id}")
     * @Template
     */
    public function catalogueAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
    	$variables = [];

    	// Lets see if we need to create
    	if($id == 'new'){
    		$variables['resource'] = ['@id' => null,'name'=>'new','id'=>'new'];
    	}
    	else{
    		$variables['resource'] = $commonGroundService->getResource('https://pdc.huwelijksplanner.online/catalogues/'.$id);
    	}

    	// If it is a delete action we can stop right here
    	if($request->query->get('action') == 'delete'){
    		$commonGroundService->deleteResource($variables['resource']);
    		return $this->redirect($this->generateUrl('app_pdc_catalogues'));
    	}

    	$variables['title'] = $translator->trans('catalogue');
    	$variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('catalogue');
    	$variables['organizations'] = $commonGroundService->getResourceList('https://wrc.huwelijksplanner.online/organizations')["hydra:member"];

    	// Lets see if there is a post to procces
    	if ($request->isMethod('POST')) {

    		// Passing the variables to the resource
    		$resource = $request->request->all();
    		$resource['@id'] = $variables['resource']['@id'];
    		$resource['id'] = $variables['resource']['id'];

    		// If there are any sub data sources the need to be removed below in order to save the resource
    		// unset($resource['somedatasource'])

    		$variables['resource'] = $commonGroundService->saveResource($resource,'https://pdc.huwelijksplanner.online/catalogues/');
    	}

    	return $variables;
    }

    /**
     * @Route("/taxes")
     * @Template
     */
    public function taxesAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {

    	$variables = [];
    	$variables['title'] = $translator->trans('taxes');
    	$variables['subtitle'] = $translator->trans('all').' '.$translator->trans('taxes');
    	$variables['resources'] = $commonGroundService->getResourceList('https://pdc.huwelijksplanner.online/taxes')["hydra:member"];

    	return $variables;
    }

    /**
     * @Route("/taxes/{id}")
     * @Template
     */
    public function taxAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
    	$variables = [];

    	// Lets see if we need to create
    	if($id == 'new'){
    		$variables['resource'] = ['@id' => null,'name'=>'new','id'=>'new'];
    	}
    	else{
    		$variables['resource'] = $commonGroundService->getResource('https://pdc.huwelijksplanner.online/taxes/'.$id);
    	}

    	// If it is a delete action we can stop right here
    	if($request->query->get('action') == 'delete'){
    		$commonGroundService->deleteResource($variables['resource']);
    		return $this->redirect($this->generateUrl('app_pdc_taxes'));
    	}


    	$variables['title'] = $translator->trans('tax');
    	$variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('tax');
    	$variables['organizations'] = $commonGroundService->getResourceList('https://wrc.huwelijksplanner.online/organizations')["hydra:member"];

    	// Lets see if there is a post to procces
    	if ($request->isMethod('POST')) {

    		// Passing the variables to the resource
    		$resource = $request->request->all();
    		$resource['@id'] = $variables['resource']['@id'];
    		$resource['id'] = $variables['resource']['id'];

    		// If there are any sub data sources the need to be removed below in order to save the resource
    		// unset($resource['somedatasource'])

    		$variables['resource'] = $commonGroundService->saveResource($resource,'https://pdc.huwelijksplanner.online/taxes/');
    	}

    	return $variables;
    }

    /**
     * @Route("/suppliers")
     * @Template
     */
    public function suppliersAction( CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {

    	$variables = [];
    	$variables['title'] = $translator->trans('suppliers');
    	$variables['subtitle'] = $translator->trans('all').' '.$translator->trans('suppliers');
    	$variables['resources'] = $commonGroundService->getResourceList('https://pdc.huwelijksplanner.online/supliers')["hydra:member"];

    	return $variables;
    }

    /**
     * @Route("/suppliers/{id}")
     * @Template
     */
    public function supplierAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
    	$variables = [];

    	// Lets see if we need to create
    	if($id == 'new'){
    		$variables['resource'] = ['@id' => null,'name'=>'new','id'=>'new'];
    	}
    	else{
    		$variables['resource'] = $commonGroundService->getResource('https://pdc.huwelijksplanner.online/suppliers/'.$id);
    	}

    	// If it is a delete action we can stop right here
    	if($request->query->get('action') == 'delete'){
    		$commonGroundService->deleteResource($variables['resource']);
    		return $this->redirect($this->generateUrl('app_pdc_supliers'));
    	}

    	$variables['title'] = $translator->trans('supplier');
    	$variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('supplier');
    	$variables['organizations'] = $commonGroundService->getResourceList('https://wrc.huwelijksplanner.online/organizations')["hydra:member"];

    	// Lets see if there is a post to procces
    	if ($request->isMethod('POST')) {

    		// Passing the variables to the resource
    		$resource = $request->request->all();
    		$resource['@id'] = $variables['resource']['@id'];
    		$resource['id'] = $variables['resource']['id'];

    		// If there are any sub data sources the need to be removed below in order to save the resource
    		// unset($resource['somedatasource'])

    		$variables['resource'] = $commonGroundService->saveResource($resource,'https://pdc.huwelijksplanner.online/suppliers/');
    	}

    	return $variables;
    }

    /**
     * @Route("/customer_types")
     * @Template
     */
    public function customerTypesAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
    	$variables = [];
    	$variables['title'] = $translator->trans('customer types');
    	$variables['subtitle'] = $translator->trans('all').' '.$translator->trans('customer type');
    	$variables['resources'] = $commonGroundService->getResourceList('https://pdc.huwelijksplanner.online/customer_types')["hydra:member"];

    	return $variables;
    }

    /**
     * @Route("/customer_types/{id}")
     * @Template
     */
    public function customerTypeAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
    	$variables = [];

    	// Lets see if we need to create
    	if($id == 'new'){
    		$variables['resource'] = ['@id' => null,'name'=>'new','id'=>'new'];
    	}
    	else{
    		$variables['resource'] = $commonGroundService->getResource('https://pdc.huwelijksplanner.online/customer_types/'.$id);
    	}

    	// If it is a delete action we can stop right here
    	if($request->query->get('action') == 'delete'){
    		$commonGroundService->deleteResource($variables['resource']);
    		return $this->redirect($this->generateUrl('app_pdc_customertypes'));
    	}

    	$variables['title'] = $translator->trans('customer type');
    	$variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('customer type');
    	$variables['organizations'] = $commonGroundService->getResourceList('https://wrc.huwelijksplanner.online/organizations')["hydra:member"];

    	// Lets see if there is a post to procces
    	if ($request->isMethod('POST')) {

    		// Passing the variables to the resource
    		$resource = $request->request->all();
    		$resource['@id'] = $variables['resource']['@id'];
    		$resource['id'] = $variables['resource']['id'];

    		// If there are any sub data sources the need to be removed below in order to save the resource
    		// unset($resource['somedatasource'])

    		$variables['resource'] = $commonGroundService->saveResource($resource,'https://pdc.huwelijksplanner.online/customer_types/');
    	}

    	return $variables;
    }
}
