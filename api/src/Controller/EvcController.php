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
 * @Route("/evc")
 */
class EvcController extends AbstractController
{

	/**
	 * @Route("/")
	 * @Template
	 */
	public function indexAction(TranslatorInterface $translator)
	{
		$variables = [];
		$variables['title'] = $translator->trans('Deployments and Enviroments');
		$variables['subtitle'] = $translator->trans('this dashboards alows the administations of kubernetes clusters, enviroment and components');

		return $variables;
	}

    /**
     * @Route("/clusters")
     * @Template
     */
	public function clustersAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
    	$variables = [];
    	$variables['title'] = $translator->trans('clusters');
    	$variables['subtitle'] = $translator->trans('all').' '.$translator->trans('clusters');
    	$variables['resources'] = $commonGroundService->getResourceList(['component'=>'evc', 'type'=>'clusters'])["hydra:member"];

        return $variables;
    }

    /**
     * @Route("/clusters/{id}")
     * @Template
     */
    public function clusterAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {

        $variables = [];

        // Lets see if we need to create
        if($id == 'new'){
            $variables['resource'] = ['@id' => null,'name'=>'new','id'=>'new'];
        }
        else{
            $variables['resource'] = $commonGroundService->getResource(['component'=>'evc', 'type'=>'clusters','id'=>$id]);
        }

        // If it is a delete action we can stop right here
        if($request->query->get('action') == 'delete'){
            $commonGroundService->deleteResource($variables['resource']);
            return $this->redirect($this->generateUrl('app_evc_clusters'));
        }

        $variables['title'] = $translator->trans('cluster');
        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('cluster');

        if($id != 'new'){
            $variables['domains'] = $commonGroundService->getResourceList(['component'=>'evc','type'=>'domains'],['cluster.id'=>$id])['hydra:member'];
            $variables['environments'] = $commonGroundService->getResourceList(['component'=>'evc','type'=>'events'],['cluster.id'=>$id])['hydra:member'];
            $variables['installations'] = $commonGroundService->getResourceList(['component'=>'evc','type'=>'events'],['environment.cluster.id'=>$id])['hydra:member'];
        }

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            if(key_exists('domain', $resource)){
                $domain = $resource['domain'];
                $domain['cluster'] = $resource['@id'];
                if(in_array('id',$domain)){
                    $domain['@id'] = $domain['id'];
                }
                $domain = $commonGroundService->saveResource($domain,['component'=>'evc','type'=>'domains']);
            }
            if(key_exists('environment', $resource)){
                $environment = $resource['environment'];
                $environment['cluster'] = $resource['@id'];
                if(in_array('id',$environment)){
                    $environment['@id'] = $environment['id'];
                }
                $domain = $commonGroundService->saveResource($environment,['component'=>'evc','type'=>'environments']);
            }
            $variables['resource'] = $commonGroundService->saveResource($resource,'https://evc.conduction.nl/clusters/');
        }
        return $variables;
    }

    /**
     * @Route("/health-logs")
     * @Template
     */
    public function healthLogsAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {

    	$variables = [];
    	$variables['title'] = $translator->trans('health logs');
    	$variables['subtitle'] = $translator->trans('all').' '.$translator->trans('health logs');
    	$variables['resources'] = $commonGroundService->getResourceList('https://evc.conduction.nl/health_logs')["hydra:member"];

    	return $variables;

    }

    /**
     * @Route("/health-logs/{id}")
     * @Template
     */
    public function healthLogAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
    	$variables = [];

    	// Lets see if we need to create
    	if($id == 'new'){
    		$variables['resource'] = ['@id' => null,'name'=>'new','id'=>'new'];
    	}
    	else{
    		$variables['resource'] = $commonGroundService->getResource('https://evc.conduction.nl/health_logs/'.$id);
    	}

    	// If it is a delete action we can stop right here
    	if($request->query->get('action') == 'delete'){
    		$commonGroundService->deleteResource($variables['resource']);
    		return $this->redirect($this->generateUrl('app_evc_healthlogs'));
    	}

    	$variables['title'] = $translator->trans('health log');
    	$variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('health log');

    	// Lets see if there is a post to procces
    	if ($request->isMethod('POST')) {

    		// Passing the variables to the resource
    		$resource = $request->request->all();
    		$resource['@id'] = $variables['resource']['@id'];
    		$resource['id'] = $variables['resource']['id'];
    		$variables['resource'] = $commonGroundService->saveResource($resource,'https://evc.conduction.nl/health_logs/');
    	}
    	return $variables;
    }

