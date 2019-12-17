<?php

namespace App\Controller;

use App\Entity\Conversations;
use App\Entity\Messages;
use App\Entity\Avatars;
use App\Entity\Users;

use Cloudinary\Search;
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
     * @Route("/message/send/{topic}", name="send_message", methods={"POST", "GET"})
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

        $avatars = [];
        $avatar = $this->getDoctrine()->getRepository(Avatars::class)->findAvatarByUserId($user->getId());

        if($avatar) {
            array_push($avatars, $avatar);
        }

        for($i=0; $i<count($conversations); $i++) {
            $avatar1 = $this->getDoctrine()->getRepository(Avatars::class)->findAvatarByUserId($conversations[$i]->getUser1());
            $avatar2 = $this->getDoctrine()->getRepository(Avatars::class)->findAvatarByUserId($conversations[$i]->getUser2());

            if($avatar1 && $avatar1->getUser() != $user)
                array_push($avatars, $avatar1);
            else if($avatar2 && $avatar2->getUser() != $user)
                array_push($avatars, $avatar2);

            unset($avatar1);
            unset($avatar2);
        }

        if ($avatars) {
            $photoPaths = [];

            //Config of server
            \Cloudinary::config(array(
                "cloud_name" => "przemke",
                "api_key" => "884987643496832",
                "api_secret" => "9KWlEeWnpdqZyo2GlohdLAqibeU",
                "secure" => true
            ));

            for($i=0; $i<count($avatars); $i++){
                $search = new Search();
                $searchData = "folder=avatars AND filename=".$avatars[$i]->getName();
                $result = $search
                    ->expression($searchData)
                    ->max_results(1)
                    ->execute();

                if($result['resources'][0])
                    $photoPaths[$avatars[$i]->getUser()->getId()] = $result['resources'][0]['url'];
            }

            if ($photoPaths) {
                $session->set('avatars', $photoPaths);

                return $this->render('chat/conversations.html.twig', ['avatars' => $photoPaths, 'conversations' => $conversations]);
            }
        }

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

        if($session->get('avatars')){
            $avatars = $session->get('avatars');

            return $this->render('chat/conversations.html.twig', ['avatars' => $avatars, 'topic' => (string)$conversation->getId(), 'conversations' => $conversations,
                'messages' => $messages, 'id_conv' => $id_conv]);
        }

        return $this->render('chat/conversations.html.twig', ['topic' => (string)$conversation->getId(), 'conversations' => $conversations,
            'messages' => $messages, 'id_conv' => $id_conv]);
    }
}