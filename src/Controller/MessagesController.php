<?php

namespace App\Controller;

use App\Entity\Conversations;
use App\Entity\Messages;
use App\Entity\Users;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
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
        $data = $request->request->get('Content');

        if ($data) {
            $update = new Update((string)$topic->getId(), $data);
            $publisher($update);

            $message = new Messages();
            $message->setConvId($topic);
            $message->setSender($this->getUser());

            $conversation = $this->getDoctrine()->getRepository(Conversations::class)->findConvById($topic);

            if ($conversation->getUser1() == $this->getUser())
                $message->setRecipient($conversation->getUser2());
            else $message->setRecipient($conversation->getUser1());

            $message->setTime(new \DateTime());
            $message->setContent($request->request->get('Content'));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($message);
            $entityManager->flush();
        }

        return new Response("Ok");
    }

    /**
     * @Route("/chat", name="chat")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function chat(Session $session)
    {
        $user = $this->getUser();

        $conversations = $this->getDoctrine()->getRepository(Conversations::class)->findConvByUserId($user->getId());

        return $this->render('chat/conversations.html.twig', ['conversations' => $conversations]);
    }

    /**
     * @Route("/chat/{conversation}", name="one_chat")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function one_chat(Session $session, Conversations $conversation)
    {
        $user = $this->getUser();

        $id_conv = $conversation->getId();
        $conversations = $this->getDoctrine()->getRepository(Conversations::class)->findConvByUserId($user->getId());

        $messages = $this->getDoctrine()->getRepository(Messages::class)->findMessagesByConvId($conversation->getId());

        return $this->render('chat/conversations.html.twig', ['topic' => (string)$conversation->getId(), 'conversations' => $conversations,
            'messages' => $messages, 'id_conv' => $id_conv]);
    }
}