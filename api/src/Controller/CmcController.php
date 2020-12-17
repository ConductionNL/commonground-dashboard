<?php

// src/Controller/DefaultController.php

namespace App\Controller;

use Conduction\CommonGroundBundle\Service\CommonGroundService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class CmcController.
 *
 * @Route("/cmc")
 */
class CmcController extends AbstractController
{
    /**
     * @Route("/")
     * @Template
     */
    public function indexAction(TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('contact moments');
        $variables['subtitle'] = $translator->trans('the contact moments component holds contact moments');

        return $variables;
    }

    /**
     * @Route("/contact_moments")
     * @Template
     */
    public function ContactMomentsAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('contact_moments');
        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('contact_moments');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'cmc', 'type'=>'contact_moments'])['hydra:member'];

        return $variables;
    }

    /**
     * @Route("/contact_moments/{id}")
     * @Template
     */
    public function ContactMomentAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
        $variables = [];

        // Lets see if we need to create
        if ($id == 'new') {
            $variables['resource'] = ['@id' => null, 'id'=>'new', 'name'=>'new'];
        } else {
            $variables['resource'] = $commonGroundService->getResource(['component'=>'cmc', 'type'=>'contact_moment', 'id'=> $id]);
        }

        // If it is a delete action we can stop right here
        if ($request->query->get('action') == 'delete') {
            $commonGroundService->deleteResource($variables['resource']);

            return $this->redirect($this->generateUrl('app_cmc_contactmoments'));
        }

        $variables['title'] = $translator->trans('contact moment');
        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('contact moment');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'wrc', 'type'=>'organizations'])['hydra:member'];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])

            $variables['resource'] = $commonGroundService->saveResource($resource, (['component'=>'cmc', 'type'=>'contact_moment']));

            /* @to this redirect is a hotfix */
            if (array_key_exists('id', $variables['resource'])) {
                return $this->redirect($this->generateUrl('app_cmc_contactmoments', ['id' =>  $variables['resource']['id']]));
            }
        }

        return $variables;
    }
}
