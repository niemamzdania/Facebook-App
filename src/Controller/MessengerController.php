<?php

namespace App\Controller;

use App\Entity\Users;
use App\Entity\Conversations;
use App\Entity\Messages;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class MessengerController extends AbstractController
{
    /**
     * @Route("/messenger", name="messenger")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function messengerMainPage()
    {
        $users = $this->getDoctrine()->getRepository(Users::class)->findAll();
        $user = $this->getUser();
        $counter = 0;

        foreach ($users as $value)  unset($users[$counter]);
        {
            if($value == $user) unset($users[$content]); 
            $counter ++; 
        }

       

        return $this->render('messenger/messenger.html.twig', [
            'controller_name' => 'MessengerController', 'users' => $users 
        ]);
    }
}
