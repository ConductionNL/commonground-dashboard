<?php

// src/Controller/DefaultController.php

namespace App\Controller;

use Conduction\CommonGroundBundle\Service\CommonGroundService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class VtcController.
 *
 * @Route("/vtc")
 */
class VtcController extends AbstractController
{
    /**
     * @Route("/")
     * @Template
     */
    public function indexAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('product and service catalouge');
        $variables['subtitle'] = $translator->trans('the product and service catalouge holds al data concerning product, groups and offers');

        return $variables;
    }

    /**
     * @Route("/request_types")
     * @Template
     */
    public function requestTypesAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('request types');
        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('request types');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'vtc', 'type'=>'request_types'])['hydra:member'];

        return $variables;
    }

    /**
     * @Route("/request_types/{id}")
     * @Template
     */
    public function requestTypeAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {

        // If it is a delete action we can stop right here
        if ($request->query->get('action') == 'delete') {
            $commonGroundService->deleteResource(null, ['component'=>'vtc', 'type'=>'request_types', 'id'=>$id]);

            return $this->redirect($this->generateUrl('app_vtc_requesttypes'));
        }

        $variables = [];

        // Lets see if we need to create
        if ($id == 'new') {
            $variables['resource'] = ['@id' => null, 'name'=>'new', 'id'=>'new'];
        } else {
            $variables['resource'] = $commonGroundService->getResource(['component'=>'vtc', 'type'=>'request_types', 'id'=>$id]);
            $variables['changeLog'] = $commonGroundService->getResourceList($variables['resource']['@id'].'/change_log');
            $variables['auditTrail'] = $commonGroundService->getResourceList($variables['resource']['@id'].'/audit_trail');
        }

        $variables['title'] = $translator->trans('request type');
        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('request type');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'wrc', 'type'=>'organizations'])['hydra:member'];
        $variables['requestTypes'] = $commonGroundService->getResourceList(['component'=>'vtc', 'type'=>'request_types'])['hydra:member'];
        $variables['uri'] = $commonGroundService->getResourceList(['component' => 'wrc', 'type' => 'templates'])['hydra:member'];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();

            // Lets see if we also need t add an template
            if (array_key_exists('template', $resource)) {
                $template = $resource['template'];
                // The resource action section
                if (array_key_exists('@id', $template) && array_key_exists('action', $template)) {
                    // The delete action
                    if ($template['action'] == 'delete') {
                        $commonGroundService->deleteResource($template);

                        $variables['resource'] = $commonGroundService->saveResource($resource, ['component'=>'vtc', 'type'=>'request_types']);

                        return $this->redirect($this->generateUrl('app_vtc_requesttype', ['id' => $id]));
                    }
                }
                $template = $commonGroundService->saveResource($template, ['component' => 'vtc', 'type' => 'templates']);

            }

            if(isset($resource['unique'])){
                $resource['unique'] = $resource['unique'] === 'true' ? true : false;
            }

            if(isset($resource['parentRequired'])){
                $resource['parentRequired'] = $resource['parentRequired'] === 'true' ? true : false;

            }
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            unset($resource['properties']);

            //var_dump(json_encode($resource));
            //die;



            // Lets see if we also need to add an configuration
            if (array_key_exists('property', $resource)) {
                $property = $resource['property'];
                $property['requestType'] = $resource['@id'];

                // We need to force some properties to integer
                $property['multipleOf'] = intval($property['multipleOf']);
                $property['maximum'] = intval($property['maximum']);
                $property['minimum'] = intval($property['minimum']);
                $property['maxLength'] = intval($property['maxLength']);
                $property['minLength'] = intval($property['minLength']);
                $property['maxItems'] = intval($property['maxItems']);
                $property['minItems'] = intval($property['minItems']);
                $property['maxProperties'] = intval($property['maxProperties']);
                $property['minProperties'] = intval($property['minProperties']);

                // We to force some properties to boolean
                $property['exclusiveMaximum'] = $property['exclusiveMaximum'] === 'true' ? true : false;
                $property['exclusiveMinimum'] = $property['exclusiveMinimum'] === 'true' ? true : false;
                $property['uniqueItems'] = $property['uniqueItems'] === 'true' ? true : false;
                $property['nullable'] = $property['nullable'] === 'true' ? true : false;
                $property['required'] = $property['required'] === 'true' ? true : false;
                $property['readOnly'] = $property['readOnly'] === 'true' ? true : false;
                $property['writeOnly'] = $property['writeOnly'] === 'true' ? true : false;
                $property['deprecated'] = $property['readOnly'] === 'deprecated' ? true : false;
                $property['start'] = $property['start'] === 'true' ? true : false;

                // We to force some properties to array
                $property['enum'] = explode(',', $property['enum']);

                // We want to strip all empty values (this prevents the forced setting of exit dates)
                foreach ($property as $key => $value) {
                    if ($value == '') {
                        unset($property[$key]);
                    }
                }

                // The resource action section
                if (array_key_exists('@id', $property) && array_key_exists('action', $property)) {
                    // The delete action
                    if ($property['action'] == 'delete') {
                        $commonGroundService->deleteResource($property);

                        return $this->redirect($this->generateUrl('app_vtc_requesttype', ['id'=>$id]));
                    }
                }

                $property = $commonGroundService->saveResource($property, ['component'=>'vtc', 'type'=>'properties']);
            }

            $variables['resource'] = $commonGroundService->saveResource($resource, ['component'=>'vtc', 'type'=>'request_types']);
            /* @to this redirect is a hotfix */
            if (array_key_exists('id', $variables['resource'])) {
                return $this->redirect($this->generateUrl('app_vtc_requesttypes', ['id' =>  $variables['resource']['id']]));
            }
        }

        return $variables;
    }

    /**
     * @Route("/properties")
     * @Template
     */
    public function PropertiesAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('properties');
        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('properties');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'vtc', 'type'=>'properties'])['hydra:member'];

        return $variables;
    }

    /**
     * @Route("/properties/{id}")
     * @Template
     */
    public function PropertyAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
        $variables = [];

        // Lets see if we need to create
        if ($id == 'new') {
            $variables['resource'] = ['@id' => null, 'id'=>'new', 'name'=>'new'];
        } else {
            $variables['resource'] = $commonGroundService->getResource(['component'=>'vtc', 'type'=>'property', 'id'=> $id]);
        }

        // If it is a delete action we can stop right here
        if ($request->query->get('action') == 'delete') {
            $commonGroundService->deleteResource($variables['resource']);

            return $this->redirect($this->generateUrl('app_vtc_properties'));
        }

        $variables['title'] = $translator->trans('property');
        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('property');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'wrc', 'type'=>'organizations'])['hydra:member'];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])

            $variables['resource'] = $commonGroundService->saveResource($resource, (['component'=>'vtc', 'type'=>'property', 'id'=>$id]));
            /* @to this redirect is a hotfix */
            if (array_key_exists('id', $variables['resource'])) {
                return $this->redirect($this->generateUrl('app_vtc_properties', ['id' =>  $variables['resource']['id']]));
            }
        }

        return $variables;
    }
}
