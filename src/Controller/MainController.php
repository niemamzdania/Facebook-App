<?php

namespace App\Controller;

use App\Entity\Quests;
use App\Form\EditQuestFormType;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="test")
     *  @IsGranted("ROLE_ADMIN")
     */
    public function mainpage()
    {
        return $this->render('mainpage/main.html.twig');
    }


}
