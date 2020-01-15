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
 * Class ProductController
 * @package App\Controller
 * @Route("/producten-en-diensten-catalogus")
 */
class ProductController extends AbstractController
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
     * @Route("/catalogues")
     * @Template
     */
    public function cataloguesAction(Request $request, EntityManagerInterface $em, CommonGroundService $commonGroundService)
    {
        $catalogues = $commonGroundService->getResourceList('https://pdc.zaakonline.nl/catalogues');

        return ["catalogues"=>$catalogues];
    }

    /**
     * @Route("/customer-types")
     * @Template
     */
    public function customerAction(Request $request, EntityManagerInterface $em, CommonGroundService $commonGroundService)
    {
        $customerTypes = $commonGroundService->getResourceList('https://pdc.zaakonline.nl/customerTypes');

        return ["customerTypes"=>$customerTypes];
    }

    /**
     * @Route("/groups")
     * @Template
     */
    public function groupsAction(Request $request, EntityManagerInterface $em, CommonGroundService $commonGroundService)
    {
        $groups = $commonGroundService->getResourceList('https://pdc.zaakonline.nl/groups');

        return ["groups"=>$groups];
    }

    /**
     * @Route("/offers")
     * @Template
     */
    public function offersAction(Request $request, EntityManagerInterface $em, CommonGroundService $commonGroundService)
    {
        $offers = $commonGroundService->getResourceList('https://pdc.zaakonline.nl/offers');

        return ["offers"=>$offers];
    }

    /**
     * @Route("/products")
     * @Template
     */
    public function productsAction(Request $request, EntityManagerInterface $em, CommonGroundService $commonGroundService)
    {
        $products = $commonGroundService->getResourceList('https://pdc.zaakonline.nl/products');

        return ["products"=>$products];
    }

    /**
     * @Route("/suppliers")
     * @Template
     */
    public function suppliersAction(Request $request, EntityManagerInterface $em, CommonGroundService $commonGroundService)
    {
        $suppliers = $commonGroundService->getResourceList('https://pdc.zaakonline.nl/suppliers');

        return ["suppliers"=>$suppliers];
    }

    /**
     * @Route("/taxes")
     * @Template
     */
    public function taxesAction(Request $request, EntityManagerInterface $em, CommonGroundService $commonGroundService)
    {
        $taxes = $commonGroundService->getResourceList('https://pdc.zaakonline.nl/taxes');

        return ["taxes"=>$taxes];
    }

    /**
     * @Route("/edit/{id}")
     * @Template
     */
    public function editAction(Request $request, EntityManagerInterface $em, CommonGroundService $commonGroundService, $id)
    {
        $products = $commonGroundService->getResourceList('https://pdc.zaakonline.nl/products'.$id);

        return ["products"=>$products,];
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
