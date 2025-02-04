<?php

namespace App\Controller;

use App\Entity\Conversations;
use App\Entity\Users;
use App\Form\RegistrationFormType;
use App\Security\LoginFormAuthenticator;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="main_page")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function mainpage(Request $request)
    {
        return $this->render('mainpage/main.html.twig');
    }

    /**
     * @Route("/changeLanguage/{lang}", name="change_lang")
     */
    public function changeLanguage(Request $request, $lang)
    {
        $session = $request->getSession();
        $session->set('locale', $lang);

        return new Response("Ok");
    }
}
