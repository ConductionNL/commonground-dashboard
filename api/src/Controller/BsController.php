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
	/**
	 * @Route("/")
	 * @Template
	 */
	public function indexAction(TranslatorInterface $translator)
	{
		$variables = [];
		$variables['title'] = $translator->trans('Betaal Service');
		$variables['subtitle'] = $translator->trans('the location catalogue holds al data concerning accomodations, places, changelogs and auditrails.');

		return $variables;
	}

    /**
     * @Route("/services")
     * @Template
     */
    public function servicesAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('services');
        $variables['subtitle'] = $translator->trans('all') . ' ' . $translator->trans('services');
        $variables['resources'] = $commonGroundService->getResourceList('https://bs.huwelijksplanner.online/services')["hydra:member"];

        return $variables;
    }

    /**
     * @Route("/services/{id}")
     * @Template
     */
    public function serviceAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {

        $variables = [];
        $variables['title'] = $translator->trans('service');
        $variables['subtitle'] = $translator->trans('save or create a') . ' ' . $translator->trans('services');
        $variables['resource'] = $commonGroundService->getResource('https://bs.huwelijksplanner.online/services/' . $id);

        return $variables;
    }

    /**
     * @Route("/messages")
     * @Template
     */
    public function messagesAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('messages');
        $variables['subtitle'] = $translator->trans('all') . ' ' . $translator->trans('messages');
        $variables['resources'] = $commonGroundService->getResourceList('https://bs.huwelijksplanner.online/messages')["hydra:member"];

        return $variables;

    }

    /**
     * @Route("/messages/{id}")
     * @Template
     */
    public function messageAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
        $variables = [];
        $variables['title'] = $translator->trans('message');
        $variables['subtitle'] = $translator->trans('save or create a') . ' ' . $translator->trans('messages');
        $variables['resource'] = $commonGroundService->getResource('https://bs.huwelijksplanner.online/messages/' . $id);

        return $variables;
    }

}






