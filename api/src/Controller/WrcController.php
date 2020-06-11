<?php

// src/Controller/DashboardController.php

namespace App\Controller;

use Conduction\CommonGroundBundle\Service\CommonGroundService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class DashboardController.
 *
 * @Route("/wrc")
 */
class WrcController extends AbstractController
{
    /**
     * @Route("/")
     * @Template
     */
    public function indexAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('webresources');
        $variables['subtitle'] = $translator->trans('the webresources consist of all items that are needed to render the application web interface');

        return $variables;
    }

    /**
     * @Route("/templates")
     * @Template
     */
    public function templatesAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('templates');
        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('templates');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'wrc', 'type'=>'templates'],['limit=90'])['hydra:member'];

        return $variables;
    }

    /**
     * @Route("/templates/{id}")
     * @Template
     */
    public function templateAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
        // If it is a delete action we can stop right here
        if ($request->query->get('action') == 'delete') {
            $commonGroundService->deleteResource(null,['component'=>'wrc', 'type'=>'templates', 'id'=> $id]);

            return $this->redirect($this->generateUrl('app_wrc_templates'));
        }

        $variables = [];

        // Lets see if we need to create
        if ($id == 'new') {
            $variables['resource'] = ['@id' => null, 'name'=>'new', 'id'=>'new'];
            $variables['slugs'] = [];
        } else {
            $variables['resource'] = $commonGroundService->getResource(['component'=>'wrc', 'type'=>'templates', 'id'=> $id]);
            $variables['slugs'] = $commonGroundService->getResourceList(['component'=>'wrc', 'type'=>'slugs'], ['template.id'=>$id])['hydra:member'];
        }

        $variables['title'] = $translator->trans('template');
        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('template');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'wrc', 'type'=>'organizations'])['hydra:member'];
        $variables['applications'] = $commonGroundService->getResourceList(['component'=>'wrc', 'type'=>'applications'])['hydra:member'];
        $variables['templateGroups'] = $commonGroundService->getResourceList(['component'=>'wrc', 'type'=>'template_groups'])['hydra:member'];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];



            // Lets see if we also need to add an slug
            if (array_key_exists('slugs', $resource)) {
                $slugs = $resource['slugs'];
                $slugs['template'] = $resource['@id'];

                // The resource action section
                if (array_key_exists('@id', $slugs) && array_key_exists('action', $slugs)) {
                    // The delete action
                    if ($slugs['action'] == 'delete') {
                        $commonGroundService->deleteResource($slugs);

                        return $this->redirect($this->generateUrl('app_wrc_template', ['id'=>$id]));
                    }
                }
                $slugs = $commonGroundService->saveResource($slugs, ['component'=>'wrc', 'type'=>'slugs']);
            }

            $variables['resource'] = $commonGroundService->saveResource($resource, ['component'=>'wrc', 'type'=>'templates']);
            /* @to this redirect is a hotfix */
            if (array_key_exists('id', $variables['resource'])) {
                return $this->redirect($this->generateUrl('app_wrc_templates', ['id' =>  $variables['resource']['id']]));
            }
        }

        return $variables;
    }

    /**
     * @Route("/template_groups")
     * @Template
     */
    public function templateGroupsAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('template groups');
        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('template groups');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'wrc', 'type'=>'template_groups'])['hydra:member'];

        return $variables;
    }

    /**
     * @Route("/template_groups/{id}")
     * @Template
     */
    public function templateGroupAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
        // If it is a delete action we can stop right here
        if ($request->query->get('action') == 'delete') {
            $commonGroundService->deleteResource(null,['component'=>'wrc', 'type'=>'template_groups', 'id'=> $id]);

            return $this->redirect($this->generateUrl('app_wrc_templategroups'));
        }

        $variables = [];

        // Lets see if we need to create
        if ($id == 'new') {
            $variables['resource'] = ['@id' => null, 'name'=>'new', 'id'=>'new'];
        } else {
            $variables['resource'] = $commonGroundService->getResource(['component'=>'wrc', 'type'=>'template_groups', 'id'=> $id]);
            $variables['templates'] = $commonGroundService->getResourceList(['component'=>'wrc', 'type'=>'templates'], ['templateGroups.id'=>$id])['hydra:member'];
        }

        $variables['title'] = $translator->trans('template groups');
        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('template group');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'wrc', 'type'=>'organizations'])['hydra:member'];
        $variables['applications'] = $commonGroundService->getResourceList(['component'=>'wrc', 'type'=>'applications'])['hydra:member'];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            if (array_key_exists('template', $resource)) {
                $template = $resource['template'];
                $template['templateGroups'] = $variables['resource']['@id'];
                $template = $commonGroundService->saveResource($template, ['component'=>'wrc', 'type'=>'templates']);

                // reload the slugs
                $variables['templates'] = $commonGroundService->getResourceList(['component'=>'wrc', 'type'=>'templates'], ['templateGroups.id'=>$id])['hydra:member'];
            }

            $variables['resource'] = $commonGroundService->saveResource($resource, ['component'=>'wrc', 'type'=>'template_groups']);
            if (array_key_exists('id', $variables['resource'])) {
                return $this->redirect($this->generateUrl('app_wrc_templategroups', ['id' =>  $variables['resource']['id']]));
            }

            // Lets see if we also need to add an slug
        }

        return $variables;
    }

    /**
     * @Route("/slugs")
     * @Template
     */
    public function slugsAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('slugs');
        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('slugs');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'wrc', 'type'=>'slugs'])['hydra:member'];

        return $variables;
    }

    /**
     * @Route("/slugs/{id}")
     * @Template
     */
    public function slugAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {

        // If it is a delete action we can stop right here
        if ($request->query->get('action') == 'delete') {
            $commonGroundService->deleteResource(null,['component'=>'wrc', 'type'=>'slugs', 'id'=>$id]);

            return $this->redirect($this->generateUrl('app_wrc_slugs'));
        }

        $variables = [];

        // Lets see if we need to create
        if ($id == 'new') {
            $variables['resource'] = ['@id' => null, 'name'=>'new', 'id'=>'new'];
        } else {
            $variables['resource'] = $commonGroundService->getResource(['component'=>'wrc', 'type'=>'slugs', 'id'=>$id]);
        }

        $variables['title'] = $translator->trans('slug');
        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('slug');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'wrc', 'type'=>'organizations'])['hydra:member'];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])

            $variables['resource'] = $commonGroundService->saveResource($resource, ['component'=>'wrc', 'type'=>'slugs']);
            if (array_key_exists('id', $variables['resource'])) {
                return $this->redirect($this->generateUrl('app_wrc_slugs', ['id' =>  $variables['resource']['id']]));
            }
        }

        return $variables;
    }

    /**
     * @Route("/organizations")
     * @Template
     */
    public function organizationsAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('organizations');
        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('organizations');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'wrc', 'type'=>'organizations'])['hydra:member'];

        return $variables;
    }

    /**
     * @Route("/organizations/{id}")
     * @Template
     */
    public function organizationAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
        // If it is a delete action we can stop right here
        if ($request->query->get('action') == 'delete') {
            $commonGroundService->deleteResource(null,['component'=>'wrc', 'type'=>'organizations', 'id'=>$id]);

            return $this->redirect($this->generateUrl('app_wrc_organizations'));
        }

        $variables = [];

        // Lets see if we need to create
        if ($id == 'new') {
            $variables['resource'] = ['@id' => null, 'name'=>'new', 'id'=>'new'];
        } else {
            $variables['resource'] = $commonGroundService->getResource(['component'=>'wrc', 'type'=>'organizations', 'id'=>$id]);
            $variables['styles'] = $commonGroundService->getResourceList(['component'=>'wrc', 'type'=>'styles'], ['organization.id'=>$id])['hydra:member'];
        }

        $variables['title'] = $translator->trans('organization');
        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('organization');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'wrc', 'type'=>'organizations'])['hydra:member'];
        $variables['employees'] = $commonGroundService->getResourceList(['component'=>'mrc', 'type'=>'employees'])['hydra:member'];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])

            $variables['resource'] = $commonGroundService->saveResource($resource, ['component'=>'wrc', 'type'=>'organizations']);
            if (array_key_exists('id', $variables['resource'])) {
                return $this->redirect($this->generateUrl('app_wrc_organizations', ['id' =>  $variables['resource']['id']]));
            }
        }

        return $variables;
    }

    /**
     * @Route("/images")
     * @Template
     */
    public function imagesAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('images');
        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('images');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'wrc', 'type'=>'images'])['hydra:member'];

        return $variables;
    }

    /**
     * @Route("/images/{id}")
     * @Template
     */
    public function imageAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
        // If it is a delete action we can stop right here
        if ($request->query->get('action') == 'delete') {
            $commonGroundService->deleteResource(null,['component'=>'wrc', 'type'=>'images', 'id'=>$id]);

            return $this->redirect($this->generateUrl('app_wrc_images'));
        }

        $variables = [];

        // Lets see if we need to create
        if ($id == 'new') {
            $variables['resource'] = ['@id' => null, 'name'=>'new', 'id'=>'new'];
        } else {
            $variables['resource'] = $commonGroundService->getResource(['component'=>'wrc', 'type'=>'images', 'id'=>$id]);
        }

        $variables['title'] = $translator->trans('image');
        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('image');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'wrc', 'type'=>'organizations'])['hydra:member'];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])

            $variables['resource'] = $commonGroundService->saveResource($resource, ['component'=>'wrc', 'type'=>'images']);
            if (array_key_exists('id', $variables['resource'])) {
                return $this->redirect($this->generateUrl('app_wrc_images', ['id' =>  $variables['resource']['id']]));
            }
        }

        return $variables;
    }

    /**
     * @Route("/styles")
     * @Template
     */
    public function stylesAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('styles');
        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('styles');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'wrc', 'type'=>'styles'])['hydra:member'];

        return $variables;
    }

    /**
     * @Route("/styles/{id}")
     * @Template
     */
    public function styleAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
        // If it is a delete action we can stop right here
        if ($request->query->get('action') == 'delete') {
            $commonGroundService->deleteResource(null, ['component'=>'wrc', 'type'=>'styles', 'id'=>$id]);

            return $this->redirect($this->generateUrl('app_wrc_styles'));
        }

        $variables = [];

        // Lets see if we need to create
        if ($id == 'new') {
            $variables['resource'] = ['@id' => null, 'name'=>'new', 'id'=>'new'];
        } else {
            $variables['resource'] = $commonGroundService->getResource(['component'=>'wrc', 'type'=>'styles', 'id'=>$id]);
        }

        $variables['title'] = $translator->trans('style');
        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('style');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'wrc', 'type'=>'organizations'])['hydra:member'];
        $variables['images'] = $commonGroundService->getResourceList(['component'=>'wrc', 'type'=>'images'])['hydra:member'];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])

            $variables['resource'] = $commonGroundService->saveResource($resource, ['component'=>'wrc', 'type'=>'styles']);
            if (array_key_exists('id', $variables['resource'])) {
                return $this->redirect($this->generateUrl('app_wrc_styles', ['id' =>  $variables['resource']['id']]));
            }
        }

        return $variables;
    }

    /**
     * @Route("/applications")
     * @Template
     */
    public function applicationsAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('applications');
        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('applications');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'wrc', 'type'=>'applications'])['hydra:member'];

        return $variables;
    }

    /**
     * @Route("/applications/{id}")
     * @Template
     */
    public function applicationAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
        // If it is a delete action we can stop right here
        if ($request->query->get('action') == 'delete') {
            $commonGroundService->deleteResource(null, ['component'=>'wrc', 'type'=>'applications', 'id'=>$id]);

            return $this->redirect($this->generateUrl('app_wrc_applications'));
        }
        $variables = [];

        // Lets see if we need to create
        if ($id == 'new') {
            $variables['resource'] = ['@id' => null, 'name'=>'new', 'id'=>'new'];
        } else {
            $variables['resource'] = $commonGroundService->getResource(['component'=>'wrc', 'type'=>'applications', 'id'=>$id]);
            $variables['configurations'] = $commonGroundService->getResourceList(['component'=>'wrc', 'type'=>'configurations'], ['application.id'=>$id])['hydra:member'];
            $variables['templates'] = $commonGroundService->getResourceList(['component'=>'wrc', 'type'=>'templates'], ['application.id'=>$id])['hydra:member'];
            $variables['slugs'] = $commonGroundService->getResourceList(['component'=>'wrc', 'type'=>'slugs'], ['application.id'=>$id])['hydra:member'];
            $variables['menus'] = $commonGroundService->getResourceList(['component'=>'wrc', 'type'=>'menus'], ['application.id'=>$id])['hydra:member'];
        }

        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'wrc', 'type'=>'organizations'])['hydra:member'];
        $variables['defaultConfigurations'] = $commonGroundService->getResourceList(['component'=>'wrc', 'type'=>'configurations'])['hydra:member'];
        $variables['styles'] = $commonGroundService->getResourceList(['component'=>'wrc', 'type'=>'styles'])['hydra:member'];

        // Als we een organisatie hebben kunnen we ook de style ophalen @to engels!
        if (array_key_exists('organization', $variables['resource'])) {
            $variables['styles'] = $commonGroundService->getResourceList(['component'=>'wrc', 'type'=>'styles'], ['organization.id'=>$variables['resource']['organization']])['hydra:member'];
        }

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // Lets see if we also need to add an configuration
            if (array_key_exists('configuration', $resource)) {
                $configuration = $resource['configuration'];
                $configuration['application'] = $resource['@id'];

                // The resource action section
                if (array_key_exists('@id', $configuration) && array_key_exists('action', $configuration)) {
                    // The delete action
                    if ($configuration['action'] == 'delete') {
                        $commonGroundService->deleteResource($configuration);

                        return $this->redirect($this->generateUrl('app_wrc_application', ['id'=>$id]));
                    }
                }

                $configuration = $commonGroundService->saveResource($configuration, ['component'=>'wrc', 'type'=>'configurations']);

                $variables['configurations'] = $commonGroundService->getResourceList(['component'=>'wrc', 'type'=>'configurations'], ['application.id'=>$id])['hydra:member'];
            }

            // Lets see if we also need to add an configuration
            if (array_key_exists('template', $resource)) {
                $template = $resource['template'];
                $template['application'] = $resource['@id'];

                // This needs to be al boolean for posting
                if (array_key_exists('slug', $template)) {
                    $template['slug'] = $template['slug'] === 'true' ? true : false;
                }

                // The resource action section
                if (array_key_exists('@id', $template) && array_key_exists('action', $template)) {
                    // The delete action
                    if ($template['action'] == 'delete') {
                        $commonGroundService->deleteResource($template);

                        return $this->redirect($this->generateUrl('app_wrc_application', ['id'=>$id]));
                    }
                }

                $template = $commonGroundService->saveResource($template, ['component'=>'wrc', 'type'=>'templates']);
                $variables['templates'] = $commonGroundService->getResourceList(['component'=>'wrc', 'type'=>'templates'], ['application.id'=>$id])['hydra:member'];
            }

            // Lets see if we also need to add an configuration
            if (array_key_exists('slug', $resource)) {
                $slug = $resource['slug'];
                $slug['application'] = $resource['@id'];

                // The resource action section
                if (array_key_exists('@id', $slug) && array_key_exists('action', $slug)) {
                    // The delete action
                    if ($slug['action'] == 'delete') {
                        $commonGroundService->deleteResource($slug);

                        return $this->redirect($this->generateUrl('app_wrc_application', ['id'=>$id]));
                    }
                }
                $slug = $commonGroundService->saveResource($slug, ['component'=>'wrc', 'type'=>'slugs']);
            }

            // Lets see if we also need to add an configuration
            if (array_key_exists('menu', $resource)) {
                $menu = $resource['menu'];
                $menu['application'] = $resource['@id'];

                // The resource action section
                if (array_key_exists('@id', $menu) && array_key_exists('action', $menu)) {
                    // The delete action
                    if ($menu['action'] == 'delete') {
                        $commonGroundService->deleteResource($menu);

                        return $this->redirect($this->generateUrl('app_wrc_application', ['id'=>$id]));
                    }
                }
                $menu = $commonGroundService->saveResource($menu, ['component'=>'wrc', 'type'=>'menus']);
                $variables['menus'] = $commonGroundService->getResourceList(['component'=>'wrc', 'type'=>'menus'], ['application.id'=>$id])['hydra:member'];
            }

            // If we do a reload here anyway? why dont we get the lates version of the resource
            $variables['resource'] = $commonGroundService->saveResource($resource, ['component'=>'wrc', 'type'=>'applications']);

            /* @to this redirect is a hotfix */
            if (array_key_exists('id', $variables['resource'])) {
                return $this->redirect($this->generateUrl('app_wrc_applications', ['id' =>  $variables['resource']['id']]));
            }
        }

        return $variables;
    }

    /**
     * @Route("/menus")
     * @Template
     */
    public function menusAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('menus');
        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('menus');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'wrc', 'type'=>'menus'])['hydra:member'];

        return $variables;
    }

    /**
     * @Route("/menus/{id}")
     * @Template
     */
    public function menuAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
        // If it is a delete action we can stop right here
        if ($request->query->get('action') == 'delete') {
            $commonGroundService->deleteResource(null,['component'=>'wrc', 'type'=>'menus', 'id'=>$id]);

            return $this->redirect($this->generateUrl('app_wrc_menus'));
        }

        $variables = [];

        // Lets see if we need to create
        if ($id == 'new') {
            $variables['resource'] = ['@id' => null, 'name'=>'new', 'id'=>'new'];
            $variables['menuItems'] = [];
        } else {
            $variables['resource'] = $commonGroundService->getResource(['component'=>'wrc', 'type'=>'menus', 'id'=>$id]);
            $variables['menuItems'] = $commonGroundService->getResourceList(['component'=>'wrc', 'type'=>'menu_items'], ['menu.id'=>$id])['hydra:member'];
        }

        $variables['title'] = $translator->trans('menu');
        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('menu');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'wrc', 'type'=>'organizations'])['hydra:member'];
        $variables['applications'] = $commonGroundService->getResourceList(['component'=>'wrc', 'type'=>'applications'])['hydra:member'];
        $variables['slugs'] = $commonGroundService->getResourceList(['component'=>'wrc', 'type'=>'slugs'])['hydra:member'];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])

            /* @todo dit vervangen door https://twig.symfony.com/doc/2.x/filters/u.html */
            // Hacky
            if (array_key_exists('menuitem', $resource)) {
                $resource['menuItem'] = $resource['menuitem'];
            }

            // Lets see if we also need to add an slug
            if (array_key_exists('menuItem', $resource)) {
                $menuItem = $resource['menuItem'];
                $menuItem['menu'] = $resource['@id'];

                if (array_key_exists('order', $menuItem)) {
                    $menuItem['order'] = intval($menuItem['order']);
                }

                // The resource action section
                if (array_key_exists('@id', $menuItem) && array_key_exists('action', $menuItem)) {
                    // The delete action
                    if ($menuItem['action'] == 'delete') {
                        $commonGroundService->deleteResource($menuItem);

                        return $this->redirect($this->generateUrl('app_wrc_menu', ['id'=>$id]));
                    }
                }

                $menuItem = $commonGroundService->saveResource($menuItem, ['component'=>'wrc', 'type'=>'menu_items']);
            }

            $variables['resource'] = $commonGroundService->saveResource($resource, ['component'=>'wrc', 'type'=>'menus']);

            /* @to this redirect is a hotfix */
            if (array_key_exists('id', $variables['resource'])) {
                return $this->redirect($this->generateUrl('app_wrc_menus', ['id' =>  $variables['resource']['id']]));
            }
        }

        return $variables;
    }

    /**
     * @Route("/configurations")
     * @Template
     */
    public function configurationsAction(CommonGroundService $commonGroundService, TranslatorInterface $translator)
    {
        $variables = [];
        $variables['title'] = $translator->trans('configurations');
        $variables['subtitle'] = $translator->trans('all').' '.$translator->trans('configurations');
        $variables['resources'] = $commonGroundService->getResourceList(['component'=>'wrc', 'type'=>'configurations'])['hydra:member'];

        return $variables;
    }

    /**
     * @Route("/configurations/{id}")
     * @Template
     */
    public function configurationAction(Request $request, CommonGroundService $commonGroundService, TranslatorInterface $translator, $id)
    {
        // If it is a delete action we can stop right here
        if ($request->query->get('action') == 'delete') {
            $commonGroundService->deleteResource(null,['component'=>'wrc', 'type'=>'configurations', 'id'=>$id]);

            return $this->redirect($this->generateUrl('app_wrc_configurations'));
        }

        $variables = [];

        // Lets see if we need to create
        if ($id == 'new') {
            $variables['resource'] = ['@id' => null, 'name'=>'new', 'id'=>'new'];
        } else {
            $variables['resource'] = $commonGroundService->getResource(['component'=>'wrc', 'type'=>'configurations', 'id'=>$id]);
        }

        $variables['title'] = $translator->trans('configuration');
        $variables['subtitle'] = $translator->trans('save or create a').' '.$translator->trans('configuration');
        $variables['organizations'] = $commonGroundService->getResourceList(['component'=>'wrc', 'type'=>'organizations'])['hydra:member'];
        $variables['applications'] = $commonGroundService->getResourceList(['component'=>'wrc', 'type'=>'applications'])['hydra:member'];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $resource = $request->request->all();
            $resource['@id'] = $variables['resource']['@id'];
            $resource['id'] = $variables['resource']['id'];

            // If there are any sub data sources the need to be removed below in order to save the resource
            // unset($resource['somedatasource'])

            $variables['resource'] = $commonGroundService->saveResource($resource, ['component'=>'wrc', 'type'=>'configurations']);

            /* @to this redirect is a hotfix */
            if (array_key_exists('id', $variables['resource'])) {
                return $this->redirect($this->generateUrl('app_wrc_configurations', ['id' =>  $variables['resource']['id']]));
            }
        }

        return $variables;
    }
}
