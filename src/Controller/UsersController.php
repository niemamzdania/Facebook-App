<?php

namespace App\Controller;

use App\Entity\Avatars;
use App\Entity\Users;
use App\Form\AvatarFormType;
use App\Form\LoginFormType;
use App\Form\PasswordFormType;
use App\Form\EmailFormType;
use App\Form\AppIdFormType;
use App\Form\AppSecretFormType;
use App\Form\PageIdFormType;
use App\Form\AccessTokenFormType;

use Cloudinary\Search;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Session\Session;

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
    public function edit_user(Request $request, Users $user, Session $session)
    {
        if ($this->getUser() != $user) {
            return new Response('Forbidden access');
        }

        //Config of server
        \Cloudinary::config(array(
            "cloud_name" => "przemke",
            "api_key" => "884987643496832",
            "api_secret" => "9KWlEeWnpdqZyo2GlohdLAqibeU",
            "secure" => true
        ));

        $entityManager = $this->getDoctrine()->getManager();

        $avatar = $this->getDoctrine()->getRepository(Avatars::class)->findAvatarByUserId($user->getId());

        $loginForm = $this->createForm(LoginFormType::class, $user);
        $passwordForm = $this->createForm(PasswordFormType::class, $user);
        $emailForm = $this->createForm(EmailFormType::class, $user);
        $avatarForm = $this->createForm(AvatarFormType::class, $avatar);

        $loginForm->handleRequest($request);
        $passwordForm->handleRequest($request);
        $emailForm->handleRequest($request);
        $avatarForm->handleRequest($request);

        if (($loginForm->isSubmitted() && $loginForm->isValid()) ||
            ($emailForm->isSubmitted() && $emailForm->isValid())) {
            $entityManager->flush();

            $session->set('message', 'Dane zostały zmienione');
        } else if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {
            $data = $passwordForm->getData();

            $user->setPassword(
                $this->passwordEncoder->encodePassword($user, $data->getPassword())
            );

            $entityManager->flush();

            $session->set('message', 'Hasło zostało zmienione');
        } else if ($avatarForm->isSubmitted() && $avatarForm->isValid()) {
            $realPath = $request->files->get('avatar_form')['name']->getRealPath();

            if ($avatar) {
                $search = new Search();
                $searchData = "folder=avatars AND filename=" . $avatar->getName();
                $result = $search
                    ->expression($searchData)
                    ->max_results(1)
                    ->execute();

                if (isset($result['resources'][0]))
                    \Cloudinary\Uploader::destroy($result['resources'][0]['public_id']);

                $upload = \Cloudinary\Uploader::upload($realPath, ['folder' => 'avatars']);

                $fileName = substr($upload['public_id'], 8);

                $avatar->setName($fileName);

                $entityManager->flush();
                $session->set('message', 'Awatar został zmieniony');
            } else {
                $upload = \Cloudinary\Uploader::upload($realPath, ['folder' => 'avatars']);

                $fileName = substr($upload['public_id'], 8);

                $avatar = new Avatars();
                $avatar->setName($fileName);
                $avatar->setUser($user);

                $entityManager->persist($avatar);
                $entityManager->flush();

                $session->set('message', 'Awatar został dodany');
            }
        }

        if ($avatar) {
            $search = new Search();
            $searchData = "folder=avatars AND filename=" . $avatar->getName();
            $result = $search
                ->expression($searchData)
                ->max_results(1)
                ->execute();

            if (isset($result['resources'][0]))
                $userAvatar = $result['resources'][0]['url'];

            if ($session->get('message')) {
                $message = $session->get('message');
                $session->remove('message');

                if (isset($userAvatar)) {
                    return $this->render('users/edit_user.html.twig', [
                        'message' => $message,
                        'avatar' => $userAvatar,
                        'loginForm' => $loginForm->createView(),
                        'passwordForm' => $passwordForm->createView(),
                        'emailForm' => $emailForm->createView(),
                        'avatarForm' => $avatarForm->createView(),
                    ]);
                }
            }
            else{
                if (isset($userAvatar)) {
                    return $this->render('users/edit_user.html.twig', [
                        'avatar' => $userAvatar,
                        'loginForm' => $loginForm->createView(),
                        'passwordForm' => $passwordForm->createView(),
                        'emailForm' => $emailForm->createView(),
                        'avatarForm' => $avatarForm->createView(),
                    ]);
                }
            }
        }

        if ($session->get('message')) {
            $message = $session->get('message');
            $session->remove('message');

            return $this->render('users/edit_user.html.twig', [
                'message' => $message,
                'loginForm' => $loginForm->createView(),
                'passwordForm' => $passwordForm->createView(),
                'emailForm' => $emailForm->createView(),
                'avatarForm' => $avatarForm->createView(),
            ]);
        }
        else{
            return $this->render('users/edit_user.html.twig', [
                'loginForm' => $loginForm->createView(),
                'passwordForm' => $passwordForm->createView(),
                'emailForm' => $emailForm->createView(),
                'avatarForm' => $avatarForm->createView(),
            ]);
        }
    }

    /**
     * @Route("/reset", name="reset")
     */
    public function reset_password(\Swift_Mailer $mailer, Request $request)
    {
        if ($request->request->get('Email')) {
            $recipient = $request->request->get('Email');

            $user = $this->getDoctrine()->getRepository(Users::class)->findUserByEmail($recipient);

            if (!$user && $request->getLocale() == 'en')
                return $this->render('users/reset.html.twig', ['message' => 'This e-mail adress not exist. Type another one.']);
            elseif (!$user && $request->getLocale() == 'pl_PL')
                return $this->render('users/reset.html.twig', ['message' => 'Ten adres e-mail nie istnieje w bazie danych. Podaj właściwy.']);
            else {
                $password = md5(time());
                $password = substr($password, 0, 8);

                $user->setPassword(
                    $this->passwordEncoder->encodePassword($user, $password)
                );

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();

                if ($request->getLocale() == 'en') {
                    $body = "Your new password is: $password. You can login now with the new credentials.";
                    $message = (new \Swift_Message('Reset your password'))
                        ->setFrom('apkafacebook20@gmail.com')
                        ->setTo($recipient)
                        ->setBody(
                            $body, 'text/html'
                        );
                } else {
                    $body = "Twoje nowe hasło to: $password. Możesz teraz zalogować się z nowymi danymi.";
                    $message = (new \Swift_Message('Resetowanie hasła'))
                        ->setFrom('apkafacebook20@gmail.com')
                        ->setTo($recipient)
                        ->setBody(
                            $body, 'text/html'
                        );
                }
                $mailer->send($message);

                return $this->redirectToRoute('app_login');
            }
        }

        return $this->render('users/reset.html.twig');
    }

    /**
     * @Route("/avatars/delete/{id}", name="delete_user")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function delete_user(Users $user, Session $session)
    {
        if ($this->getUser()->getId() != $this->getUser()->getId() &&
            !$this->isGranted("ROLE_ADMIN")) {
            return new Response('Forbidden access');
        }

        $avatar = $this->getDoctrine()->getRepository(Avatars::class)->findAvatarByUserId($user->getId());

        if ($avatar) {
            //Config of server
            \Cloudinary::config(array(
                "cloud_name" => "przemke",
                "api_key" => "884987643496832",
                "api_secret" => "9KWlEeWnpdqZyo2GlohdLAqibeU",
                "secure" => true
            ));

            $search = new Search();
            $searchData = "folder=avatars AND filename=" . $avatar->getName();
            $result = $search
                ->expression($searchData)
                ->max_results(1)
                ->execute();

            if (isset($result['resources'][0]))
                \Cloudinary\Uploader::destroy($result['resources'][0]['public_id']);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($avatar);
            $entityManager->flush();
        }

        $session->set('message', 'Awatar został usunięty');

        return $this->redirectToRoute('edit_user', ['id' => $user->getId()]);
    }
}
