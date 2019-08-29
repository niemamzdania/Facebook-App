<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="main_page")
     * @IsGranted("IS_AUTHENTICATED_ANONYMOUSLY")
     */
    public function mainpage()
    {
        return $this->render('mainpage/main.html.twig');
    }
}
