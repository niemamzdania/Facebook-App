<?php

namespace App\Controller;

use App\Entity\Users;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mercure\Publisher;
use Symfony\Component\Mercure\Update;

class PublisherController extends AbstractController
{
    /**
     * @Route("/publish/{topic}", name="publisher", methods={"POST"})
     */
    public function publish($topic, Publisher $publisher, Request $request, Session $session)
    {
        $update = new Update($topic, $request->request->get('Content'));

        $publisher($update);

        return $this->redirectToRoute('chat');
    }

    /**
     * @Route("/chat", name="chat")
     */
    public function chat(Session $session)
    {

        return $this->render('chat/index.html.twig', ['topic' => '1e9']);
    }
}