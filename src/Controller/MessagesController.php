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
    public function publish($topic, Publisher $publisher, Request $request, Session $session)
    {
        $update = new Update((string)$topic, $request->request->get('Content'));



        $publisher($update);

        return $this->redirectToRoute('chat');
    }

    /**
     * @Route("/chat", name="chat")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function chat(Session $session)
    {
        $users = $this->getDoctrine()->getRepository(Users::class)->findAllUsers();

        return $this->render('chat/conversations.html.twig', ['topic' => '
        ','users' => $users]);
    }
}