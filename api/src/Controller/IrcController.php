<?php
// src/Controller/DefaultController.php
namespace App\Controller;

use Conduction\CommonGroundBundle\Service\CommonGroundService;
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
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class PdcController
 * @package App\Controller
 * @Route("/irc")
 */
class IrcController extends AbstractController
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
     * @Route("/assents")
     * @Template
     */
	public function assentsAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
    	$variables = [];
    	$variables['title'] = $translator->trans('assents');
    	$variables['subtitle'] = $translator->trans('all').' '.$translator->trans('assents');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'irc','type'=>'assents'])["hydra:member"];
        return $variables;
    }

    /**
     * @Route("/assents/{id}")
     * @Template
     */
    public function assentAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {

        $variables = [];

        // Lets see if we need to create
        if($id == 'new'){
            $variables['resource'] = ['@id' => null,'id'=>'new', 'name'=>'new'];
        }
        else{
            $variables['resource'] = $commonGroundService->getResource(['component'=>'irc','type'=>'assents','id'=> $id]);
        }

        // If it is a delete action we can stop right here
        if($request->query->get('action') == 'delete'){
            $commonGroundService->deleteResource($variables['resource']);
            return $this->redirect($this->generateUrl('app_irc_assents'));
        }

        $variables['title'] = $translator->trans('assent');
        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('assent');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'wrc','type'=>'organizations'])["hydra:member"];
        $variables['employees'] = $commonGroundService->getResourceList(['component'=>'mrc','type'=>'employees'])["hydra:member"];
        $variables['people'] = $commonGroundService->getResourceList(['component'=>'cc','type'=>'people'])["hydra:member"];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])

            $variables['resource'] = $commonGroundService->saveResource($resource,(['component'=>'irc','type'=>'assents']));
            /* @to this redirect is a hotfix */
            if(array_key_exists('id', $variables['resource'])){
                return $this->redirect($this->generateUrl('app_irc_assents', ["id" =>  $variables['resource']['id']]));
            }
        }

        return $variables;
    }
}
