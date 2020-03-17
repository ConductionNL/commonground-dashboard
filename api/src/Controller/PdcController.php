<?php
// src/Controller/DefaultController.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use Knp\Bundle\MarkdownBundle\MarkdownParserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use App\Service\CommonGroundService;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use App\Security\User\CommongroundUser;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class PdcController
 * @package App\Controller
 * @Route("/producten")
 */
class PdcController extends AbstractController
{

    /**
     * @Route("/products")
     * @Template
     */
    public function productsAction(Request $request, CommonGroundService $commonGroundService)
    {
        $products = $commonGroundService->getResourceList('https://pdc.huwelijksplanner.online/products')["hydra:member"];

        $babsschets = "";

        $h1 = "Producten overzicht";

        return ["babsschets" => $babsschets, "h1" => $h1, "products"=>$products];
    }

    /**
     * @Route("/products/{id}")
     * @Template
     */
    public function productAction(Request $request, CommonGroundService $commonGroundService, $id)
    {
        $product = $commonGroundService->getResource('https://pdc.huwelijksplanner.online/products/'.$id);

        $groups = $commonGroundService->getResourceList('https://pdc.huwelijksplanner.online/groups')["hydra:member"];

        $babsschets = "";

        // Kijken of het formulier is getriggerd
        if ($request->isMethod('POST')) {

            // Passing the variables to the resource
            $variables = $request->request->all();

            foreach ($product['groups'] as $group){
                $variables['groups'][] = 'groups/'.$group['id'];
            }

            if($variables['addgroup'] != ""){
                $variables['groups'][] = $variables['addgroup'];
            }


            if($variables['removegroup'] != ""){

                foreach($variables['groups'] as $key=>$group){
                    if($group == $variables['removegroup']){
                        unset($variables['groups'][$key]);
                    }
                }
                /*
                */
            }

            $variables['@id'] = $product['@id'];

            /*@todo use try catch here */
            if($commonGroundService->updateResource($variables)){
                $this->addFlash('success', 'Product saved');
                $product = $commonGroundService->getResource($variables['@id']);
            }
            else{
                $this->addFlash('error', 'Product could not be saved');
            }
        }

        return ["babsschets" => $babsschets, "product"=>$product, "groups"=>$groups];
    }

    /**
     * @Route("/groups")
     * @Template
     */
    public function groupsAction(Request $request, CommonGroundService $commonGroundService)
    {
        $babsschets = "";

        $h1 = "Groepen overzicht";

        return ["babsschets" => $babsschets, "h1" => $h1];
    }

    /**
     * @Route("/offers")
     * @Template
     */
    public function offersAction(Request $request, CommonGroundService $commonGroundService)
    {
        $babsschets = "";

        $h1 = "Offers overzicht";

        return ["babsschets" => $babsschets, "h1" => $h1];
    }
}
