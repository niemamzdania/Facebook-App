<?php

namespace App\Controller;

use App\Entity\Conversations;
use App\Entity\Users;
use App\Form\RegistrationFormType;
use App\Security\LoginFormAuthenticator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\HttpFoundation\Session\Session;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     * @IsGranted("IS_AUTHENTICATED_ANONYMOUSLY")
     */
    public function register(Session $session, Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $authenticator): Response
    {
        $user = new Users();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $role = [$request->request->get('roles')];
            $user->setRoles($role);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);

            //Create conversations for new user
            $users = $this->getDoctrine()->getRepository(Users::class)->findAllUsers();

            for($i = 0; $i < count($users); $i++)
            {
                if($users[$i] == $user)
                    continue;
                $conversation = new Conversations();
                $conversation->setUser1($user);
                $conversation->setUser2($users[$i]);
                $entityManager->persist($conversation);
            }

            $entityManager->flush();
            
            $session->set('message', 'User has been created');    

            return $this->redirectToRoute('show_users');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
