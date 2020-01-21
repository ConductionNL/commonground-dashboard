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
 * Class MedewerkerController
 * @package App\Controller
 * @Route("/medewerker-catalogus")
 */
class MedewerkerController extends AbstractController
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
     * @Route("/employees")
     * @Template
     */
    public function employeesAction(Request $request, EntityManagerInterface $em, CommonGroundService $commonGroundService)
    {
        $employees = $commonGroundService->getResourceList('https://mrc.zaakonline.nl/employees');

        return ["employees"=>$employees];
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
