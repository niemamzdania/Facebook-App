<?php

namespace App\Controller;

use App\Entity\Quests;
use App\Entity\Projects;
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

        $projectName = $request->request->get('Project');

        $project = $this->getDoctrine()->getRepository(Projects::class)->findProjectByName($projectName);

        $entityManager = $this->getDoctrine()->getManager();

        if(!isset($project)) {
            $project = new Projects();
            $project->setName($projectName);
            $entityManager->persist($project);
            $entityManager->flush();
        }

        $questsService->saveNewQuest($entityManager, $quest, $project, $request);
        $session->set('message', 'Zadanie zostało dodane');

        return $this->redirectToRoute('show_all_quests',['id'=>$this->getUser()->getId()]);
    }

    /**
     * @Route("/quest/new", name="new_quest")
     * @IsGranted("ROLE_ADMIN")
     */
    public function show_form_quest(Request $request)
    {
        $quest = new Quests();

        $date = new \DateTime();
        $dateInString = $date->format('Y-m-d');

        $futureDate = date('Y-m-d', strtotime('+1 year'));
        $futureDate = date('Y-m-d', strtotime('+1 year', strtotime($dateInString)));

        $users = $this->getDoctrine()->getRepository(Users::class)->findAllUsers();

        $projects = $this->getDoctrine()->getRepository(Projects::class)->findAllProjects();

        if ($users && $projects)
            return $this->render('quests/new_quest.html.twig', ['users' => $users, 'quest' => $quest, 'projects' => $projects,
                'minDate' => $dateInString, 'maxDate' => $futureDate]);
        elseif($users && !$projects)
            return $this->render('quests/new_quest.html.twig', ['users' => $users, 'quest' => $quest,
                'minDate' => $dateInString, 'maxDate' => $futureDate]);

        if($request->getLocale() == 'en')
            return new Response('No employees to add them the task.');
        return new Response('Brak pracowników, którym można przydzielić zadanie.');
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

        $projectName = $request->request->get('Project');
        $project = $this->getDoctrine()->getRepository(Projects::class)->findProjectByName($projectName);

        if(!$project){
            $project = new Projects();
            $project->setName($projectName);
            $entityManager->persist($project);
            $entityManager->flush();
        }

        $questsService->saveEditedQuest($entityManager, $quest, $project, $request);
        $session->set('message','Zadanie zostało zmodyfikowane');
        
        return $this->redirectToRoute('show_quest',['id'=>$quest->getId()]);
    }

    /**
     * @Route("/quests/all", name="show_all_quests")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function show_all_quests(PaginatorInterface $paginator, Request $request, Session $session)
    {
        if(!$this->isGranted("ROLE_ADMIN")) {
            return new Response("Forbidden access");
        }

        $quests = $this->getDoctrine()->getRepository(Quests::class)->findAllQuests();

        if($session->get('message'))
        {
            $message = $session->get('message');
            $session->remove('message');
            return $this->render('quests/show_quests.html.twig', ['message' => $message, 'user' => 'user',
                'quests' => $paginator->paginate(
                $quests,
                $request->query->getInt('page', 1),
                8
            )]);
        }
        else{
            return $this->render('quests/show_quests.html.twig', ['user' => 'user',
                'quests' => $paginator->paginate(
                    $quests,
                    $request->query->getInt('page', 1),
                    8
                )]);
        }
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

        $projects = $this->getDoctrine()->getRepository(Projects::class)->findAllProjects();

        return $this->render('quests/edit_quest.html.twig', ['users' => $users, 'quest' => $quest, 'projects' =>$projects,  'minDate' => $dateInString,
            'maxDate' => $futureDate, 'questDate' => $questDateInString]);
    }

    /**
     * @Route("/quest/delete/{id}", name="delete_quest")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function delete_post(Quests $quest, Session $session)
    {
        if ($this->getUser() != $quest->getUser() && !$this->isGranted("ROLE_ADMIN")) {
            return new Response('Forbidden access');
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($quest);
        $entityManager->flush();

        $session->set('message','Zadanie zostało usunięte');

        return $this->redirectToRoute('show_all_quests');
    }
}
