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
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Conduction\CommonGroundBundle\Security\User\CommongroundUser;
use Conduction\CommonGroundBundle\Service\CommonGroundService;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class PdcController
 * @package App\Controller
 * @Route("/stuf")
 */
class StufController extends AbstractController
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
     * @Route("/stuf_interfaces")
     * @Template
     */
    public function StufInterfacesAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('stuf_interfaces');
        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('stuf_interfaces');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'stuf','type'=>'stuf_interfaces'])["hydra:member"];
        return $variables;
    }

    /**
     * @Route("/stuf_interfaces/{id}")
     * @Template
     */
    public function StufInterfaceAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {

        $variables = [];

        // Lets see if we need to create
        if($id == 'new'){
            $variables['resource'] = ['@id' => null,'id'=>'new','name'=>'new'];
        }
        else{
            $variables['resource'] = $commonGroundService->getResource(['component'=>'stuf','type'=>'stufinterface','id'=> $id]);        }

        // If it is a delete action we can stop right here
        if($request->query->get('action') == 'delete'){
            $commonGroundService->deleteResource($variables['resource']);
            return $this->redirect($this->generateUrl('app_stuf_stufinterfaces'));
        }

        $variables['title'] = $translator->trans('stufinterface');
        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('stufinterface');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'stuf','type'=>'organizations'])["hydra:member"];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])


            $variables['resource'] = $commonGroundService->saveResource($resource,(['component'=>'stuf','type'=>'stufinterface','id'=>$id]));
        }


        return $variables;
    }
}
