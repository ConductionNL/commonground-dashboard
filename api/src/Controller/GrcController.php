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
            $commonGroundService->deleteResource(['component'=>'grc','type'=>'graves','id'=> $id]);
            return $this->redirect($this->generateUrl('app_grc_graves'));
        }

        $variables = [];

        // Lets see if we need to create
        if($id == 'new'){
            $variables['resource'] = ['@id' => null,'id'=>'new'];
        }
        else{
            $variables['resource'] = $commonGroundService->getResource(['component'=>'grc','type'=>'graves','id'=> $id]);
        }

        /*
        // If it is a delete action we can stop right here
        if($request->query->get('action') == 'delete'){
            $commonGroundService->deleteResource($variables['resource']);
            return $this->redirect($this->generateUrl('app_grc_graves'));
        }
        */

        $variables['title'] = $translator->trans('grave');
        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('grave');
        $variables['cemeteries'] = $commonGroundService->getResourceList(['component'=>'grc','type'=>'cemeteries'])["hydra:member"];
        $variables['gravetypes'] = $commonGroundService->getResourceList(['component'=>'grc','type'=>'grave_types'])["hydra:member"];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            $variables['resource'] = $commonGroundService->saveResource($resource, ['component'=>'grc','type'=>'graves']);
        }

        return $variables;
    }


}
