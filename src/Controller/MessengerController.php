<?php

namespace App\Controller;

use App\Entity\Users;
use App\Entity\Conversations;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MessengerController extends AbstractController
{
    /**
     * @Route("/messenger", name="messenger")
     */
    public function messengerMainPage()
    {
        $users = $this->getDoctrine()->getRepository(Users::class)->findAll();
        $user = $this->getUser();
        dump($user);
        $conversation = $this->getDoctrine()->getRepository(Conversations::class)->findAll();
        
        return $this->render('messenger/messenger.html.twig', [
            'controller_name' => 'MessengerController', 'users' => $users , 'conversation' => $conversation[0]
        ]);
    }
}
