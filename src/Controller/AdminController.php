<?php

namespace App\Controller;

use App\Entity\Users;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends AbstractController
{
    /**
     * @Route("/edit/email/{id}", name="edit_email")
     * @IsGranted("ROLE_ADMIN")
     */
    public function edit_email(Request $request, Users $user, Session $session)
    {
        $email = $request->request->get('email');
        $user->setEmail($email);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();
        $session->set('message', 'Email has been changed');

        return $this->redirectToRoute('show_users');
    }

     /**
     * @Route("/edit/role/{id}", name="edit_role")
     * @IsGranted("ROLE_ADMIN")
     */
    public function edit_role(Request $request, Users $user, Session $session)
    {
        $role = $request->request->get('roles');
        $user->setRoles([$role]);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();
        $session->set('message', 'Role has been changed');

        return $this->redirectToRoute('show_users');
    }
    
    /**
     * @Route("/users", name="show_users")
     * @IsGranted("ROLE_ADMIN")
     */
    public function show_users(Session $session)
    {
        $users = $this->getDoctrine()->getRepository(Users::class)->findAllUsers();

        if($session->get('message'))
        {
            $message = $session->get('message');
            $session->remove('message');

            return $this->render('users/show_users.html.twig', ['users' => $users, 'message' => $message]);
        }
        else
        {
            return $this->render('users/show_users.html.twig', ['users' => $users]);   
        }
        
    }

    /**
     * @Route("/user/delete/{id}", name="delete_user")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function delete_user(Users $user, Session $session)
    {
        if ($this->getUser()->getId() != $user->getId() &&
        !$this->isGranted("ROLE_ADMIN")) {
            return new Response('Forbidden access');
        }

        //dd('It is only test. Do you really want to delete user?');

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($user);
        $entityManager->flush();

        $session->set('message', 'User has been deleted');

        return $this->redirectToRoute('show_users');
    }
}
