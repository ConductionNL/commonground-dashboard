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
 * Class ContactController
 * @package App\Controller
 * @Route("/contact-catalogus")
 */
class ContactController extends AbstractController
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
     * @Route("/addresses")
     * @Template
     */
    public function addressesAction(Request $request, EntityManagerInterface $em, CommonGroundService $commonGroundService)
    {
        $addresses = $commonGroundService->getResourceList('https://cc.zaakonline.nl/addresses');

        return ["addresses"=>$addresses];
    }

    /**
     * @Route("/contact-lists")
     * @Template
     */
    public function contactListsAction(Request $request, EntityManagerInterface $em, CommonGroundService $commonGroundService)
    {
        $contactLists = $commonGroundService->getResourceList('https://cc.zaakonline.nl/contact_lists');

        return ["contactLists"=>$contactLists];
    }

    /**
     * @Route("/emails")
     * @Template
     */
    public function emailsAction(Request $request, EntityManagerInterface $em, CommonGroundService $commonGroundService)
    {
        $emails = $commonGroundService->getResourceList('https://cc.zaakonline.nl/emails');

        return ["emails"=>$emails];
    }

    /**
     * @Route("/organizations")
     * @Template
     */
    public function organizationsAction(Request $request, EntityManagerInterface $em, CommonGroundService $commonGroundService)
    {
        $organizations = $commonGroundService->getResourceList('https://cc.zaakonline.nl/organizations');

        return ["organizations"=>$organizations];
    }

    /**
     * @Route("/persons")
     * @Template
     */
    public function personsAction(Request $request, EntityManagerInterface $em, CommonGroundService $commonGroundService)
    {
        $persons = $commonGroundService->getResourceList('https://cc.zaakonline.nl/persons');

        return ["persons"=>$persons];
    }

    /**
     * @Route("/telephones")
     * @Template
     */
    public function telephonesAction(Request $request, EntityManagerInterface $em, CommonGroundService $commonGroundService)
    {
        $telephones = $commonGroundService->getResourceList('https://cc.zaakonline.nl/telephones');

        return ["telephones"=>$telephones];
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
