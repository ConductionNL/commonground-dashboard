<?php

// src/Controller/DashboardController.php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use App\Service\CommonGroundService;

/**
 * Class DashboardController
 * @package App\Controller
 * @Route("/configuratie")
 */
class WrcController extends AbstractController
{
    /**
     * @Route("/")
     * @Template
     */
    public function indexAction(Request $request, CommonGroundService $commonGroundService)
    {
        return [];
    }

    /**
     * @Route("/templates")
     * @Template
     */
    public function templatesAction(Request $request, CommonGroundService $commonGroundService)
    {
        $templates = $commonGroundService->getResourceList('https://wrc.huwelijksplanner.online/templates')["hydra:member"];

        $babsschets = "";

        return ["babsschets"=>$babsschets, "templates"=>$templates];
    }

    /**
     * @Route("/templates/{id}")
     * @Template
     */
    public function templateAction(Request $request, CommonGroundService $commonGroundService, $id)
    {
        $template = $commonGroundService->getResource('https://wrc.huwelijksplanner.online/templates/'.$id);

        $babsschets = "";

        // Kijken of het formulier is getriggerd
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $variables = $request->request->all();

            $variables['@id'] = $template['@id'];

            /*@todo use try catch here */
            if($commonGroundService->updateResource($variables)){
                $this->addFlash('success', 'Template saved');
                $template = $commonGroundService->getResource($variables['@id']);
            }
            else{
                $this->addFlash('error', 'Template could not be saved');
            }
        }

        return ["babsschets"=>$babsschets,"template"=>$template];
    }

    /**
     * @Route("/vormgeving")
     * @Template
     */
    public function vormgevingAction(Request $request, CommonGroundService $commonGroundService)
    {
        $templates = $commonGroundService->getResourceList('https://wrc.huwelijksplanner.online/templates')["hydra:member"];

        $babsschets = "";

        return ["babsschets"=>$babsschets, "templates"=>$templates];
    }

    /**
     * @Route("/applicatie")
     * @Template
     */
    public function applicatieAction(Request $request, CommonGroundService $commonGroundService)
    {
        $templates = $commonGroundService->getResourceList('https://wrc.huwelijksplanner.online/templates')["hydra:member"];

        $babsschets = "";

        return ["babsschets"=>$babsschets, "templates"=>$templates];
    }
}
