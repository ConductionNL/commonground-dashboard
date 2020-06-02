<?php
// src/Controller/DefaultController.php
namespace App\Controller;

use Conduction\CommonGroundBundle\Service\CommonGroundService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use Knp\Bundle\MarkdownBundle\MarkdownParserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Translation\TranslatorInterface;
use DateTimeZone;

/**
 * Class GrcController
 * @package App\Controller
 * @Route("/grc")
 */
class GrcController extends AbstractController
{

	/**
	 * @Route("/")
	 * @Template
	 */
	public function indexAction(TranslatorInterface $translator)
	{
		$variables = [];
		$variables['title'] = $translator->trans('Grave registration catalouge');
		$variables['subtitle'] = $translator->trans('The Grave administration catalouge holds al data concerning issues surround grave administration');

		return $variables;
	}

    /**
     * @Route("/graves")
     * @Template
     */
	public function gravesAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
    	$variables = [];
    	$variables['title'] = $translator->trans('graves');
    	$variables['subtitle'] = $translator->trans('all').' '.$translator->trans('graves');
    	$variables['resources'] = $commonGroundService->getResourceList(['component'=>'grc','type'=>'graves'])["hydra:member"];

        return $variables;
    }
    /**
     * @Route("/graves/{id}")
     * @Template
     */
    public function graveAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
        // If it is a delete action we can stop right here
        if($request->query->get('action') == 'delete'){
            $commonGroundService->deleteResource(null,['component'=>'grc','type'=>'graves','id'=>$id]);
            return $this->redirect($this->generateUrl('app_grc_graves'));
        }



        $variables = [];

        // Lets see if we need to create
        if($id == 'new'){
            $variables['resource'] = ['@id' => null,'name'=>'new','id'=>'new'];
        }
        else{
            $variables['resource'] = $commonGroundService->getResource(['component'=>'grc','type'=>'graves','id'=>$id]);
            //$variables['styles'] = $commonGroundService->getResourceList(['component'=>'grc','type'=>'styles'],['grave.id'=>$id])["hydra:member"];
        }


        /*// If it is a delete action we can stop right here
        if($request->query->get('action') == 'delete'){
            $commonGroundService->deleteResource($variables['resource']);
            return $this->redirect($this->generateUrl('app_grc_graves'));
        }*/


        $variables['title'] = $translator->trans('grave');
        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('grave');
        $variables['cemeteries'] = $commonGroundService->getResourceList(['component'=>'grc','type'=>'cemeteries'])["hydra:member"];
        $variables['gravetypes'] = $commonGroundService->getResourceList(['component'=>'grc','type'=>'grave_types'])["hydra:member"];
        $variables['gravecovers'] = $commonGroundService->getResourceList(['component'=>'grc','type'=>'grave_covers'])["hydra:member"];
        $variables['ingeschrevenpersonen'] = $commonGroundService->getResourceList(['component'=>'brp','type'=>'ingeschrevenpersonen']);


        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            $resource['position'] = (int)$resource['position'];

            foreach($resource as $key=>$value){
                if($value == ""){
                    unset($resource[$key]);
                }
            }

            $variables['resource'] = $commonGroundService->saveResource($resource, ['component'=>'grc','type'=>'graves']);

            /* @to this redirect is a hotfix */
            if(array_key_exists('id', $variables['resource'])){
                return $this->redirect($this->generateUrl('app_grc_graves', ["id" =>  $variables['resource']['id']]));
            }

        }

        return $variables;
    }

    /**
     * @Route("/gravetypes")
     * @Template
     */
    public function gravetypesAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('grave_types');
        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('grave_types');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'grc','type'=>'grave_types'])["hydra:member"];

        return $variables;
    }
    /**
     * @Route("/gravetype/{id}")
     * @Template
     */
    public function gravetypeAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
        // If it is a delete action we can stop right here
        if ($request->query->get('action') == 'delete') {
            $commonGroundService->deleteResource(null, ['component' => 'grc', 'type' => 'grave_types', 'id' => $id]);
            return $this->redirect($this->generateUrl('app_grc_gravetypes'));
        }

        $variables = [];

        // Lets see if we need to create
        if ($id == 'new') {
            $variables['resource'] = ['@id' => null, 'name' => 'new', 'id' => 'new'];
        } else {
            $variables['resource'] = $commonGroundService->getResource(['component' => 'grc', 'type' => 'grave_types', 'id' => $id]);
            //$variables['styles'] = $commonGroundService->getResourceList(['component'=>'grc','type'=>'styles'],['grave.id'=>$id])["hydra:member"];
        }


        /*// If it is a delete action we can stop right here
        if($request->query->get('action') == 'delete'){
            $commonGroundService->deleteResource($variables['resource']);
            return $this->redirect($this->generateUrl('app_grc_graves'));
        }*/


        $variables['title'] = $translator->trans('grave_type');
        $variables['subtitle'] = $translator->trans('save or create a') . ' ' . $translator->trans('grave_type');


        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            $variables['resource'] = $commonGroundService->saveResource($resource, ['component' => 'grc', 'type' => 'grave_types']);

            /* @to this redirect is a hotfix */
            if(array_key_exists('id', $variables['resource'])){
                return $this->redirect($this->generateUrl('app_grc_gravetypes', ["id" =>  $variables['resource']['id']]));
            }
        }
        return $variables;
    }

        /**
         * @Route("/cemeteries")
         * @Template
         */
        public function cemeteriesAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
        {
            $variables = [];
            $variables['title'] = $translator->trans('cemeteries');
            $variables['subtitle'] = $translator->trans('all') . ' ' . $translator->trans('cemeteries');
            $variables['resources'] = $commonGroundService->getResourceList(['component' => 'grc', 'type' => 'cemeteries'])["hydra:member"];

            return $variables;
        }

        /**
         * @Route("/cemeteries/{id}")
         * @Template
         */
        public
        function cemeteryAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
        {
            $variables = [];

            // If it is a delete action we can stop right here
            if ($request->query->get('action') == 'delete') {
                $commonGroundService->deleteResource(null, ['component' => 'grc', 'type' => 'cemeteries', 'id' => $id]);
                return $this->redirect($this->generateUrl('app_grc_cemeteries'));
            }


            // Lets see if we need to create
            if ($id == 'new') {
                $variables['resource'] = ['@id' => null, 'name' => 'new', 'id' => 'new'];

            } else {
                $variables['resource'] = $commonGroundService->getResource(['component' => 'grc', 'type' => 'cemeteries', 'id' => $id]);
                $variables['graves'] = $commonGroundService->getResourceList(['component'=>'grc','type'=>'graves'],['cemetery.id'=>$id])['hydra:member'];
            }


            /*// If it is a delete action we can stop right here
            if($request->query->get('action') == 'delete'){
                $commonGroundService->deleteResource($variables['resource']);
                return $this->redirect($this->generateUrl('app_grc_graves'));
            }*/


            $variables['title'] = $translator->trans('cemetery');
            $variables['subtitle'] = $translator->trans('save or create a') . ' ' . $translator->trans('cemetery');
            $variables['organizations'] = $commonGroundService->getResourceList(['component' => 'wrc', 'type' => 'organizations'])["hydra:member"];
            $variables['gravetypes'] = $commonGroundService->getResourceList(['component'=>'grc','type'=>'grave_types'])["hydra:member"];
            $variables['places'] = $commonGroundService->getResourceList(['component'=>'lc','type'=>'places'])["hydra:member"];

            // Lets see if there is a post to procces
            if ($request->isMethod('POST')) {

                $resource = $request->request->all();
                $resource['@id'] = $variables['resource']['@id'];
                $resource['id'] = $variables['resource']['id'];

                if($id == 'new'){
                    $calendar = [];
                    $calendar['dateCreated'] = $date;
                    $calendar['dateModified'] = $date;
                    $calendar['name'] = "Calendar " . $_POST['reference'];
                    $calendar['description'] = "Calendar voor begraafplaats " . $_POST['reference'] . " in gemeente " . $organizationName;
                    $calendar['timeZone'] = "CET";

                    $calendar = $commonGroundService->saveResource($calendar, ['component' => 'arc', 'type' => 'calendars']);
                    $cemetery['calendar'] = $calendar['@id'];
                }


                $variables['resource'] = $commonGroundService->saveResource($resource, ['component' => 'grc', 'type' => 'cemeteries']);

                /* @to this redirect is a hotfix */
                if(array_key_exists('id', $variables['resource'])){
                    return $this->redirect($this->generateUrl('app_grc_cemeteries', ["id" =>  $variables['resource']['id']]));
                }

            }

            return $variables;
        }

    /**
     * @Route("/grave_covers")
     * @Template
     */
    public function graveCoversAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('grave covers');
        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('grave covers');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'grc','type'=>'grave_covers'])["hydra:member"];
        return $variables;
    }

    /**
     * @Route("/grave_covers/{id}")
     * @Template
     */
    public function graveCoverAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {

        $variables = [];

        // Lets see if we need to create
        if($id == 'new'){
            $variables['resource'] = ['@id' => null,'id'=>'new','name'=>'new'];
        }
        else{
            $variables['resource'] = $commonGroundService->getResource(['component'=>'grc','type'=>'grave_covers','id'=> $id]);        }

        // If it is a delete action we can stop right here
        if($request->query->get('action') == 'delete'){
            $commonGroundService->deleteResource($variables['resource']);
            return $this->redirect($this->generateUrl('app_grc_gravecovers'));
        }

        $variables['title'] = $translator->trans('grave cover');
        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('grave cover');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'wrc','type'=>'organizations'])["hydra:member"];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])


            $variables['resource'] = $commonGroundService->saveResource($resource,(['component'=>'grc','type'=>'grave_covers']));

            /* @to this redirect is a hotfix */
            if(array_key_exists('id', $variables['resource'])){
                return $this->redirect($this->generateUrl('app_grc_gravecovers', ["id" =>  $variables['resource']['id']]));
            }
        }


        return $variables;
    }


}
