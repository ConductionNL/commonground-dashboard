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
 * @Route("/mock")
 */
class BabsController extends AbstractController
{
    /**
     * @Route("/")
     * @Template
     */
    public function indexAction(Request $request, CommonGroundService $commonGroundService)
    {
        return $this->redirect("/mock/login");
    }

    /**
     * @Route("/login")
     * @Template
     */
    public function loginAction(Request $request, CommonGroundService $commonGroundService)
    {
        return [];
    }

    /**
     * @Route("/trouwambtenaar/huwelijken")
     * @Template
     */
    public function babsHuwelijkenAction(Request $request, CommonGroundService $commonGroundService)
    {

        $babsschets = "";

        $h1 = "Uw overzicht van door u te sluiten huwelijken";
        $functie = "Trouwambtenaar";

        $requests = $commonGroundService->getResourceList('https://vrc.huwelijksplanner.online/requests');

        return ["babsschets" => $babsschets, "h1" => $h1, "functie" => $functie, "requests" => $requests];
    }

    /**
     * @Route("/trouwambtenaar/huwelijk")
     * @Template
     */
    public function babsHuwelijkAction(Request $request, CommonGroundService $commonGroundService)
    {

        $babsschets = "";

        $h1 = "Martin Timmers en Anita Henrika de Kieft hebben u gekozen als trouwambtenaar";
        $functie = "Trouwambtenaar";

        return ["babsschets" => $babsschets, "h1" => $h1, "functie" => $functie];
    }

    /**
     * @Route("/trouwambtenaar/agenda")
     * @Template
     */
    public function agendaAction(Request $request, CommonGroundService $commonGroundService)
    {

        $babsschets = "";

        $h1 = "Uw agenda waar u uw afpsraken in kan terug vinden";
        $functie = "Trouwambtenaar";

        return ["babsschets" => $babsschets, "h1" => $h1, "functie" => $functie];
    }

    /**
     * @Route("/medewerker/huwelijken")
     * @Template
     */
    public function medewerkerHuwelijkenAction(Request $request, CommonGroundService $commonGroundService)
    {

        $babsschets = "";

        $h1 = "Uw overzicht van binnengekomen huwelijken";
        $functie = "Medewerker";

        $requests = $commonGroundService->getResourceList('https://vrc.huwelijksplanner.online/requests');

        return ["babsschets" => $babsschets, "h1" => $h1, "functie" => $functie, "requests" => $requests ];
    }

    /**
     * @Route("/medewerker/huwelijk")
     * @Template
     */
    public function medewerkerHuwelijkAction(Request $request, CommonGroundService $commonGroundService)
    {

        $babsschets = "";

        $h1 = "Het huwelijksoverzicht van Martin Timmers en Anita Henrika de Kieft";
        $functie = "Medewerker";

        return ["babsschets" => $babsschets, "h1" => $h1, "functie" => $functie];
    }

    /**
     * @Route("/melding/tijdstip-wijzigen")
     * @Template
     */
    public function tijdstipAction(Request $request, CommonGroundService $commonGroundService)
    {

        $babsschets = "";

        $h1 = "Het tijdstip van het huwelijk van Martin Timmers en Anita Henrika de Kieft";
        $functie = "Medewerker";

        return ["babsschets" => $babsschets, "h1" => $h1, "functie" => $functie];
    }

    /**
     * @Route("/melding/babs-wijzigen")
     * @Template
     */
    public function babsAction(Request $request, CommonGroundService $commonGroundService)
    {

        $babsschets = "";

        $h1 = "De toegewezen babs van het huwelijk van Martin Timmers en Anita Henrika de Kieft";
        $functie = "Medewerker";

        return ["babsschets" => $babsschets, "h1" => $h1, "functie" => $functie];
    }

    /**
     * @Route("/medewerker/locatieagenda")
     * @Template
     */
    public function locatieagendaAction(Request $request, CommonGroundService $commonGroundService)
    {

        $babsschets = "";

        $h1 = "Planning Locaties";
        $functie = "Medewerker";

        return ["babsschets" => $babsschets, "h1" => $h1, "functie" => $functie];
    }

    /**
     * @Route("/medewerker/babsagenda")
     * @Template
     */
    public function babsagendaAction(Request $request, CommonGroundService $commonGroundService)
    {

        $babsschets = "";

        $h1 = "Planning Trouwambtenaren";
        $functie = "Medewerker";

        return ["babsschets" => $babsschets, "h1" => $h1, "functie" => $functie];
    }

    /**
     * @Route("/beheerder/gebruikersbeheer")
     * @Template
     */
    public function gebruikersbeheerAction(Request $request, CommonGroundService $commonGroundService)
    {

        $babsschets = "";

        $h1 = "Uw overzicht van alle gebruikers en hun rollen";
        $functie = "Beheerder";

        return ["babsschets" => $babsschets, "h1" => $h1, "functie" => $functie];
    }

    /**
     * @Route("/beheerder/configuratie")
     * @Template
     */
    public function configuratieAction(Request $request, CommonGroundService $commonGroundService)
    {

        $babsschets = "";

        $h1 = "De configuratie van de huwelijksplanner";
        $functie = "Beheerder";

        return ["babsschets" => $babsschets, "h1" => $h1, "functie" => $functie];
    }

    /**
     * @Route("/beheerder/ceremonies")
     * @Template
     */
    public function ceremoniebeheerAction(Request $request, CommonGroundService $commonGroundService)
    {

        $babsschets = "";

        $h1 = "Beheer van de ceremonies";
        $functie = "Beheerder";

        return ["babsschets" => $babsschets, "h1" => $h1, "functie" => $functie];
    }

    /**
     * @Route("/beheerder/plechtigheden")
     * @Template
     */
    public function plechtigheidbeheerAction(Request $request, CommonGroundService $commonGroundService)
    {

        $babsschets = "";

        $h1 = "Beheer van de plechtigheden";
        $functie = "Beheerder";

        return ["babsschets" => $babsschets, "h1" => $h1, "functie" => $functie];
    }

    /**
     * @Route("/beheerder/trouwambtenaren")
     * @Template
     */
    public function babsbeheerAction(Request $request, CommonGroundService $commonGroundService)
    {

        $babsschets = "";

        $h1 = "Beheer van de trouwambtenaren";
        $functie = "Beheerder";

        return ["babsschets" => $babsschets, "h1" => $h1, "functie" => $functie];
    }


    /**
     * @Route("/beheerder/locatiebeheer")
     * @Template
     */
    public function locatiebeheerAction(Request $request, CommonGroundService $commonGroundService)

    {

        $babsschets = "";

        $h1 = "Hier ziet u alle beschikbare locaties";
        $functie = "Beheerder";

        return ["babsschets" => $babsschets, "h1" => $h1, "functie" => $functie];
    }

    /**
     * @Route("/beheerder/extras")
     * @Template
     */
    public function extrabeheerAction(Request $request, CommonGroundService $commonGroundService)
    {

        $babsschets = "";

        $h1 = "Beheer van de extra's";
        $functie = "Beheerder";

        return ["babsschets" => $babsschets, "h1" => $h1, "functie" => $functie];
    }
}



