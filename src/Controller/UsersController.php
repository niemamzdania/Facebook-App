<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\LoginFormType;
use App\Form\PasswordFormType;
use App\Form\EmailFormType;
use App\Form\AppIdFormType;
use App\Form\AppSecretFormType;
use App\Form\PageIdFormType;
use App\Form\AccessTokenFormType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class UsersController extends AbstractController
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @Route("/user/edit/{id}", name="edit_user")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function edit_user(Request $request, Users $user)
    {
        if ($this->getUser() != $user) {
            return new Response('Forbidden access');
        }

        $loginForm = $this->createForm(LoginFormType::class, $user);
        $passwordForm = $this->createForm(PasswordFormType::class, $user);
        $emailForm = $this->createForm(EmailFormType::class, $user);

        $loginForm->handleRequest($request);
        $passwordForm->handleRequest($request);
        $emailForm->handleRequest($request);

        if(($loginForm->isSubmitted() && $loginForm->isValid()) ||
            ($emailForm->isSubmitted() && $emailForm->isValid()))
        {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
        }

        else if($passwordForm->isSubmitted() && $passwordForm->isValid())
        {
            $data = $passwordForm->getData();

            $user->setPassword(
                $this->passwordEncoder->encodePassword($data, $data->getPassword())
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
        }

        return $this->render('users/edit_user.html.twig', [
            'loginForm' => $loginForm->createView(),
            'passwordForm' => $passwordForm->createView(),
            'emailForm' => $emailForm->createView()
        ]);
    }

    /**
     * @Route("/facebook/edit/{id}", name="edit_facebook")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function edit_facebook(Request $request, Users $user)
    {
        if ($this->getUser() != $user) {
            return new Response('Forbidden access');
        }

        $appIdForm = $this->createForm(AppIdFormType::class, $user);
        $appSecretForm = $this->createForm(AppSecretFormType::class, $user);
        $pageIdForm = $this->createForm(PageIdFormType::class, $user);
        $accessTokenForm = $this->createForm(AccessTokenFormType::class, $user);

        $appIdForm->handleRequest($request);
        $appSecretForm->handleRequest($request);
        $pageIdForm->handleRequest($request);
        $accessTokenForm->handleRequest($request);

        if(($appIdForm->isSubmitted() && $appIdForm->isValid()) ||
            ($appSecretForm->isSubmitted() && $appSecretForm->isValid()) ||
            ($pageIdForm->isSubmitted() && $pageIdForm->isValid()) ||
            ($accessTokenForm->isSubmitted() && $accessTokenForm->isValid()))
        {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
        }

        return $this->render('users/edit_facebook.html.twig', [
            'appIdForm' => $appIdForm->createView(),
            'appSecretForm' => $appSecretForm->createView(),
            'pageIdForm' => $pageIdForm->createView(),
            'accessTokenForm' => $accessTokenForm->createView()
        ]);
    }
    /** 
    * @Route("/reset/password", name="reset_password")
    */
   public function reset_password(\Swift_Mailer $mailer)
   {
    $name = "name";
    
    $message = (new \Swift_Message('Hello Email'))
    ->setFrom('send@example.com')
    ->setTo('milewskimateusz28@gmail.com')
    ->setBody(
        $this->renderView(
            'send.html.twig',
            ['name' => $name]
        ),
        'text/html'
    )

;

$mailer->send($message);
return new response("ok");
   }

}
