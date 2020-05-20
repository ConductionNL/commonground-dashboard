<?php
// src/Controller/LcController.php
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
 * Class LcController
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
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'lc','type'=>'accommodations'])["hydra:member"];
        return $variables;
    }

    /**
     * @Route("/accommodations/{id}")
     * @Template
     */
    public function accommodationAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {

    	$variables = [];

        // Lets see if we need to create
        if($id == 'new'){
            $variables['resource'] = ['@id' => null,'id'=>'new','name'=>'new'];
        }
        else{
            $variables['resource'] = $commonGroundService->getResource(['component'=>'lc','type'=>'accommodations','id'=> $id]);
        }

        // If it is a delete action we can stop right here
        if($request->query->get('action') == 'delete'){
            $commonGroundService->deleteResource($variables['resource']);
            return $this->redirect($this->generateUrl('app_lc_accommodations'));
        }

        $variables['title'] = $translator->trans('accommodation');
    	$variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('accommodation');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'wrc','type'=>'organizations'])["hydra:member"];
        $variables['places'] = $commonGroundService->getResourceList(['component'=>'lc','type'=>'places'])["hydra:member"];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();

            $resource['numberOfBathroomsTotal']= (int)$resource['numberOfBathroomsTotal'];
            $resource['floorLevel']= (int)$resource['floorLevel'];
            $resource['maximumAttendeeCapacity']= (int)$resource['maximumAttendeeCapacity'];
            $resource['resources']= (array)$resource['resources'];
            $resource['petsAllowed']= $resource['petsAllowed'] === 'true'? true: false;
            $resource['wheelchairAccessible']= $resource['wheelchairAccessible'] === 'true'? true: false;

            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])

            $variables['resource'] = $commonGroundService->saveResource($resource,(['component'=>'lc','type'=>'accommodations']));
        }


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
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'lc','type'=>'places'])["hydra:member"];

        return $variables;
    }

    /**
     * @Route("/places/{id}")
     * @Template
     */
    public function placeAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
        $variables = [];

        // Lets see if we need to create
        if($id == 'new'){
            $variables['resource'] = ['@id' => null,'id'=>'new','name'=>'new'];
        }
        else{
            $variables['resource'] = $commonGroundService->getResource(['component'=>'lc','type'=>'places','id'=> $id]);
        }

        // If it is a delete action we can stop right here
        if($request->query->get('action') == 'delete'){
            $commonGroundService->deleteResource($variables['resource']);
            return $this->redirect($this->generateUrl('app_lc_places'));
        }

        $variables['title'] = $translator->trans('place');
        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('place');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'wrc','type'=>'organizations'])["hydra:member"];
        $variables['labels'] = $commonGroundService->getResourceList(['component'=>'vrc','type'=>'labels'])["hydra:member"];



        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();

            $resource['publicAccess']= $resource['publicAccess'] === 'true'? true: false;
            $resource['smokingAllowed']= $resource['smokingAllowed'] === 'true'? true: false;

            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])

            $variables['resource'] = $commonGroundService->saveResource($resource,(['component'=>'lc','type'=>'places']));

        }

    	return $variables;
    }

}
