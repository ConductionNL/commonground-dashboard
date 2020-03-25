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
        return $this->redirect($this->generateUrl('app_wrc_templates'));
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
        $variables = [];
        $variables['huwelijken'] = $commonGroundService->getResourceList('https://vrc.huwelijksplanner.online/requests', ['properties.type'=>'Huwelijk'])["hydra:member"];

        return $variables;
    }

    /**
     * @Route("/medewerker/huwelijken/{id}")
     * @Template
     */
    public function medewerkerHuwelijkViewAction(Request $request, CommonGroundService $commonGroundService, $id)
    {
        $variables = [];

        $variables['huwelijk'] = $commonGroundService->getResource('https://vrc.huwelijksplanner.online/requests/' . $id, [], true);
        $variables['plechtigheden'] = $commonGroundService->getResourceList('https://pdc.huwelijksplanner.online/products', ['groups.id' => '1cad775c-c2d0-48af-858f-a12029af24b3'])["hydra:member"];
        $variables['locaties'] = $commonGroundService->getResourceList('https://pdc.huwelijksplanner.online/products', ['groups.id' => '170788e7-b238-4c28-8efc-97bdada02c2e'])["hydra:member"];
        $variables['ambtenaren'] = $commonGroundService->getResourceList('https://pdc.huwelijksplanner.online/products', ['groups.id' => '7f4ff7ae-ed1b-45c9-9a73-3ed06a36b9cc'])["hydra:member"];
        $variables['extras'] = $commonGroundService->getResourceList('https://pdc.huwelijksplanner.online/products', ['groups.id' => 'f8298a12-91eb-46d0-b8a9-e7095f81be6f'])["hydra:member"];

        $variables['totalChecks'] = 8;
        $variables['confirmedChecks'] = 0;

        if (isset($variables['huwelijk']['properties']['partners'][0]) && !empty($variables['huwelijk']['properties']['partners'][0]) && isset($variables['huwelijk']['properties']['partners'][1]) && !empty($variables['huwelijk']['properties']['partners'][1])) {
            $variables['confirmedChecks']++;
        }
        if (isset($variables['huwelijk']['properties']['type']) && !empty($variables['huwelijk']['properties']['type'])) {
            $variables['confirmedChecks']++;
        }
        if (isset($variables['huwelijk']['properties']['plechtigheid']) && !empty($variables['huwelijk']['properties']['plechtigheid'])) {
            $variables['confirmedChecks']++;
        }
        if (isset($variables['huwelijk']['properties']['locatie']) && !empty($variables['huwelijk']['properties']['locatie'])) {
            $variables['confirmedChecks']++;
        }
        if (isset($variables['huwelijk']['properties']['datum']) && !empty($variables['huwelijk']['properties']['datum'])) {
            $variables['confirmedChecks']++;
        }
        if (isset($variables['huwelijk']['properties']['ambtenaar']) && !empty($variables['huwelijk']['properties']['ambtenaar'])) {
            $variables['confirmedChecks']++;
        }
        if (isset($variables['huwelijk']['properties']['getuigen']) && !empty($variables['huwelijk']['properties']['getuigen']) && count($variables['huwelijk']['properties']['getuigen']) > 1) {
            $variables['confirmedChecks']++;
        }
        if ($variables['huwelijk']['status'] == "completed") {
            $variables['confirmedChecks']++;
        }

        if ($request->isMethod('POST')) {

            $resource['properties'] = $request->request->all();
            $resource['properties'] = $variables['huwelijk']['properties'];
            foreach ($request->request->all() as $key=>$value){
                $resource['properties'][$key] = $value;
            }

            $resource['@id'] = $variables['huwelijk']['@id'];
            $resource['id'] = $variables['huwelijk']['id'];

            $variables['huwelijk'] = $commonGroundService->saveResource($resource, 'https://vrc.huwelijksplanner.online/requests/');
        }

//        var_dump($variables['huwelijk']);
//        die;

        return ["variables" => $variables];
    }

    /**
     * @Route("/medewerker/partnerschappen")
     * @Template
     */
    public function medewerkerPartnerschappenAction(Request $request, CommonGroundService $commonGroundService)
    {
        $variables = [];
        $variables['partnerschappen'] = $commonGroundService->getResourceList('https://vrc.huwelijksplanner.online/requests', ["properties.type"=>"Partnerschap"])["hydra:member"];

        return $variables;
    }


    /**
     * @Route("/medewerker/huwelijk")
     * @Template
     */
    public
    function medewerkerHuwelijkAction(Request $request, CommonGroundService $commonGroundService)
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
    public
    function tijdstipAction(Request $request, CommonGroundService $commonGroundService)
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
    public
    function babsAction(Request $request, CommonGroundService $commonGroundService)
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
    public
    function locatieagendaAction(Request $request, CommonGroundService $commonGroundService)
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
    public
    function babsagendaAction(Request $request, CommonGroundService $commonGroundService)
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
    public
    function gebruikersbeheerAction(Request $request, CommonGroundService $commonGroundService)
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
    public
    function configuratieAction(Request $request, CommonGroundService $commonGroundService)
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
    public
    function ceremoniebeheerAction(Request $request, CommonGroundService $commonGroundService)
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
    public
    function plechtigheidbeheerAction(Request $request, CommonGroundService $commonGroundService)
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
    public
    function babsbeheerAction(Request $request, CommonGroundService $commonGroundService)
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
    public
    function locatiebeheerAction(Request $request, CommonGroundService $commonGroundService)

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
    public
    function extrabeheerAction(Request $request, CommonGroundService $commonGroundService)
    {

        $babsschets = "";

        $h1 = "Beheer van de extra's";
        $functie = "Beheerder";

        return ["babsschets" => $babsschets, "h1" => $h1, "functie" => $functie];
    }
}



