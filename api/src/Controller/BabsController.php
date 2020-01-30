<?php

// src/Controller/DefaultController.php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use App\Service\CommonGroundService;

/**
 * Class BabsController
 * @package App\Controller
 * @Route("/babs")
 */
class BabsController extends AbstractController
{
    /**
     * @Route("/login")
     * @Template
     */
	public function indexAction(Request $request, CommonGroundService $commonGroundService)
    {
    	return [];
    }

    /**
     * @Route("/huwelijken")
     * @Template
     */
    public function huwelijkenAction(Request $request, CommonGroundService $commonGroundService)
    {
        $requests= $commonGroundService->getResourceList('https://vrc.zaakonline.nl/requests');
        $components= $commonGroundService->getComponentList();

        $babsschets = "";

        $h1 = "Uw overzicht van door u te sluiten huwelijken";

        return ["requests"=>$requests, "components"=>$components, "babsschets"=>$babsschets, "h1"=>$h1];
    }

    /**
     * @Route("/huwelijk")
     * @Template
     */
    public function huwelijkAction(Request $request, CommonGroundService $commonGroundService)
    {
        $requests= $commonGroundService->getResourceList('https://vrc.zaakonline.nl/requests');
        $components= $commonGroundService->getComponentList();

        $babsschets = "";

        $h1 = "Huwelijk van Martin Timmers en Anita Henrika de Kieft";

        return ["requests"=>$requests, "components"=>$components, "babsschets"=>$babsschets, "h1"=>$h1];
    }

    /**
     * @Route("/agenda")
     * @Template
     */
    public function agendaAction(Request $request, CommonGroundService $commonGroundService)
    {
        $requests= $commonGroundService->getResourceList('https://vrc.zaakonline.nl/requests');
        $components= $commonGroundService->getComponentList();

        $babsschets = "";

        $h1 = "Uw agenda waar u uw afpsraken in kan terug vinden";

        return ["requests"=>$requests, "components"=>$components, "babsschets"=>$babsschets, "h1"=>$h1];
    }
}
