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
 * Class LtcController
 * @package App\Controller
 * @Route("/ltc")
 */
class LtcController extends AbstractController
{

	/**
	 * @Route("/")
	 * @Template
	 */
	public function indexAction(TranslatorInterface $translator)
	{
		$variables = [];
		$variables['title'] = $translator->trans('Landelijke tabellen catalogus');
		$variables['subtitle'] = $translator->trans('catalogus van de landelijke tabellen');

		return $variables;
	}

    /**
     * @Route("/rsin")
     * @Template
     */
	public function rsinAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
    	$variables = [];
    	$variables['title'] = $translator->trans('rsin');
    	$variables['subtitle'] = $translator->trans('all').' '.$translator->trans('rsin');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'ltc','type'=>'rsin'])["hydra:member"];
        return $variables;
    }

    /**
     * @Route("/tabel32")
     * @Template
     */
    public function tabel32Action(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('tabel32');
        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('tabel32');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'ltc','type'=>'tabel32'])["hydra:member"];
        return $variables;
    }

    /**
     * @Route("/tabel33")
     * @Template
     */
    public function tabel33Action(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('tabel33');
        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('tabel33');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'ltc','type'=>'tabel33'])["hydra:member"];
        return $variables;
    }

    /**
     * @Route("/tabel34")
     * @Template
     */
    public function tabel34Action(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('tabel34');
        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('tabel34');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'ltc','type'=>'tabel34'])["hydra:member"];
        return $variables;
    }

    /**
     * @Route("/tabel36")
     * @Template
     */
    public function tabel36Action(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('tabel36');
        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('tabel36');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'ltc','type'=>'tabel36'])["hydra:member"];
        return $variables;
    }

    /**
     * @Route("/tabel37")
     * @Template
     */
    public function tabel37Action(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('tabel37');
        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('tabel37');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'ltc','type'=>'tabel37'])["hydra:member"];
        return $variables;
    }

    /**
     * @Route("/tabel38")
     * @Template
     */
    public function tabel38Action(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('tabel38');
        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('tabel38');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'ltc','type'=>'tabel38'])["hydra:member"];
        return $variables;
    }

    /**
     * @Route("/tabel39")
     * @Template
     */
    public function tabel39Action(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('tabel39');
        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('tabel39');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'ltc','type'=>'tabel39'])["hydra:member"];
        return $variables;
    }

    /**
     * @Route("/tabel41")
     * @Template
     */
    public function tabel41Action(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('tabel41');
        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('tabel41');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'ltc','type'=>'tabel41'])["hydra:member"];
        return $variables;
    }

    /**
     * @Route("/tabel48")
     * @Template
     */
    public function tabel48Action(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('tabel48');
        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('tabel48');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'ltc','type'=>'tabel48'])["hydra:member"];
        return $variables;
    }

    /**
     * @Route("/tabel49")
     * @Template
     */
    public function tabel49Action(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('tabel49');
        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('tabel49');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'ltc','type'=>'tabel49'])["hydra:member"];
        return $variables;
    }

    /**
     * @Route("/tabel55")
     * @Template
     */
    public function tabel55Action(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('tabel55');
        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('tabel55');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'ltc','type'=>'tabel55'])["hydra:member"];
        return $variables;
    }

    /**
     * @Route("/tabel56")
     * @Template
     */
    public function tabel56Action(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('tabel56');
        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('tabel56');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'ltc','type'=>'tabel56'])["hydra:member"];
        return $variables;
    }


}
