<?php

namespace App\Controller;

use App\Entity\Quests;
use App\Form\EditQuestFormType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="main")
     */
    public function mainpage()
    {
        return $this->render('mainpage/main.html.twig');
    }


}
