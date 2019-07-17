<?php

namespace App\Controller;

use App\Entity\Quests;

use App\Service\QuestsService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class QuestsController extends AbstractController
{
    /**
     * @Route("/quest/new", name="new_quest")
     * @IsGranted("ROLE_ADMIN")
     */
    public function show_form_quest()
    {
        return $this->render('quests/new_quest.html.twig');
    }

    /**
     * @Route("/quest/add", name="add_quest")
     * @IsGranted("ROLE_ADMIN")
     */
    public function add_quest(Request $request, QuestsService $questsService)
    {
            $quest = new Quests();

            $quest->setUser($this->getUser());

            $entityManager = $this->getDoctrine()->getManager();

            $questsService->saveNewQuest($entityManager, $quest, $request);

            return $this->redirectToRoute('show_posts');
    }
}
