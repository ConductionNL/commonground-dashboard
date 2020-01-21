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
 * Class OrderController
 * @package App\Controller
 * @Route("/order-registratie-component")
 */
class OrderController extends AbstractController
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
     * @Route("/orders")
     * @Template
     */
    public function ordersAction(Request $request, EntityManagerInterface $em, CommonGroundService $commonGroundService)
    {
        $orders = $commonGroundService->getResourceList("https://orc.zaakonline.nl/orders");

        return ["orders"=>$orders];
    }

    /**
     * @Route("/order-items")
     * @Template
     */
    public function orderItemsAction(Request $request, EntityManagerInterface $em, CommonGroundService $commonGroundService)
    {
        $orderItems = $commonGroundService->getResourceList("https://orc.zaakonline.nl/order_items");

        return ["orderItems"=>$orderItems];
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
