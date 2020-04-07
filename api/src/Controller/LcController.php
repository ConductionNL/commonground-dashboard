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

}
