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
 * Class BrpController
 * @package App\Controller
 * @Route("/brp-service")
 */
class BrpController extends AbstractController
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
     * @Route("/aangaan-huwelijk-partnerschappen")
     * @Template
     */
    public function aangaanHuwelijkPartnerschappenAction(Request $request, EntityManagerInterface $em, CommonGroundService $commonGroundService)
    {
        $aangaanHuwelijkPartnerschappen = $commonGroundService->getResourceList('https://brp.zaakonline.nl/aangaan_huwelijk_partnerschappen');

        return ["aangaanHuwelijkPartnerschappen"=>$aangaanHuwelijkPartnerschappen];
    }

    /**
     * @Route("/geboortes")
     * @Template
     */
    public function geboortesAction(Request $request, EntityManagerInterface $em, CommonGroundService $commonGroundService)
    {
        $geboortes = $commonGroundService->getResourceList('https://brp.zaakonline.nl/geboortes');

        return ["geboortes"=>$geboortes];
    }

    /**
     * @Route("/gezags-verhoudingen")
     * @Template
     */
    public function gezagsVerhoudingenAction(Request $request, EntityManagerInterface $em, CommonGroundService $commonGroundService)
    {
        $gezagsVerhoudingen = $commonGroundService->getResourceList('https://brp.zaakonline.nl/gezags_verhoudingen');

        return ["gezagsVerhoudingen"=>$gezagsVerhoudingen];
    }

    /**
     * @Route("/ingeschreven-personen")
     * @Template
     */
    public function ingeschrevenPersonenAction(Request $request, EntityManagerInterface $em, CommonGroundService $commonGroundService)
    {
        $ingeschrevenPersonen = $commonGroundService->getResourceList('https://brp.zaakonline.nl/ingeschreven_personen');

        return ["ingeschrevenPersonen"=>$ingeschrevenPersonen];
    }

    /**
     * @Route("/kinderen")
     * @Template
     */
    public function kinderenAction(Request $request, EntityManagerInterface $em, CommonGroundService $commonGroundService)
    {
        $kinderen = $commonGroundService->getResourceList('https://brp.zaakonline.nl/kinderen');

        return ["kinderen"=>$kinderen];
    }

    /**
     * @Route("/namen-personen")
     * @Template
     */
    public function namenPersonenAction(Request $request, EntityManagerInterface $em, CommonGroundService $commonGroundService)
    {
        $namenPersonen = $commonGroundService->getResourceList('https://brp.zaakonline.nl/namen_personen');

        return ["namenPersonen"=>$namenPersonen];
    }

    /**
     * @Route("/nationaliteit")
     * @Template
     */
    public function nationaliteitAction(Request $request, EntityManagerInterface $em, CommonGroundService $commonGroundService)
    {
        $nationaliteiten = $commonGroundService->getResourceList('https://brp.zaakonline.nl/nationaliteiten');

        return ["nationaliteiten"=>$nationaliteiten];
    }

    /**
     * @Route("/opschortings-bijhoudingen")
     * @Template
     */
    public function opschortingsBijhoudingenAction(Request $request, EntityManagerInterface $em, CommonGroundService $commonGroundService)
    {
        $opschortingsBijhoudingen = $commonGroundService->getResourceList('https://brp.zaakonline.nl/opschortings_bijhoudingen');

        return ["opschortingsBijhoudingen"=>$opschortingsBijhoudingen];
    }

    /**
     * @Route("/ouders")
     * @Template
     */
    public function oudersAction(Request $request, EntityManagerInterface $em, CommonGroundService $commonGroundService)
    {
        $ouders = $commonGroundService->getResourceList('https://brp.zaakonline.nl/ouders');

        return ["ouders"=>$ouders];
    }

    /**
     * @Route("/overlijdens")
     * @Template
     */
    public function overlijdensAction(Request $request, EntityManagerInterface $em, CommonGroundService $commonGroundService)
    {
        $overlijdens = $commonGroundService->getResourceList('https://brp.zaakonline.nl/overlijdens');

        return ["overlijdens"=>$overlijdens];
    }

    /**
     * @Route("/partners")
     * @Template
     */
    public function partnersAction(Request $request, EntityManagerInterface $em, CommonGroundService $commonGroundService)
    {
        $partners = $commonGroundService->getResourceList('https://brp.zaakonline.nl/partners');

        return ["partners"=>$partners];
    }

    /**
     * @Route("/reisdocumenten")
     * @Template
     */
    public function reisdocumentenAction(Request $request, EntityManagerInterface $em, CommonGroundService $commonGroundService)
    {
        $reisdocumenten = $commonGroundService->getResourceList('https://brp.zaakonline.nl/reisdocumenten');

        return ["reisdocumenten"=>$reisdocumenten];
    }

    /**
     * @Route("/verblijven-buitenland")
     * @Template
     */
    public function verblijvenBuitenlandAction(Request $request, EntityManagerInterface $em, CommonGroundService $commonGroundService)
    {
        $verblijvenBuitenland = $commonGroundService->getResourceList('https://brp.zaakonline.nl/verblijvenBuitenland');

        return ["verblijvenBuitenland"=>$verblijvenBuitenland];
    }

    /**
     * @Route("/verblijf-plaatsen")
     * @Template
     */
    public function verblijfPlaatsenAction(Request $request, EntityManagerInterface $em, CommonGroundService $commonGroundService)
    {
        $verblijfPlaatsen = $commonGroundService->getResourceList('https://brp.zaakonline.nl/verblijfPlaatsen');

        return ["verblijfPlaatsen"=>$verblijfPlaatsen];
    }

    /**
     * @Route("/verblijfstitels")
     * @Template
     */
    public function verblijfstitelsAction(Request $request, EntityManagerInterface $em, CommonGroundService $commonGroundService)
    {
        $verblijfstitels = $commonGroundService->getResourceList('https://brp.zaakonline.nl/verblijfstitels');

        return ["verblijfstitels"=>$verblijfstitels];
    }

    /**
     * @Route("/waardetabellen")
     * @Template
     */
    public function waardetabellenAction(Request $request, EntityManagerInterface $em, CommonGroundService $commonGroundService)
    {
        $waardetabellen = $commonGroundService->getResourceList('https://brp.zaakonline.nl/waardetabellen');

        return ["waardetabellen"=>$waardetabellen];
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
