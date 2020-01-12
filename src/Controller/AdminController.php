<?php

namespace App\Controller;

use App\Entity\Users;

use App\Form\AccessTokenFormType;
use App\Form\AppIdFormType;
use App\Form\AppSecretFormType;
use App\Form\PageIdFormType;
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

        $repeat = $this->getDoctrine()->getRepository(Users::class)->findUserByEmail($email);

        if($repeat) {
            $session->set('error', 'Ten adres e-mail istnieje w bazie danych, proszę podać inny.');

            return $this->redirectToRoute('show_users');
        }

        $user->setEmail($email);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();
        $session->set('message', 'E-mail został zmieniony');

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
        $session->set('message', 'Rola została zmieniona');

        return $this->redirectToRoute('show_users');
    }

    /**
     * @Route("/facebook/edit/{id}", name="edit_facebook")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function edit_facebook(Request $request, Users $user, Session $session)
    {
        if ($this->getUser() != $user && $request->getLocale() == 'en') {
            return new Response('Forbidden access');
        }
        if ($this->getUser() != $user && $request->getLocale() == 'pl_PL') {
            return new Response('Dostęp zabroniony');
        }
        if ($this->getUser() != $user)
            return new Response('Dostęp wzbroniony');

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

            $session->set('message', 'Dane zostały zmienione');
        }

        if($session->get('message')) {
            $message = $session->get('message');
            $session->remove('message');

            return $this->render('users/edit_facebook.html.twig', [
                'message' => $message,
                'appIdForm' => $appIdForm->createView(),
                'appSecretForm' => $appSecretForm->createView(),
                'pageIdForm' => $pageIdForm->createView(),
                'accessTokenForm' => $accessTokenForm->createView()
            ]);
        }
        else{
            return $this->render('users/edit_facebook.html.twig', [
                'appIdForm' => $appIdForm->createView(),
                'appSecretForm' => $appSecretForm->createView(),
                'pageIdForm' => $pageIdForm->createView(),
                'accessTokenForm' => $accessTokenForm->createView()
            ]);
        }
    }
    
    /**
     * @Route("/users", name="show_users")
     * @IsGranted("ROLE_ADMIN")
     */
    public function show_users(Session $session)
    {
        $users2 = $this->getDoctrine()->getRepository(Users::class)->findAllUsers();
        $users = [];
        for($i=0; $i<count($users2); $i++){
            if($users2[$i] == $this->getUser())
                continue;
            array_push($users, $users2[$i]);
        }

        if($session->get('message'))
        {
            $message = $session->get('message');
            $session->remove('message');

            return $this->render('users/show_users.html.twig', ['users' => $users, 'message' => $message]);
        }
        elseif ($session->get('error'))
        {
            $error = $session->get('error');
            $session->remove('error');

            return $this->render('users/show_users.html.twig', ['users' => $users, 'error' => $error]);
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

        $session->set('message', 'Użytkownik został usunięty');

        return $this->redirectToRoute('show_users');
    }
}