//    /**
//     * @Route("/environments")
//     * @Template
//     */
//    public function environmentsAction( CommonGroundService $commonGroundService, TranslatorInterface $translator)
//    {
//
//    	$variables = [];
//    	$variables['title'] = $translator->trans('environments');
//    	$variables['subtitle'] = $translator->trans('all').' '.$translator->trans('environments');
//    	$variables['resources'] = $commonGroundService->getResourceList('https://evc.conduction.nl/environments')["hydra:member"];
//
//    	return $variables;
//    }
//
//    /**
//     * @Route("/environments/{id}")
//     * @Template
//     */
//    public function environmentAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
//    {
//    	$variables = [];
//
//    	// Lets see if we need to create
//    	if($id == 'new'){
//    		$variables['resource'] = ['@id' => null,'name'=>'new','id'=>'new'];
//    	}
//    	else{
//    		$variables['resource'] = $commonGroundService->getResource('https://evc.conduction.nl/environments/'.$id);
//    	}
//
//    	// If it is a delete action we can stop right here
//    	if($request->query->get('action') == 'delete'){
//    		$commonGroundService->deleteResource($variables['resource']);
//    		return $this->redirect($this->generateUrl('app_evc_environments'));
//    	}
//
//    	$variables['title'] = $translator->trans('environment');
//    	$variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('environment');
//
//    	// Lets see if there is a post to procces
//    	if ($request->isMethod('POST')) {
//
//    		// Passing the variables to the resource
//    		$resource = $request->request->all();
//    		$resource['@id'] = $variables['resource']['@id'];
//    		$resource['id'] = $variables['resource']['id'];
//
//    		// If there are any sub data sources the need to be removed below in order to save the resource
//    		// unset($resource['somedatasource'])
//
//    		$variables['resource'] = $commonGroundService->saveResource($resource,'https://evc.conduction.nl/environments/');
//    	}
//
//    	return $variables;
//    }
//
    /**
     * @Route("/components")
     * @Template
     */
    public function componentsAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {

    	$variables = [];
    	$variables['title'] = $translator->trans('components');
    	$variables['subtitle'] = $translator->trans('all').' '.$translator->trans('components');
    	$variables['resources'] = $commonGroundService->getResourceList('https://evc.conduction.nl/components')["hydra:member"];

    	return $variables;
    }

    /**
     * @Route("/components/{id}")
     * @Template
     */
    public function componentAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
    	$variables = [];

    	// Lets see if we need to create
    	if($id == 'new'){
    		$variables['resource'] = ['@id' => null,'name'=>'new','id'=>'new'];
    	}
    	else{
    		$variables['resource'] = $commonGroundService->getResource(['component'=>'evc', 'type'=>'components']);
    	}

    	// If it is a delete action we can stop right here
    	if($request->query->get('action') == 'delete'){
    		$commonGroundService->deleteResource($variables['resource']);
    		return $this->redirect($this->generateUrl('app_evc_components'));
    	}

    	$variables['title'] = $translator->trans('component');
    	$variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('component');

        if($id != 'new') {
            $variables['installations'] = $commonGroundService->getResourceList(['component' => 'evc', 'type' => 'installations'], ['component.id' => $id])['hydra:member'];
        }

    	// Lets see if there is a post to procces
    	if ($request->isMethod('POST')) {

    		// Passing the variables to the resource
    		$resource = $request->request->all();
    		$resource['@id'] = $variables['resource']['@id'];
    		$resource['id'] = $variables['resource']['id'];

            if(key_exists('installation', $resource)){
                $installation = $resource['installation'];
                $installation['calendar'] = $resource['@id'];
                if(in_array('id',$installation)){
                    $installation['@id'] = $installation['id'];
                }
                $installation = $commonGroundService->saveResource($installation, ['component'=>'evc','type'=>'installations']);
            }
    		$variables['resource'] = $commonGroundService->saveResource($resource,['component'=>'evc', 'type'=>'components']);
    	}

    	return $variables;
    }

