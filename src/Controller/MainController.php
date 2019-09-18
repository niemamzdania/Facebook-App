<?php

namespace App\Controller;

use App\Entity\Conversations;
use App\Entity\Users;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="main_page")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function mainpage(ObjectManager $manager)
    {
        return $this->render('mainpage/main.html.twig');
    }
}
