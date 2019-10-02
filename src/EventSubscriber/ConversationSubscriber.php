<?php

namespace App\EventSubscriber;

use App\Entity\Conversations;
use App\Entity\Users;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ConversationSubscriber extends AbstractController implements EventSubscriberInterface
{
    public function onKernelConversationRequest(RequestEvent $event)
    {
        if($this->getUser()) {
            $conversations = $this->getDoctrine()->getRepository(Conversations::class)->findConvByUserId($this->getUser()->getId());
            $session = new Session();
            $session->set('conversations', $conversations);
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => [['onKernelConversationRequest', 0]],
        ];
    }
}
