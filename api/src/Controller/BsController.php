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
 * Class BsController
 * @package App\Controller
 * @Route("/bs")
 */
class BsController extends AbstractController
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
 * @Route("/payments")
 * @Template
 */
    public function paymentsAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('payments');
        $variables['subtitle'] = $translator->trans('all') . ' ' . $translator->trans('payments');
        $variables['resources'] = $commonGroundService->getResourceList('https://bc.huwelijksplanner.online/payments')["hydra:member"];

        return $variables;
    }

    /**
     * @Route("/payments/{id}")
     * @Template
     */
    public function paymentAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {

        $variables = [];
        $variables['title'] = $translator->trans('payments');
        $variables['subtitle'] = $translator->trans('save or create a') . ' ' . $translator->trans('payments');
        $variables['resource'] = $commonGroundService->getResource('https://bc.huwelijksplanner.online/payments/' . $id);


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
     * @Route("/services")
     * @Template
     */
    public function servicesAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {

        $variables = [];
        $variables['title'] = $translator->trans('places');
        $variables['subtitle'] = $translator->trans('all') . ' ' . $translator->trans('services');
        $variables['resources'] = $commonGroundService->getResourceList('https://lc.huwelijksplanner.online/services')["hydra:member"];

        return $variables;

    }

    /**
     * @Route("/services/{id}")
     * @Template
     */
    public function serviceAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
        $variables = [];


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
        $variables['subtitle'] = $translator->trans('all') . ' ' . $translator->trans('organizations');
        $variables['resources'] = $commonGroundService->getResourceList('https://bc.huwelijksplanner.online/organizations')["hydra:member"];

        return $variables;
    }
    /**
     * @Route("/organization/{id}")
     * @Template
     */
    public function organizationAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {

        $variables = [];
        $variables['title'] = $translator->trans('organizations');
        $variables['subtitle'] = $translator->trans('save or create a') . ' ' . $translator->trans('organizations');
        $variables['resource'] = $commonGroundService->getResource('https://bc.huwelijksplanner.online/organizations/' . $id);
    }



    /**
     * @Route("/invoices")
     * @Template
     */
    public function invoicesAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {

        $variables = [];
        $variables['title'] = $translator->trans('invoice');
        $variables['subtitle'] = $translator->trans('all') . ' ' . $translator->trans('invoice');
        $variables['resources'] = $commonGroundService->getResourceList('https://bc.huwelijksplanner.online/invoice')["hydra:member"];

        return $variables;
    }
    /**
     * @Route("/invoices/{id}")
     * @Template
     */
    public function invoiceAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {

        $variables = [];
        $variables['title'] = $translator->trans('invoices');
        $variables['subtitle'] = $translator->trans('save or create a') . ' ' . $translator->trans('invoices');
        $variables['resource'] = $commonGroundService->getResource('https://bc.huwelijksplanner.online/invoices/' . $id);
    }


/**
* @Route("/invoice-items")
* @Template
*/
    public function invoiceitemsAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {

        $variables = [];
        $variables['title'] = $translator->trans('invoice');
        $variables['subtitle'] = $translator->trans('all') . ' ' . $translator->trans('invoice');
        $variables['resources'] = $commonGroundService->getResourceList('https://bc.huwelijksplanner.online/invoice')["hydra:member"];

        return $variables;
    }
    /**
     * @Route("/invoice-item/{id}")
     * @Template
     */
    public function invoiceitemAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {

        $variables = [];
        $variables['title'] = $translator->trans('invoices');
        $variables['subtitle'] = $translator->trans('save or create a') . ' ' . $translator->trans('invoices');
        $variables['resource'] = $commonGroundService->getResource('https://bc.huwelijksplanner.online/invoices/' . $id);
    }




/**
* @Route("/taxes")
* @Template
*/
    public function taxesAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {

        $variables = [];
        $variables['title'] = $translator->trans('tax');
        $variables['subtitle'] = $translator->trans('all') . ' ' . $translator->trans('tax');
        $variables['resources'] = $commonGroundService->getResourceList('https://bc.huwelijksplanner.online/tax')["hydra:member"];

        return $variables;
    }
    /**
     * @Route("/taxes/{id}")
     * @Template
     */
    public function taxAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {

        $variables = [];
        $variables['title'] = $translator->trans('taxes');
        $variables['subtitle'] = $translator->trans('save or create a') . ' ' . $translator->trans('taxes');
        $variables['resource'] = $commonGroundService->getResource('https://bc.huwelijksplanner.online/taxes/' . $id);
    }




}






