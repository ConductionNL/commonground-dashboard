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
 * @Route("/bc")
 */
class BcController extends AbstractController
{

    /**
     * @Route("/")
     * @Template
     */
    public function indexAction(TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('Betaal Component');
        $variables['subtitle'] = $translator->trans('the location catalogue holds al data concerning accomodations, places, changelogs and auditrails.');

        return $variables;
    }

    /**
     * @Route("/payments")
     * @Template
     */
    public function paymentsAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('payments');
        $variables['subtitle'] = $translator->trans('all') . ' ' . $translator->trans('payments');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'bc','type'=>'payments'])["hydra:member"];

        return $variables;
    }

    /**
     * @Route("/payments/{id}")
     * @Template
     */
    public function paymentAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
        $variables = [];

        // If it is a delete action we can stop right here
        if($request->query->get('action') == 'delete'){
            $commonGroundService->deleteResource($variables['resource']);
            return $this->redirect($this->generateUrl('app_bc_payments'));
        }

        // Lets see if we need to create
        if($id == 'new'){
            $variables['resource'] = ['@id' => null,'name'=>'new','id'=>'new'];
        }
        else{
            $variables['resource'] = $commonGroundService->getResource(['component'=>'bc','type'=>'payments','id'=>$id]);
        }


        $variables['title'] = $translator->trans('payments');
        $variables['subtitle'] = $translator->trans('save or create a') . ' ' . $translator->trans('payments');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'bc','type'=>'organizations'])["hydra:member"];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])

            $variables['resource'] = $commonGroundService->saveResource($resource,['component'=>'bc','type'=>'slugs']);
        }

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
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'bc','type'=>'services'])["hydra:member"];

        return $variables;

    }

    /**
     * @Route("/services/{id}")
     * @Template
     */
    public function serviceAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
        $variables = [];

        // Lets see if we need to create
        if($id == 'new'){
            $variables['resource'] = ['@id' => null,'id'=>'new'];
        }
        else{
            $variables['resource'] = $commonGroundService->getResource(['component'=>'bc','type'=>'services','id'=>$id]);
        }

        // If it is a delete action we can stop right here
        if($request->query->get('action') == 'delete'){
            $commonGroundService->deleteResource($variables['resource']);
            return $this->redirect($this->generateUrl('app_bc_services'));
        }

        $variables['title'] = $translator->trans('payments');
        $variables['subtitle'] = $translator->trans('save or create a') . ' ' . $translator->trans('payments');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'bc','type'=>'organizations'])["hydra:member"];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])

            $variables['resource'] = $commonGroundService->saveResource($resource,(['component'=>'bc','type'=>'resource','id'=>$id]));
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
        $variables['subtitle'] = $translator->trans('all') . ' ' . $translator->trans('organizations');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'bc','type'=>'organizations'])["hydra:member"];

        return $variables;
    }

    /**
     * @Route("/organization/{id}")
     * @Template
     */
    public function organizationAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {

        $variables = [];
// If it is a delete action we can stop right here
        if($request->query->get('action') == 'delete'){
            $commonGroundService->deleteResource(['component'=>'bc','type'=>'organization','id'=> $id]);
            return $this->redirect($this->generateUrl('app_bc_organizations'));
        }

        // Lets see if we need to create
        if($id == 'new'){
            $variables['resource'] = ['@id' => null,'id'=>'new'];
        }
        else{
            $variables['resource'] = $commonGroundService->getResource(['component'=>'bc','type'=>'organizations','id'=> $id]);
        }



        $variables['title'] = $translator->trans('organizations');
        $variables['subtitle'] = $translator->trans('save or create a') . ' ' . $translator->trans('organizations');
        $variables['slugs'] = $commonGroundService->getResourceList(['component'=>'bc','type'=>'slugs'],['organization.id'=>$id])["hydra:member"];
        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])

            $variables['resource'] = $commonGroundService->saveResource($resource,(['component'=>'bc','type'=>'resource','id'=>$id]));
        }
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
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'bc','type'=>'invoice'])["hydra:member"];
        return $variables;
    }

    /**
     * @Route("/invoices/{id}")
     * @Template
     */
    public function invoiceAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {

        $variables = [];

        // Lets see if we need to create
        if($id == 'new'){
            $variables['resource'] = ['@id' => null,'id'=>'new'];
        }
        else{
            $variables['resource'] = $commonGroundService->getResource(['component'=>'bc','type'=>'invoices','id'=> $id]);        }

        // If it is a delete action we can stop right here
        if($request->query->get('action') == 'delete'){
            $commonGroundService->deleteResource($variables['resource']);
            return $this->redirect($this->generateUrl('app_bc_invoices'));
        }

        $variables['title'] = $translator->trans('invoices');
        $variables['subtitle'] = $translator->trans('save or create a') . ' ' . $translator->trans('invoices');
        $variables['slugs'] = $commonGroundService->getResourceList(['component'=>'bc','type'=>'slugs'],['invoices.id'=>$id])["hydra:member"];
        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])

            $variables['resource'] = $commonGroundService->saveResource($resource,(['component'=>'bc','type'=>'resource','id'=>$id]));        }
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
        $variables['resource'] = $commonGroundService->getResourceList(['component'=>'bc','type'=>'resource'])["hydra:member"];
        return $variables;
    }

    /**
     * @Route("/invoice-item/{id}")
     * @Template
     */
    public function invoiceitemAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {

        $variables = [];

        // Lets see if we need to create
        if($id == 'new'){
            $variables['resource'] = ['@id' => null,'id'=>'new'];
        }
        else{
            $variables['resource'] = $commonGroundService->getResource(['component'=>'bc','type'=>'invoice-item','id'=> $id]);
        }

        // If it is a delete action we can stop right here
        if($request->query->get('action') == 'delete'){
            $commonGroundService->deleteResource($variables['resource']);
            return $this->redirect($this->generateUrl('app_bc_invoices'));
        }

        $variables['title'] = $translator->trans('invoices');
        $variables['subtitle'] = $translator->trans('save or create a') . ' ' . $translator->trans('invoices');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'bc','type'=>'slugs'],['invoice-item.id'=>$id])["hydra:member"];


        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])


            $variables['resource'] = $commonGroundService->saveResource($resource,(['component'=>'bc','type'=>'resource','id'=>$id]));
        }
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
        $variables['resources'] = $commonGroundService->getResource(['component'=>'bc','type'=>'taxes']);
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
            $variables['resource'] = ['@id' => null,'id'=>'new'];
        }
        else{
            $variables['resource'] = $commonGroundService->getResource(['component'=>'bc','type'=>'taxes','id'=> $id]);
        }

        // If it is a delete action we can stop right here
        if($request->query->get('action') == 'delete'){
            $commonGroundService->deleteResource($variables['resource']);
            return $this->redirect($this->generateUrl('app_bc_taxes'));
        }

        $variables['title'] = $translator->trans('taxes');
        $variables['subtitle'] = $translator->trans('save or create a') . ' ' . $translator->trans('taxes');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'bc','type'=>'slugs'],['taxes.id'=>$id])["hydra:member"];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])

            $variables['resource'] = $commonGroundService->saveResource($resource,(['component'=>'bc','type'=>'resource','id'=>$id]));
        }
    }
}
