<?php

namespace App\Controller;

use App\Entity\Projects;
use App\Entity\Users;
use App\Entity\Quests;
use App\Form\ProjectFormType;

use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;

class ProjectsController extends AbstractController
{
    /**
     * @Route("/projects/show", name="show_all_projects")
     * @IsGranted("ROLE_ADMIN")
     */
    public function show_all_projects(PaginatorInterface $paginator, Request $request, Session $session)
    {
        $projects = $this->getDoctrine()->getRepository(Projects::class)->findAllProjects();

        $quests = $this->getDoctrine()->getRepository(Quests::class)->findAllQuests();

        $clients = $this->getDoctrine()->getRepository(Users::class)->findUsersByRole("ROLE_CLIENT");

        $project = new Projects();

        $form = $this->createForm(ProjectFormType::class, $project);

        if ($session->get('message')) {
            $message = $session->get('message');
            $session->remove('message');

            return $this->render('projects/show_all_projects.html.twig', [
                'clients' => $clients,
                'message' => $message,
                'quests' => $quests,
                'form' => $form->createView(),
                'projects' => $paginator->paginate(
                    $projects,
                    $request->query->getInt('page', 1),
                    6
                )]);
        } else {
            return $this->render('projects/show_all_projects.html.twig', [
                'clients' => $clients,
                'quests' => $quests,
                'form' => $form->createView(),
                'projects' => $paginator->paginate(
                    $projects,
                    $request->query->getInt('page', 1),
                    6
                )]);
        }
    }

    /**
     * @Route("/projects/user/{id}", name="show_user_projects")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_CLIENT')")
     */
    public function show_user_projects(Users $user, PaginatorInterface $paginator, Request $request, Session $session)
    {
        if ($this->getUser() != $user && !$this->isGranted("ROLE_ADMIN")) {
            return new Response('Forbidden access');
        }

        $projects = $this->getDoctrine()->getRepository(Projects::class)->findProjectsByUserId($user->getId());

        $quests = [];
        for ($i = 0; $i < count($projects); $i++) {
            $projectQuests = $this->getDoctrine()->getRepository(Quests::class)->findQuestsByProjectId($projects[$i]->getId());
            if ($projectQuests) {
                $quests[$projects[$i]->getId()] = $projectQuests;
            }
        }

        $project = new Projects();

        $form = $this->createForm(ProjectFormType::class, $project);

        if ($session->get('message')) {
            $message = $session->get('message');
            $session->remove('message');

            return $this->render('projects/show_user_projects.html.twig', [
                'message' => $message,
                'quests' => $quests,
                'form' => $form->createView(),
                'projects' => $paginator->paginate(
                    $projects,
                    $request->query->getInt('page', 1),
                    6
                )]);
        } else {
            return $this->render('projects/show_user_projects.html.twig', [
                'quests' => $quests,
                'form' => $form->createView(),
                'projects' => $paginator->paginate(
                    $projects,
                    $request->query->getInt('page', 1),
                    6
                )]);
        }
    }

    /**
     * @Route("/projects/new", name="new_project")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_CLIENT')")
     */
    public function new_project(Request $request, Session $session)
    {
        $project = new Projects();
        $project->setName($request->query->get('project_form')['Name']);

        $client = $this->getDoctrine()->getRepository(Users::class)->findUserById($request->query->get('client'));
        $project->setUser($client);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($project);
        $entityManager->flush();

        $session->set('message', 'Projekt został dodany');

        if ($this->getUser()->getRoles()[0] == "ROLE_CLIENT") {
            return $this->redirectToRoute('show_user_projects', ['id' => $this->getUser()->getId()]);
        } else {
            return $this->redirectToRoute('show_all_projects');
        }
    }

    /**
     * @Route("/projects/edit/{id}", name="edit_project")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_CLIENT')")
     */
    public function edit_project(Projects $project, Request $request, Session $session)
    {
        $entityManager = $this->getDoctrine()->getManager();

        if ($request->query->get('Name')) {
            if ($project->getName() == $request->query->get('Name')) {
                $session->set('message', 'Podano taką samą nazwę projektu');
                if ($this->getUser()->getRoles()[0] == "ROLE_CLIENT") {
                    return $this->redirectToRoute('show_user_projects', ['id' => $this->getUser()->getId()]);
                } else {
                    return $this->redirectToRoute('show_all_projects');
                }
            } else {
                $project->setName($request->query->get('Name'));
                $entityManager->flush();

                $session->set('message', 'Nazwa projektu została zmieniona');
            }
        } elseif ($request->query->get('client')) {
            $projectClient = $this->getDoctrine()->getRepository(Users::class)->findUserById($request->query->get('client'));
            $project->setUser($projectClient);
            $entityManager->flush();

            $session->set('message', 'Klient został zmieniony');
        }

        if ($this->getUser()->getRoles()[0] == "ROLE_CLIENT") {
            return $this->redirectToRoute('show_user_projects', ['id' => $this->getUser()->getId()]);
        } else {
            return $this->redirectToRoute('show_all_projects');
        }
    }

    /**
     * @Route("/projects/delete/{id}", name="delete_project")
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete_project(Projects $project, Request $request, Session $session)
    {
        if (!$this->isGranted('ROLE_ADMIN'))
            return new Response('Forbidden access');

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($project);
        $entityManager->flush();

        $session->set('message', 'Projekt został usunięty');

        return $this->redirectToRoute('show_all_projects');
    }
}
