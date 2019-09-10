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

        if (($loginForm->isSubmitted() && $loginForm->isValid()) ||
            ($emailForm->isSubmitted() && $emailForm->isValid())) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
        } else if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {
            $data = $passwordForm->getData();

            $user->setPassword(
                $this->passwordEncoder->encodePassword($user, $data->getPassword())
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

        if (($appIdForm->isSubmitted() && $appIdForm->isValid()) ||
            ($appSecretForm->isSubmitted() && $appSecretForm->isValid()) ||
            ($pageIdForm->isSubmitted() && $pageIdForm->isValid()) ||
            ($accessTokenForm->isSubmitted() && $accessTokenForm->isValid())) {
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
     * @Route("/reset", name="reset")
     */
    public function reset_password(\Swift_Mailer $mailer, Request $request)
    {
        if ($request->request->get('Email')) {
            $recipient = $request->request->get('Email');

            $user = $this->getDoctrine()->getRepository(Users::class)->findUserByEmail($recipient);

            if (!$user)
                return $this->render('users/reset.html.twig', ['message' => 'This e-mail adress not exist. Type another one.']);
            else {
                $password = md5(time());
                $password = substr($password, 0, 8);

                $user->setPassword(
                    $this->passwordEncoder->encodePassword($user, $password)
                );

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();

                $message = (new \Swift_Message('Hello Email'))
                    ->setFrom('apkafacebook20@gmail.com')
                    ->setTo($recipient)
                    ->setBody(
                        "Your new password is: $password. You can login now with the new credentials.",
                        'text/html'
                    );

                $mailer->send($message);

                return $this->redirectToRoute('app_login');
            }
        }

        return $this->render('users/reset.html.twig');
    }
}
