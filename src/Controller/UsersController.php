<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\LoginFormType;
use App\Form\PasswordFormType;
use App\Form\EmailFormType;

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
}
