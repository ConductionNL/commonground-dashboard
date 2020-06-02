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
 * Class ChrcController
 * @package App\Controller
 * @Route("/chrc")
 */
class ChrcController extends AbstractController
{
	/**
	 * @Route("/")
	 * @Template
	 */
	public function indexAction(TranslatorInterface $translator)
	{
		$variables = [];
		$variables['title'] = $translator->trans('Challenge Component');
		$variables['subtitle'] = $translator->trans('Component for adding challenges');

		return $variables;
	}

    /**
     * @Route("/pitches")
     * @Template
     */
    public function pitchesAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('pitches');
        $variables['subtitle'] = $translator->trans('all') . ' ' . $translator->trans('pitches');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'chrc','type'=>'pitches']);
        return $variables;
    }

    /**
     * @Route("/pitches/{id}")
     * @Template
     */
    public function pitchAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {

        $variables = [];

        // Lets see if we need to create
        if($id == 'new'){
            $variables['resource'] = ['@id' => null,'id'=>'new','name'=>'new'];
        }
        else{
            $variables['resource'] = $commonGroundService->getResource(['component'=>'chrc','type'=>'pitches','id'=> $id]);
        }

        // If it is a delete action we can stop right here
        if($request->query->get('action') == 'delete'){
            $commonGroundService->deleteResource($variables['resource']);
            return $this->redirect($this->generateUrl('app_chrc_pitches'));
        }

        $variables['title'] = $translator->trans('pitch');
        $variables['subtitle'] = $translator->trans('save or create a') . ' ' . $translator->trans('pitches');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'chrc','type'=>'slugs'],['organization.id'=>$id])["hydra:member"];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])

            $variables['resource'] = $commonGroundService->saveResource($resource,(['component'=>'chrc','type'=>'pitches']));

            /* @to this redirect is a hotfix */
            if(array_key_exists('id', $variables['resource'])){
                return $this->redirect($this->generateUrl('app_chrc_pitches', ["id" =>  $variables['resource']['id']]));
            }

        }

        return $variables;
    }

    /**
     * @Route("/challenges")
     * @Template
     */
    public function challengesAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('challenges');
        $variables['subtitle'] = $translator->trans('all') . ' ' . $translator->trans('challenges');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'chrc','type'=>'challenges'])["hydra:member"];
        return $variables;

    }

    /**
     * @Route("/challenges/{id}")
     * @Template
     */
    public function challengeAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
        $variables = [];

        // Lets see if we need to create
        if($id == 'new'){
            $variables['resource'] = ['@id' => null,'id'=>'new','name'=>'new'];
        }
        else{
            $variables['resource'] = $commonGroundService->getResource(['component'=>'chrc','type'=>'challenges','id'=> $id]);        }

        // If it is a delete action we can stop right here
        if($request->query->get('action') == 'delete'){
            $commonGroundService->deleteResource($variables['resource']);
            return $this->redirect($this->generateUrl('app_chrc_challenges'));
        }

        $variables['title'] = $translator->trans('challenge');
        $variables['subtitle'] = $translator->trans('save or create a') . ' ' . $translator->trans('challenges');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'chrc','type'=>'slugs'],['organization.id'=>$id])["hydra:member"];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])

            $variables['resource'] = $commonGroundService->saveResource($resource,(['component'=>'chrc','type'=>'challenges','id'=>$id]));        }

        return $variables;
    }

}






