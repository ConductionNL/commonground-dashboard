<?php

// src/Controller/DefaultController.php

namespace App\Controller;

use App\Service\CommonGroundService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AgendaController
 * @package App\Controller
 * @Route("/agenda-component")
 */
class AgendaController extends AbstractController
{
    /**
     * @Route("/")
     * @Template
     */
    public function indexAction(Request $request, EntityManagerInterface $em)
    {
        return [];
    }

    /**
     * @Route("/alarms")
     * @Template
     */
    public function alarmsAction(Request $request, EntityManagerInterface $em,  CommonGroundService $commonGroundService)
    {
        $alarms = $commonGroundService->getResourceList('https://ac.zaakonline.nl/alarms');

        var_dump($alarms);
        return ["alarms"=>$alarms];
    }

    /**
     * @Route("/calendars")
     * @Template
     */
    public function calendarsAction(Request $request, EntityManagerInterface $em,  CommonGroundService $commonGroundService)
    {
        $calendars = $commonGroundService->getResourceList('https://ac.zaakonline.nl/calendars');

        return ["calendars"=>$calendars];
    }

    /**
     * @Route("/events")
     * @Template
     */
    public function eventsAction(Request $request, EntityManagerInterface $em,  CommonGroundService $commonGroundService)
    {
        $events = $commonGroundService->getResourceList('https://ac.zaakonline.nl/events');

        return ["events"=>$events];
    }

    /**
     * @Route("/freebusies")
     * @Template
     */
    public function freebusiesAction(Request $request, EntityManagerInterface $em,  CommonGroundService $commonGroundService)
    {
        $freebusies = $commonGroundService->getResourceList('https://ac.zaakonline.nl/freebusies');

        return ["freebusies"=>$freebusies];
    }

    /**
     * @Route("/journals")
     * @Template
     */
    public function journalsAction(Request $request, EntityManagerInterface $em,  CommonGroundService $commonGroundService)
    {
        $journals = $commonGroundService->getResourceList('https://ac.zaakonline.nl/journals');

        return ["journals"=>$journals];
    }

    /**
     * @Route("/resources")
     * @Template
     */
    public function resourcesAction(Request $request, EntityManagerInterface $em,  CommonGroundService $commonGroundService)
    {
        $resources = $commonGroundService->getResourceList('https://ac.zaakonline.nl/resources');

        return ["resources"=>$resources];
    }

    /**
     * @Route("/todos")
     * @Template
     */
    public function todosAction(Request $request, EntityManagerInterface $em,  CommonGroundService $commonGroundService)
    {
        $todos = $commonGroundService->getResourceList('https://ac.zaakonline.nl/todos');

        return ["todos"=>$todos];
    }

    /**
     * @Route("/config")
     * @Template
     */
    public function configAction(Request $request, EntityManagerInterface $em)
    {
        return [];
    }
}
