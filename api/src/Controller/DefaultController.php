<?php

// src/Controller/DashboardController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGenerator;

/**
 * Class DefaultController.
 */
class DefaultController extends AbstractController
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        if (!$this->getUser()) {
            return $this->redirect($this->generateUrl('app_user_login'));
        }

        return $this->redirect($this->generateUrl('app_ud_index', [], UrlGenerator::RELATIVE_PATH));
    }
}
