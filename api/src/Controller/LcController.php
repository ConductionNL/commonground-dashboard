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
 * @Route("/lc")
 */
class LcController extends AbstractController
{

//	/**
//	 * @Route("/")
//	 * @Template
//	 */
//	public function indexAction(TranslatorInterface $translator)
//	{
//		$variables = [];
//		$variables['title'] = $translator->trans('location catalogue');
//		$variables['subtitle'] = $translator->trans('the location catalogue holds al data concerning accomodations, places, changelogs and auditrails.');
//
//		return $variables;
//	}

    /**
     * @Route("/accommodations")
     * @Template
     */
	public function accommodationsAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
    	$variables = [];
    	$variables['title'] = $translator->trans('accommodations');
    	$variables['subtitle'] = $translator->trans('all').' '.$translator->trans('accommodations');
    	$variables['resources'] = $commonGroundService->getResourceList('https://lc.huwelijksplanner.online/accommodations')["hydra:member"];

        return $variables;
    }

    /**
     * @Route("/accommodation/{id}")
     * @Template
     */
    public function accommodationAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {

    	$variables = [];
    	$variables['title'] = $translator->trans('accommodation');
    	$variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('accommodation');
    	$variables['resource'] = $commonGroundService->getResource('https://lc.huwelijksplanner.online/accommodations/'.$id);



//    	// Lets see if we need to create
//    	if($id == 'new') {
//            $variables['resource'] = ['@id' => null, 'name' => 'new', 'id' => 'new'];
//        }
//    	else{
//    		$variables['resource'] = $commonGroundService->getResource('https://pdc.huwelijksplanner.online/products/'.$id);
//    	}

//    	// Lets see if there is a post to procces
//    	if ($request->isMethod('POST')) {
//
//    		// Passing the variables to the resource
//    		$resource = $request->request->all();
//
//    		foreach ($variables['resource']['groups'] as $group){
//    			$resource['groups'][] = 'groups/'.$group['id'];
//    		}
//
//    		if($resource['addgroup'] != ""){
//    			$resource['groups'][] = $resource['addgroup'];
//    		}
//
//    		if($resource['removegroup'] != ""){
//    			foreach($resource['groups'] as $key=>$group){
//    				if($group == $resource['removegroup']){
//    					unset($resource['groups'][$key]);
//    				}
//    			}
//    		}
//
//    		$resource['@id'] = $variables['resource']['@id'];
//    		$resource['id'] = $variables['resource']['id'];

    		// If there are any sub data sources the need to be removed below in order to save the resource
    		// unset($resource['somedatasource'])
//
//    		$variables['resource'] = $commonGroundService->saveResource($resource,'https://pdc.huwelijksplanner.online/products/');
//    	}

        return $variables;
    }

    /**
     * @Route("/places")
     * @Template
     */
    public function placesAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {

    	$variables = [];
    	$variables['title'] = $translator->trans('places');
    	$variables['subtitle'] = $translator->trans('all').' '.$translator->trans('places');
    	$variables['resources'] = $commonGroundService->getResourceList('https://lc.huwelijksplanner.online/places')["hydra:member"];

    	return $variables;
    }

    /**
     * @Route("/places/{id}")
     * @Template
     */
    public function placeAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
        $variables = [];
        $variables['title'] = $translator->trans('place');
        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('place');
        $variables['resource'] = $commonGroundService->getResource('https://lc.huwelijksplanner.online/places/'.$id);

    	return $variables;
    }

    /**
     * @Route("/change-logs")
     * @Template
     */
    public function changeLogsAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {

    	$variables = [];
    	$variables['title'] = $translator->trans('change log');
    	$variables['subtitle'] = $translator->trans('all').' '.$translator->trans('change logs');
    	$variables['resources'] = $commonGroundService->getResourceList('https://lc.huwelijksplanner.online/changeLogs')["hydra:member"];

    	return $variables;
    }

    /**
     * @Route("/change-logs/{id}")
     * @Template
     */
    public function changeLogAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
        $variables = [];
        $variables['title'] = $translator->trans('change log');
        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('change log');
        $variables['resource'] = $commonGroundService->getResource('https://lc.huwelijksplanner.online/changeLogs/'.$id);

    	return $variables;
    }

    /**
     * @Route("/audit-trails")
     * @Template
     */
    public function auditTrailsAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {

    	$variables = [];
    	$variables['title'] = $translator->trans('audit trails');
    	$variables['subtitle'] = $translator->trans('all').' '.$translator->trans('audit trails');
    	$variables['resources'] = $commonGroundService->getResourceList('https://lc.huwelijksplanner.online/auditTrails')["hydra:member"];

    	return $variables;
    }

    /**
     * @Route("/audit-trails/{id}")
     * @Template
     */
    public function auditTrailAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
        $variables = [];
        $variables['title'] = $translator->trans('audit trail');
        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('audit trail');
        $variables['resource'] = $commonGroundService->getResource('https://lc.huwelijksplanner.online/auditTrails/'.$id);

    	return $variables;
    }



}
