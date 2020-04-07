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
 * Class MrcController
 * @package App\Controller
 * @Route("/cc")
 */
class CcController extends AbstractController
{

	/**
	 * @Route("/")
	 * @Template
	 */
	public function indexAction(TranslatorInterface $translator)
	{
		$variables = [];
		$variables['title'] = $translator->trans('location catalogue');
		$variables['subtitle'] = $translator->trans('the location catalogue holds al data concerning accomodations, places, changelogs and auditrails.');

		return $variables;
	}

    /**
     * @Route("/persons")
     * @Template
     */
	public function personsAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
    	$variables = [];
    	$variables['title'] = $translator->trans('persons');
    	$variables['subtitle'] = $translator->trans('all').' '.$translator->trans('persons');
    	$variables['resources'] = $commonGroundService->getResourceList('https://cc.huwelijksplanner.online/persons')["hydra:member"];

        return $variables;
    }

    /**
     * @Route("/persons/{id}")
     * @Template
     */
    public function personAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {

    	$variables = [];
    	$variables['title'] = $translator->trans('person');
    	$variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('person');
    	$variables['resource'] = $commonGroundService->getResource('https://cc.huwelijksplanner.online/person/'.$id);

        return $variables;
    }

    /**
     * @Route("/addresses")
     * @Template
     */
	public function addressesAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
    	$variables = [];
    	$variables['title'] = $translator->trans('addresses');
    	$variables['subtitle'] = $translator->trans('all').' '.$translator->trans('addresses');
    	$variables['resources'] = $commonGroundService->getResourceList('https://cc.huwelijksplanner.online/addresses')["hydra:member"];

        return $variables;
    }

    /**
     * @Route("/adresses/{id}")
     * @Template
     */
    public function addressAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {

    	$variables = [];
    	$variables['title'] = $translator->trans('address');
    	$variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('address');
    	$variables['resource'] = $commonGroundService->getResource('https://cc.huwelijksplanner.online/addresses/'.$id);

        return $variables;
    }

    /**
     * @Route("/emails")
     * @Template
     */
	public function emailsAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
    	$variables = [];
    	$variables['title'] = $translator->trans('emails');
    	$variables['subtitle'] = $translator->trans('all').' '.$translator->trans('emails');
    	$variables['resources'] = $commonGroundService->getResourceList('https://cc.huwelijksplanner.online/emails')["hydra:member"];

        return $variables;
    }

    /**
     * @Route("/emails/{id}")
     * @Template
     */
    public function emailAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {

    	$variables = [];
    	$variables['title'] = $translator->trans('email');
    	$variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('email');
    	$variables['resource'] = $commonGroundService->getResource('https://cc.huwelijksplanner.online/emails/'.$id);

        return $variables;
    }

    /**
     * @Route("/telephones")
     * @Template
     */
	public function telephonesAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
    	$variables = [];
    	$variables['title'] = $translator->trans('telephones');
    	$variables['subtitle'] = $translator->trans('all').' '.$translator->trans('telephones');
    	$variables['resources'] = $commonGroundService->getResourceList('https://cc.huwelijksplanner.online/telephones')["hydra:member"];

        return $variables;
    }

    /**
     * @Route("/telephones/{id}")
     * @Template
     */
    public function telephoneAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {

    	$variables = [];
    	$variables['title'] = $translator->trans('telephone');
    	$variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('telephones');
    	$variables['resource'] = $commonGroundService->getResource('https://cc.huwelijksplanner.online/telephones/'.$id);

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
    	$variables['resources'] = $commonGroundService->getResourceList('https://cc.huwelijksplanner.online/organizations')["hydra:member"];

        return $variables;
    }

    /**
     * @Route("/organizations/{id}")
     * @Template
     */
    public function organizationAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {

    	$variables = [];
    	$variables['title'] = $translator->trans('organization');
    	$variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('organizations');
    	$variables['resource'] = $commonGroundService->getResource('https://cc.huwelijksplanner.online/organizations/'.$id);

        return $variables;
    }

    /**
     * @Route("/contact-lists")
     * @Template
     */
	public function contactListsAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
    	$variables = [];
    	$variables['title'] = $translator->trans('contact lists');
    	$variables['subtitle'] = $translator->trans('all').' '.$translator->trans('contact lists');
    	$variables['resources'] = $commonGroundService->getResourceList('https://cc.huwelijksplanner.online/contactLists')["hydra:member"];

        return $variables;
    }

    /**
     * @Route("/contact-lists/{id}")
     * @Template
     */
    public function contactListAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {

    	$variables = [];
    	$variables['title'] = $translator->trans('contact list');
    	$variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('contact lists');
    	$variables['resource'] = $commonGroundService->getResource('https://cc.huwelijksplanner.online/contactLists/'.$id);

        return $variables;
    }

}
