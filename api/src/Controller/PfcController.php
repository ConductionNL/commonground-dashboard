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
 * Class PfcController
 * @package App\Controller
 * @Route("/pfc")
 */
class PfcController extends AbstractController
{
	/**
	 * @Route("/")
	 * @Template
	 */
	public function indexAction(TranslatorInterface $translator)
	{
		$variables = [];
		$variables['title'] = $translator->trans('Portfolio Component');
		$variables['subtitle'] = $translator->trans('The Portfolio Components hold Portfolios');

		return $variables;
	}

    /**
     * @Route("/activities")
     * @Template
     */
    public function activiesAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('activities');
        $variables['subtitle'] = $translator->trans('all') . ' ' . $translator->trans('activities');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'pfc','type'=>'activities']);
        return $variables;
    }

    /**
     * @Route("/activities/{id}")
     * @Template
     */
    public function activityAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {

        $variables = [];

        // Lets see if we need to create
        if($id == 'new'){
            $variables['resource'] = ['@id' => null,'id'=>'new','name'=>'new'];
        }
        else{
            $variables['resource'] = $commonGroundService->getResource(['component'=>'pfc','type'=>'activities','id'=> $id]);
        }

        // If it is a delete action we can stop right here
        if($request->query->get('action') == 'delete'){
            $commonGroundService->deleteResource($variables['resource']);
            return $this->redirect($this->generateUrl('app_pfc_activities'));
        }

        $variables['title'] = $translator->trans('activity');
        $variables['subtitle'] = $translator->trans('save or create a') . ' ' . $translator->trans('activities');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'pfc','type'=>'slugs'],['organization.id'=>$id])["hydra:member"];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])

            $variables['resource'] = $commonGroundService->saveResource($resource,(['component'=>'pfc','type'=>'activities','id'=>$id]));        }

        return $variables;
    }

    /**
     * @Route("/evaluations")
     * @Template
     */
    public function evaluationsAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('evaluations');
        $variables['subtitle'] = $translator->trans('all') . ' ' . $translator->trans('evaluations');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'pfc','type'=>'evaluations'])["hydra:member"];
        return $variables;

    }

    /**
     * @Route("/evaluations/{id}")
     * @Template
     */
    public function evaluationAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
        $variables = [];

        // Lets see if we need to create
        if($id == 'new'){
            $variables['resource'] = ['@id' => null,'id'=>'new','name'=>'new'];
        }
        else{
            $variables['resource'] = $commonGroundService->getResource(['component'=>'pfc','type'=>'evaluations','id'=> $id]);        }

        // If it is a delete action we can stop right here
        if($request->query->get('action') == 'delete'){
            $commonGroundService->deleteResource($variables['resource']);
            return $this->redirect($this->generateUrl('app_pfc_evaluations'));
        }

        $variables['title'] = $translator->trans('evaluation');
        $variables['subtitle'] = $translator->trans('save or create a') . ' ' . $translator->trans('evaluations');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'pfc','type'=>'slugs'],['organization.id'=>$id])["hydra:member"];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])

            $variables['resource'] = $commonGroundService->saveResource($resource,(['component'=>'pfc','type'=>'evaluations','id'=>$id]));        }

        return $variables;
    }

    /**
     * @Route("/formalrecognitions")
     * @Template
     */
    public function formalRecognitionsAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('formalRecognitions');
        $variables['subtitle'] = $translator->trans('all') . ' ' . $translator->trans('formalRecognitions');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'pfc','type'=>'formalRecognitions'])["hydra:member"];
        return $variables;

    }

    /**
     * @Route("/formalRecognitions/{id}")
     * @Template
     */
    public function formalRecognitionAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
        $variables = [];

        // Lets see if we need to create
        if($id == 'new'){
            $variables['resource'] = ['@id' => null,'id'=>'new','name'=>'new'];
        }
        else{
            $variables['resource'] = $commonGroundService->getResource(['component'=>'pfc','type'=>'formalRecognitions','id'=> $id]);        }

        // If it is a delete action we can stop right here
        if($request->query->get('action') == 'delete'){
            $commonGroundService->deleteResource($variables['resource']);
            return $this->redirect($this->generateUrl('app_pfc_formalRecognitions'));
        }

        $variables['title'] = $translator->trans('formalRecognition');
        $variables['subtitle'] = $translator->trans('save or create a') . ' ' . $translator->trans('formalRecognitions');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'pfc','type'=>'slugs'],['organization.id'=>$id])["hydra:member"];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])

            $variables['resource'] = $commonGroundService->saveResource($resource,(['component'=>'pfc','type'=>'formalRecognitions','id'=>$id]));        }

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
        $variables['subtitle'] = $translator->trans('all') . ' ' . $translator->trans('products');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'pfc','type'=>'products'])["hydra:member"];
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
        if($id == 'new'){
            $variables['resource'] = ['@id' => null,'id'=>'new','name'=>'new'];
        }
        else{
            $variables['resource'] = $commonGroundService->getResource(['component'=>'pfc','type'=>'products','id'=> $id]);        }

        // If it is a delete action we can stop right here
        if($request->query->get('action') == 'delete'){
            $commonGroundService->deleteResource($variables['resource']);
            return $this->redirect($this->generateUrl('app_pfc_products'));
        }

        $variables['title'] = $translator->trans('product');
        $variables['subtitle'] = $translator->trans('save or create a') . ' ' . $translator->trans('products');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'pfc','type'=>'slugs'],['organization.id'=>$id])["hydra:member"];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])

            $variables['resource'] = $commonGroundService->saveResource($resource,(['component'=>'pfc','type'=>'products','id'=>$id]));        }

        return $variables;
    }

    /**
     * @Route("/reflections")
     * @Template
     */
    public function refelectionsAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('reflections');
        $variables['subtitle'] = $translator->trans('all') . ' ' . $translator->trans('reflections');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'pfc','type'=>'reflections'])["hydra:member"];
        return $variables;

    }

    /**
     * @Route("/reflections/{id}")
     * @Template
     */
    public function reflectionAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
        $variables = [];

        // Lets see if we need to create
        if($id == 'new'){
            $variables['resource'] = ['@id' => null,'id'=>'new','name'=>'new'];
        }
        else{
            $variables['resource'] = $commonGroundService->getResource(['component'=>'pfc','type'=>'reflections','id'=> $id]);        }

        // If it is a delete action we can stop right here
        if($request->query->get('action') == 'delete'){
            $commonGroundService->deleteResource($variables['resource']);
            return $this->redirect($this->generateUrl('app_pfc_reflections'));
        }

        $variables['title'] = $translator->trans('reflection');
        $variables['subtitle'] = $translator->trans('save or create a') . ' ' . $translator->trans('reflections');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'pfc','type'=>'slugs'],['organization.id'=>$id])["hydra:member"];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])

            $variables['resource'] = $commonGroundService->saveResource($resource,(['component'=>'pfc','type'=>'reflections','id'=>$id]));        }

        return $variables;
    }

    /**
     * @Route("/results")
     * @Template
     */
    public function resultsAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('results');
        $variables['subtitle'] = $translator->trans('all') . ' ' . $translator->trans('results');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'pfc','type'=>'results'])["hydra:member"];
        return $variables;

    }

    /**
     * @Route("/results/{id}")
     * @Template
     */
    public function resultAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
        $variables = [];

        // Lets see if we need to create
        if($id == 'new'){
            $variables['resource'] = ['@id' => null,'id'=>'new','name'=>'new'];
        }
        else{
            $variables['resource'] = $commonGroundService->getResource(['component'=>'pfc','type'=>'results','id'=> $id]);        }

        // If it is a delete action we can stop right here
        if($request->query->get('action') == 'delete'){
            $commonGroundService->deleteResource($variables['resource']);
            return $this->redirect($this->generateUrl('app_pfc_results'));
        }

        $variables['title'] = $translator->trans('result');
        $variables['subtitle'] = $translator->trans('save or create a') . ' ' . $translator->trans('results');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'pfc','type'=>'slugs'],['organization.id'=>$id])["hydra:member"];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])

            $variables['resource'] = $commonGroundService->saveResource($resource,(['component'=>'pfc','type'=>'results','id'=>$id]));        }

        return $variables;
    }
}