//    /**
//     * @Route("/domains")
//     * @Template
//     */
//    public function domainsAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
//    {
//
//    	$variables = [];
//    	$variables['title'] = $translator->trans('domains');
//    	$variables['subtitle'] = $translator->trans('all').' '.$translator->trans('domains');
//    	$variables['resources'] = $commonGroundService->getResourceList('https://evc.conduction.nl/domains')["hydra:member"];
//
//    	return $variables;
//    }
//
//    /**
//     * @Route("/domains/{id}")
//     * @Template
//     */
//    public function domainAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
//    {
//    	$variables = [];
//
//    	// Lets see if we need to create
//    	if($id == 'new'){
//    		$variables['resource'] = ['@id' => null,'name'=>'new','id'=>'new'];
//    	}
//    	else{
//    		$variables['resource'] = $commonGroundService->getResource('https://evc.conduction.nl/domains/'.$id);
//    	}
//
//    	// If it is a delete action we can stop right here
//    	if($request->query->get('action') == 'delete'){
//    		$commonGroundService->deleteResource($variables['resource']);
//    		return $this->redirect($this->generateUrl('app_evc_domains'));
//    	}
//
//
//    	$variables['title'] = $translator->trans('domain');
//    	$variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('domain');
//
//    	// Lets see if there is a post to procces
//    	if ($request->isMethod('POST')) {
//
//    		// Passing the variables to the resource
//    		$resource = $request->request->all();
//    		$resource['@id'] = $variables['resource']['@id'];
//    		$resource['id'] = $variables['resource']['id'];
//
//    		// If there are any sub data sources the need to be removed below in order to save the resource
//    		// unset($resource['somedatasource'])
//
//    		$variables['resource'] = $commonGroundService->saveResource($resource,'https://evc.conduction.nl/domains/');
//    	}
//
//    	return $variables;
//    }
//
//    /**
//     * @Route("/installations")
//     * @Template
//     */
//    public function installationsAction( CommonGroundService $commonGroundService, TranslatorInterface $translator)
//    {
//
//    	$variables = [];
//    	$variables['title'] = $translator->trans('installation');
//    	$variables['subtitle'] = $translator->trans('all').' '.$translator->trans('installation');
//    	$variables['resources'] = $commonGroundService->getResourceList('https://evc.conduction.nl/installations')["hydra:member"];
//
//    	return $variables;
//    }
//
//    /**
//     * @Route("/installations/{id}")
//     * @Template
//     */
//    public function installationAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
//    {
//    	$variables = [];
//
//    	// Lets see if we need to create
//    	if($id == 'new'){
//    		$variables['resource'] = ['@id' => null,'name'=>'new','id'=>'new'];
//    	}
//    	else{
//    		$variables['resource'] = $commonGroundService->getResource('https://evc.conduction.nl/installations/'.$id);
//    	}
//
//    	// If it is a delete action we can stop right here
//    	if($request->query->get('action') == 'delete'){
//    		$commonGroundService->deleteResource($variables['resource']);
//    		return $this->redirect($this->generateUrl('app_evc_installations'));
//    	}
//    	if($request->query->get('action') == 'install'){
//    		$commonGroundService->getResource($variables['resource'].'/install');
//    		return $this->redirect($this->generateUrl('app_evc_installations'));
//    	}
//    	if($request->query->get('action') == 'update'){
//    		$commonGroundService->getResource($variables['resource'].'/update');
//    		return $this->redirect($this->generateUrl('app_evc_installations'));
//    	}
//    	if($request->query->get('action') == 'update'){
//    		$commonGroundService->getResource($variables['resource'].'/delete');
//    		return $this->redirect($this->generateUrl('app_evc_installations'));
//    	}
//
//    }




}
