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
 * Class MemoController
 * @package App\Controller
 * @Route("/memo")
 */
class MemoController extends AbstractController
{

	/**
	 * @Route("/")
	 * @Template
	 */
	public function indexAction(TranslatorInterface $translator)
	{
		$variables = [];
		$variables['title'] = $translator->trans('memos');
		$variables['subtitle'] = $translator->trans('the memos component holds memos.');

		return $variables;
	}

    /**
     * @Route("/memos")
     * @Template
     */
	public function memosAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
    	$variables = [];
    	$variables['title'] = $translator->trans('memos');
    	$variables['subtitle'] = $translator->trans('all').' '.$translator->trans('memos');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'memo','type'=>'memos'])["hydra:member"];
        return $variables;
    }

    /**
     * @Route("/memos/{id}")
     * @Template
     */
    public function memoAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {

    	$variables = [];

        // Lets see if we need to create
        if($id == 'new'){
            $variables['resource'] = ['@id' => null,'id'=>'new','name'=>'new'];
        }
        else{
            $variables['resource'] = $commonGroundService->getResource(['component'=>'memo','type'=>'memos','id'=> $id]);        }

        // If it is a delete action we can stop right here
        if($request->query->get('action') == 'delete'){
            $commonGroundService->deleteResource($variables['resource']);
            return $this->redirect($this->generateUrl('app_memo_memos'));
        }

        $variables['title'] = $translator->trans('memo');
    	$variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('memo');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'memo','type'=>'organizations'])["hydra:member"];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])


            $variables['resource'] = $commonGroundService->saveResource($resource,(['component'=>'memo','type'=>'memos','id'=>$id]));
        }


        return $variables;
    }


}
