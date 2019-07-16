<?php

namespace App\Controller;

use App\Entity\Quests;
use App\Form\QuestFormType;

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
    public function add_quest(Request $request, $quest)
    {
        //$quest = new Quests();

        dd($quest);

        $form = $this->createForm(QuestFormType::class, $quest);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $quest->setAddDate(new \DateTime());
            $quest->setUser($this->getUser());
            //dd($quest);

            $entityManager = $this->getDoctrine()->getmanager();
            $entityManager->persist($quest);
            $entityManager->flush();

            return $this->redirectToRoute('show_posts');
        }

        return $this->render('quests/new_quest.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
