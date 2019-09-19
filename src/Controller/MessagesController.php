<?php

namespace App\Controller;

use App\Entity\Conversations;
use App\Entity\Messages;
use App\Entity\Users;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mercure\Publisher;
use Symfony\Component\Mercure\Update;

class MessagesController extends AbstractController
{
    /**
     * @Route("/message/send/{topic}", name="send_message", methods={"POST"})
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function send_message(Conversations $topic, Publisher $publisher, Request $request, Session $session)
    {
        $update = new Update((string)$topic->getId(), $request->request->get('Content'));

        $publisher($update);

        $message = new Messages();
        $message->setConvId($topic);
        $message->setSender($this->getUser());

        $conversation = $this->getDoctrine()->getRepository(Conversations::class)->findConvById($topic);

        dd($conversation->getUser1());

        $user1 = $this->getDoctrine()->getRepository(Users::class)->findUserById($conversation->getUser1());
        $user2 = $this->getDoctrine()->getRepository(Users::class)->findUserById($conversation->getUser2());

        if($user1 == $this->getUser())
            $message->setRecipient($user2);
        else $message->setRecipient($user1);

        $message->setTime(new \DateTime());

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($message);
        $entityManager->flush();

        $five = 5;

        $conv = $this->getDoctrine()->getRepository(Conversations::class)->findConvById($five);
        dd($conv);

        return $this->redirectToRoute('one_chat', ['conversation' => $conversation]);
    }

    /**
     * @Route("/chat", name="chat")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function chat(Session $session)
    {
        $users = $this->getDoctrine()->getRepository(Users::class)->findAllUsers();

        $user = $this->getUser();

        $conversations = $this->getDoctrine()->getRepository(Conversations::class)->findConvByUserId($user->getId());

        return $this->render('chat/conversations.html.twig', ['topic' => '1e9', 'conversations' => $conversations]);
    }

    /**
     * @Route("/chat/{conversation}", name="one_chat")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function one_chat(Session $session, Conversations $conversation)
    {
        $user = $this->getUser();

        $conversations = $this->getDoctrine()->getRepository(Conversations::class)->findConvByUserId($user->getId());

        $messages = $this->getDoctrine()->getRepository(Messages::class)->findMessagesByConvId($conversation->getId());

        return $this->render('chat/conversations.html.twig', ['topic' => $conversation->getId(), 'conversations' => $conversations]);
    }
}