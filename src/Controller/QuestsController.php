<?php

namespace App\Controller;

use App\Entity\Quests;
use App\Form\QuestFormType;

use App\Service\QuestsService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class QuestsController extends AbstractController
{
    /**
     * @Route("/quest/new", name="add_quest")
     * @IsGranted("ROLE_ADMIN")
     */
    public function add_quest(Request $request, QuestsService $questsService)
    {
        if ($request->request->get('EndDate')) {
            $quest = new Quests();

            //dd($request->request->get('Status'));

            $quest->setUser($this->getUser());

            $entityManager = $this->getDoctrine()->getManager();

            $questsService->saveNewQuest($entityManager, $quest, $request);
        }
        return $this->render('quests/new_quest.html.twig');
    }
}
