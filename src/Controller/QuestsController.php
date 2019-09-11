<?php

namespace App\Controller;

use App\Entity\Quests;
use App\Entity\Users;

use App\Service\QuestsService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class QuestsController extends AbstractController
{
    /**
     * @Route("/quest/add", name="add_quest")
     * @IsGranted("ROLE_ADMIN")
     */
    public function add_quest(Request $request, QuestsService $questsService, Session $session)
    {
        $quest = new Quests();

        $userId = $request->request->get('user');

        $user = $this->getDoctrine()->getRepository(Users::class)->findUserById($userId);

        $quest->setUser($user);

        $entityManager = $this->getDoctrine()->getManager();

        $questsService->saveNewQuest($entityManager, $quest, $request);
        $session->set('message', 'Quest has been created');

        return $this->redirectToRoute('show_user_quests',['id'=>$this->getUser()->getId()]);
    }

    /**
     * @Route("/quest/new", name="new_quest")
     * @IsGranted("ROLE_ADMIN")
     */
    public function show_form_quest()
    {
        $quest = new Quests();

        $date = new \DateTime();
        $dateInString = $date->format('Y-m-d');

        $futureDate = date('Y-m-d', strtotime('+1 year'));
        $futureDate = date('Y-m-d', strtotime('+1 year', strtotime($dateInString)));

        $users = $this->getDoctrine()->getRepository(Users::class)->findAllUsers();

        if ($users)
            return $this->render('quests/new_quest.html.twig', ['users' => $users, 'quest' => $quest, 'minDate' => $dateInString, 'maxDate' => $futureDate]);

        return new Response('No employees to add them the task');
    }

    /**
     * @Route("/quest/save", name="save_quest")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function save_quest(QuestsService $questsService, Request $request, Session $session)
    {
        $quest = $this->getDoctrine()->getRepository(Quests::class)->findQuestById($request->request->get('id'));

        if($this->isGranted("ROLE_ADMIN")) {
            $userId = $request->request->get('user');
            $user = $this->getDoctrine()->getRepository(Users::class)->findUserById($userId);
            $quest->setUser($user);
        }

        $entityManager = $this->getDoctrine()->getmanager();

        $questsService->saveEditedQuest($entityManager, $quest, $request);
        $session->set('message','Quest has been edited');
        
        return $this->redirectToRoute('show_quest',['id'=>$quest->getId()]);
    }

    /**
     * @Route("/quests/all", name="show_all_quests")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function show_all_quests(PaginatorInterface $paginator, Request $request)
    {
        $quests = $this->getDoctrine()->getRepository(Quests::class)->findAllQuests();

           return $this->render('quests/show_quests.html.twig', ['user' => 'user', 'quests' => $paginator->paginate(
                $quests,
                $request->query->getInt('page', 1),
                8
            )]);
         
    }

    /**
     * @Route("/quest/{id}", name="show_quest")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function show_quest($id, Session $session)
    {   
        $quest = $this->getDoctrine()->getRepository(Quests::class)->find($id);

        if($session->get('message'))
        {
            $message = $session->get('message');
            $session->remove('message');
            return $this->render('quests/show_one_quest.html.twig', ['message' => $message, 'quest' => $quest]);
        }
        else
        {
            return $this->render('quests/show_one_quest.html.twig', ['quest' => $quest]);
        }
    }

    /**
     * @Route("/quests/user/{id}", name="show_user_quests")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function show_user_quests($id,  PaginatorInterface $paginator, Request $request, Session $session)
    {
        if ($id != $this->getUser()->getId())
            return new Response('Forbidden access');

        $quests = $this->getDoctrine()->getRepository(Quests::class)->findByUserId($id);

        
        if($session->get('message'))
        {
            $message = $session->get('message');
            $session->remove('message');
           
            return $this->render('quests/show_quests.html.twig', ['message' => $message, 'quests' => $paginator->paginate(
                $quests,
                $request->query->getInt('page', 1),
                8
            )]);
        }
        else
        {
            return $this->render('quests/show_quests.html.twig', ['quests' => $paginator->paginate(
                $quests,
                $request->query->getInt('page', 1),
                8
            )]);
        }
    }

     /**
     * @Route("/quest/edit/{id}", name="edit_quest")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function edit_quest(Request $request, Quests $quest)
    {
        if ($this->getUser() != $quest->getUser() &&
            !$this->isGranted("ROLE_ADMIN")) {
            return new Response('Forbidden access');
        }

        $date = new \DateTime();
        $dateInString = $date->format('Y-m-d');

        $futureDate = date('Y-m-d', strtotime('+1 year'));
        $futureDate = date('Y-m-d', strtotime('+1 year', strtotime($dateInString)));

        $questDate = $quest->getEndDate();
        $questDateInString = $questDate->format('Y-m-d');

        $users = $this->getDoctrine()->getRepository(Users::class)->findAllUsers();

        return $this->render('quests/edit_quest.html.twig', ['users' => $users, 'quest' => $quest, 'minDate' => $dateInString, 'maxDate' => $futureDate, 'questDate' => $questDateInString]);
    }

    /**
     * @Route("/quest/delete/{id}", name="delete_quest")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function delete_post(Quests $quest, Session $session)
    {
        if ($this->getUser() != $quest->getUser()) {
            return new Response('Forbidden access');
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($quest);
        $entityManager->flush();

        $session->set('message','Quest has been deleted');

        return $this->redirectToRoute('show_user_quests',['id'=>$this->getUser()->getId()]);
    }
}
