<?php

namespace App\Controller;

use App\Entity\Users;
use App\Entity\Conversations;
use App\Entity\Messages;

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
    public function send_message($topic, Publisher $publisher, Request $request, Session $session)
    {
        $update = new Update($topic, $request->request->get('Content'));

        $publisher($update);

        $message = new Messages();
        $message->setConvId($topic);
        $message->setSender($this->getUser());

        dd('asdas');
        $conversation = $this->getDoctrine()->getRepository(Conversations::class)->findById($topic);
        dd($conversation);

        //if()

        return $this->redirectToRoute('chat');
    }

    /**
     * @Route("/chat", name="chat")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function chat(Session $session)
    {
        $users = $this->getDoctrine()->getRepository(Users::class)->findAllUsers();

        $user = $this->getUser();

        $conversations = $this->getDoctrine()->getRepository(Conversations::class)->findByUserId($user->getId());

        return $this->render('chat/conversations.html.twig', ['topic' => '1e9', 'conversations' => $conversations]);
    }

    /**
     * @Route("/chat/{conversation}", name="one_chat")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function one_chat(Session $session, Conversations $conversation)
    {
        $users = $this->getDoctrine()->getRepository(Users::class)->findAllUsers();

        $user = $this->getUser();

        $conversations = $this->getDoctrine()->getRepository(Conversations::class)->findByUserId($user->getId());

        $messages = $this->getDoctrine()->getRepository(Messages::class)->findMessageByConvId($conversation->getId());

        dd($messages);

        return $this->render('chat/conversations.html.twig', ['topic' => '1e9', 'conversations' => $conversations, ]);
    }
}