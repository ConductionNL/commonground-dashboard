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
 * Class RcController.
 *
 * @Route("/rc")
 */
class RcController extends AbstractController
{
    /**
     * @Route("/")
     * @Template
     */
    public function indexAction(TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('Review Component');
        $variables['subtitle'] = $translator->trans('The review component holds reviews.');

        return $variables;
    }

    /**
     * @Route("/aspects")
     * @Template
     */
    public function aspectsAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('aspects');
        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('aspects');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'rc', 'type'=>'aspects']);

        return $variables;
    }

    /**
     * @Route("/aspects/{id}")
     * @Template
     */
    public function aspectAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
        $variables = [];

        // Lets see if we need to create
        if ($id == 'new') {
            $variables['resource'] = ['@id' => null, 'id'=>'new', 'name'=>'new'];
        } else {
            $variables['resource'] = $commonGroundService->getResource(['component'=>'rc', 'type'=>'aspects', 'id'=> $id]);
        }

        // If it is a delete action we can stop right here
        if ($request->query->get('action') == 'delete') {
            $commonGroundService->deleteResource($variables['resource']);

            return $this->redirect($this->generateUrl('app_rc_aspects'));
        }

        $variables['title'] = $translator->trans('aspect');
        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('aspects');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'rc', 'type'=>'slugs'], ['organization.id'=>$id])['hydra:member'];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])

            $variables['resource'] = $commonGroundService->saveResource($resource, (['component'=>'rc', 'type'=>'aspects']));

            /* @to this redirect is a hotfix */
            if (array_key_exists('id', $variables['resource'])) {
                return $this->redirect($this->generateUrl('app_rc_aspects', ['id' =>  $variables['resource']['id']]));
            }
        }

        return $variables;
    }

    /**
     * @Route("/likes")
     * @Template
     */
    public function likesAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('likes');
        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('likes');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'rc', 'type'=>'likes'])['hydra:member'];

        return $variables;
    }

    /**
     * @Route("/likes/{id}")
     * @Template
     */
    public function likeAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
        $variables = [];

        // Lets see if we need to create
        if ($id == 'new') {
            $variables['resource'] = ['@id' => null, 'id'=>'new', 'name'=>'new'];
        } else {
            $variables['resource'] = $commonGroundService->getResource(['component'=>'rc', 'type'=>'likes', 'id'=> $id]);
        }

        // If it is a delete action we can stop right here
        if ($request->query->get('action') == 'delete') {
            $commonGroundService->deleteResource($variables['resource']);

            return $this->redirect($this->generateUrl('app_rc_likes'));
        }

        $variables['title'] = $translator->trans('like');
        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('likes');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'rc', 'type'=>'slugs'], ['organization.id'=>$id])['hydra:member'];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])

            $variables['resource'] = $commonGroundService->saveResource($resource, (['component'=>'rc', 'type'=>'likes']));

            /* @to this redirect is a hotfix */
            if (array_key_exists('id', $variables['resource'])) {
                return $this->redirect($this->generateUrl('app_rc_likes', ['id' =>  $variables['resource']['id']]));
            }
        }

        return $variables;
    }

    /**
     * @Route("/ratings")
     * @Template
     */
    public function ratingsAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('ratings');
        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('ratings');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'rc', 'type'=>'ratings'])['hydra:member'];

        return $variables;
    }

    /**
     * @Route("/ratings/{id}")
     * @Template
     */
    public function ratingAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
        $variables = [];

        // Lets see if we need to create
        if ($id == 'new') {
            $variables['resource'] = ['@id' => null, 'id'=>'new', 'name'=>'new'];
        } else {
            $variables['resource'] = $commonGroundService->getResource(['component'=>'rc', 'type'=>'ratings', 'id'=> $id]);
        }

        // If it is a delete action we can stop right here
        if ($request->query->get('action') == 'delete') {
            $commonGroundService->deleteResource($variables['resource']);

            return $this->redirect($this->generateUrl('app_rc_ratings'));
        }

        $variables['title'] = $translator->trans('rating');
        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('ratings');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'rc', 'type'=>'slugs'], ['organization.id'=>$id])['hydra:member'];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])

            $variables['resource'] = $commonGroundService->saveResource($resource, (['component'=>'rc', 'type'=>'ratings']));

            /* @to this redirect is a hotfix */
            if (array_key_exists('id', $variables['resource'])) {
                return $this->redirect($this->generateUrl('app_rc_ratings', ['id' =>  $variables['resource']['id']]));
            }
        }

        return $variables;
    }

    /**
     * @Route("/reviews")
     * @Template
     */
    public function reviewsAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('reviews');
        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('reviews');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'rc', 'type'=>'reviews'])['hydra:member'];

        return $variables;
    }

    /**
     * @Route("/reviews/{id}")
     * @Template
     */
    public function reviewAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
        $variables = [];

        // Lets see if we need to create
        if ($id == 'new') {
            $variables['resource'] = ['@id' => null, 'id'=>'new', 'name'=>'new'];
        } else {
            $variables['resource'] = $commonGroundService->getResource(['component'=>'rc', 'type'=>'reviews', 'id'=> $id]);
        }

        // If it is a delete action we can stop right here
        if ($request->query->get('action') == 'delete') {
            $commonGroundService->deleteResource($variables['resource']);

            return $this->redirect($this->generateUrl('app_rc_reviews'));
        }

        $variables['title'] = $translator->trans('review');
        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('reviews');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'rc', 'type'=>'slugs'], ['organization.id'=>$id])['hydra:member'];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])

            $variables['resource'] = $commonGroundService->saveResource($resource, (['component'=>'rc', 'type'=>'reviews']));

            /* @to this redirect is a hotfix */
            if (array_key_exists('id', $variables['resource'])) {
                return $this->redirect($this->generateUrl('app_rc_reviews', ['id' =>  $variables['resource']['id']]));
            }
        }

        return $variables;
    }
}
