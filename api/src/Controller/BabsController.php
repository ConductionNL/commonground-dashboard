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

        $babsschets = "";

        $h1 = "Uw overzicht van door u te sluiten huwelijken";
        $functie = "Trouwambtenaar";

        $requests = $commonGroundService->getResourceList('https://vrc.zaakonline.nl/requests');

        return [ "babsschets"=>$babsschets, "h1"=>$h1, "functie"=>$functie, "requests" => $requests];
    }

    /**
     * @Route("/huwelijk")
     * @Template
     */
    public function huwelijkAction(Request $request, CommonGroundService $commonGroundService)
    {

        $babsschets = "";

        $h1 = "Martin Timmers en Anita Henrika de Kieft hebben u gekozen als trouwambtenaar";
        $functie = "Trouwambtenaar";

        return ["babsschets"=>$babsschets, "h1"=>$h1, "functie"=>$functie];
    }

    /**
     * @Route("/agenda")
     * @Template
     */
    public function agendaAction(Request $request, CommonGroundService $commonGroundService)
    {

        $babsschets = "";

        $h1 = "Uw agenda waar u uw afpsraken in kan terug vinden";
        $functie = "Trouwambtenaar";

        return ["babsschets"=>$babsschets, "h1"=>$h1, "functie"=>$functie];
    }

    /**
     * @Route("/meldingen")
     * @Template
     */
    public function meldingenAction(Request $request, CommonGroundService $commonGroundService)
    {

        $babsschets = "";

        $h1 = "Uw overzicht van binnengekomen verzoeken";
        $functie = "Medewerker";

        $requests = $commonGroundService->getResourceList('https://vrc.zaakonline.nl/requests');

        return [ "babsschets"=>$babsschets, "h1"=>$h1, "functie"=>$functie, "requests"=>$requests];
    }

    /**
     * @Route("/melding")
     * @Template
     */
    public function meldingAction(Request $request, CommonGroundService $commonGroundService)
    {

        $babsschets = "";

        $h1 = "Het huwelijksoverzicht van Martin Timmers en Anita Henrika de Kieft";
        $functie = "Medewerker";

        return [ "babsschets"=>$babsschets, "h1"=>$h1, "functie"=>$functie];
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

        return [ "babsschets"=>$babsschets, "h1"=>$h1, "functie"=>$functie];
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

        return [ "babsschets"=>$babsschets, "h1"=>$h1, "functie"=>$functie];
    }

    /**
     * @Route("/locaties")
     * @Template
     */
    public function locatiesAction(Request $request, CommonGroundService $commonGroundService)
    {

        $babsschets = "";

        $h1 = "Planning Locaties";
        $functie = "Medewerker";

        return [ "babsschets"=>$babsschets, "h1"=>$h1, "functie"=>$functie];
    }

    /**
     * @Route("/trouwambtenaren")
     * @Template
     */
    public function ambtenarenAction(Request $request, CommonGroundService $commonGroundService)
    {

        $babsschets = "";

        $h1 = "Planning Trouwambtenaren";
        $functie = "Medewerker";

        return [ "babsschets"=>$babsschets, "h1"=>$h1, "functie"=>$functie];
    }

    /**
     * @Route("/gebruikersbeheer")
     * @Template
     */
    public function gebruikersbeheerAction(Request $request, CommonGroundService $commonGroundService)
    {

        $babsschets = "";

        $h1 = "Uw overzicht van alle gebruikers en hun rollen";
        $functie = "Beheerder";

        return [ "babsschets"=>$babsschets, "h1"=>$h1, "functie"=>$functie];
    }

    /**
     * @Route("/configuratie")
     * @Template
     */
    public function configuratieAction(Request $request, CommonGroundService $commonGroundService)
    {

        $babsschets = "";

        $h1 = "De configuratie van de huwelijksplanner";
        $functie = "Beheerder";

        return [ "babsschets"=>$babsschets, "h1"=>$h1, "functie"=>$functie];
    }
}
