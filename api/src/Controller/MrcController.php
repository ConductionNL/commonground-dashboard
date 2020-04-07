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
 * @Route("/mrc")
 */
class MrcController extends AbstractController
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
     * @Route("/employees")
     * @Template
     */
	public function employeesAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
    	$variables = [];
    	$variables['title'] = $translator->trans('employees');
    	$variables['subtitle'] = $translator->trans('all').' '.$translator->trans('employees');
    	$variables['resources'] = $commonGroundService->getResourceList('https://mrc.huwelijksplanner.online/employees')["hydra:member"];

        return $variables;
    }

    /**
     * @Route("/employees/{id}")
     * @Template
     */
    public function employeeAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {

    	$variables = [];
    	$variables['title'] = $translator->trans('employee');
    	$variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('employee');
    	$variables['resource'] = $commonGroundService->getResource('https://mrc.huwelijksplanner.online/employees/'.$id);

        return $variables;
    }
}
