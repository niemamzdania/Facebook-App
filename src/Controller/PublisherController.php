<?php

namespace App\Controller;

use App\Entity\Users;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mercure\Publisher;
use Symfony\Component\Mercure\Update;

class PublisherController extends AbstractController
{
    /**
     * @Route("/publish/{topic}", name="publisher", methods={"POST"})
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function publish($topic, Publisher $publisher, Request $request, Session $session)
    {
        $update = new Update($topic, $request->request->get('Content'));

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

        return $this->render('chat/index.html.twig', ['topic' => '1e9','users' => $users]);
    }
}