<?php

namespace App\Controller;

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
    public function publish(Publisher $publisher, $topic, Request $request)
    {

        $publisher(new Update($topic, $request->getContent()));
        return new Response('success');
    }

    /**
     * @Route("/chat", name="chat")
     */
    public function chat()
    {
        return $this->render('chat/index.html.twig', [
            'config' => [
                'topic' => '1e9',
                'publishRoute' => $this->generateUrl('publisher', ['topic' => '1e9'])
            ]
        ]);
    }
}