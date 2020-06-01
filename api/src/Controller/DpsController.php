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
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class dpsController
 * @package App\Controller
 * @Route("/dps")
 */
class DpsController extends AbstractController
{

	/**
	 * @Route("/")
	 * @Template
	 */
	public function indexAction(TranslatorInterface $translator)
	{
		$variables = [];
		$variables['title'] = $translator->trans('doc parsers');
		$variables['subtitle'] = $translator->trans('The doc parser holds doc parsers');

		return $variables;
	}

    /**
     * @Route("/api_docs")
     * @Template
     */
	public function ApiDocsAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
    	$variables = [];
    	$variables['title'] = $translator->trans('api docs');
    	$variables['subtitle'] = $translator->trans('all').' '.$translator->trans('api docs');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'dps','type'=>'api_docs'])["hydra:member"];
        return $variables;
    }

    /**
     * @Route("/api_docs/{id}")
     * @Template
     */
    public function ApiDocAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {

    	$variables = [];

        // Lets see if we need to create
        if($id == 'new'){
            $variables['resource'] = ['@id' => null,'id'=>'new','name'=>'new'];
        }
        else{
            $variables['resource'] = $commonGroundService->getResource(['component'=>'dps','type'=>'api_docs','id'=> $id]);        }

        // If it is a delete action we can stop right here
        if($request->query->get('action') == 'delete'){
            $commonGroundService->deleteResource($variables['resource']);
            return $this->redirect($this->generateUrl('app_dps_apidocs'));
        }

        $variables['title'] = $translator->trans('api doc');
    	$variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('api doc');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'dps','type'=>'organizations'])["hydra:member"];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])


            $variables['resource'] = $commonGroundService->saveResource($resource,(['component'=>'dps','type'=>'api_docs','id'=>$id]));

            /* @to this redirect is a hotfix */
            if(array_key_exists('id', $variables['resource'])){
                return $this->redirect($this->generateUrl('app_dps_apidocs', ["id" =>  $variables['resource']['id']]));
            }
        }


        return $variables;
    }


}
