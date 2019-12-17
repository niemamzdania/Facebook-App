<?php

namespace App\Controller;

use App\Entity\Projects;
use App\Entity\Users;
use App\Entity\Quests;
use App\Form\ProjectFormType;

use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

        $users = $this->getDoctrine()->getRepository(Users::class)->findAllUsers();

        $quests = $this->getDoctrine()->getRepository(Quests::class)->findAllQuests();

        $project = new Projects();

        $form = $this->createForm(ProjectFormType::class, $project);

        if($session->get('message')) {
            $message = $session->get('message');
            $session->remove('message');

            return $this->render('projects/show_all_projects.html.twig', [
                'message' => $message, 'users' => $users, 'quests' => $quests, 'form' => $form->createView(),
                'projects' => $paginator->paginate(
                    $projects,
                    $request->query->getInt('page', 1),
                    8
                )]);
        }
        else{
            return $this->render('projects/show_all_projects.html.twig', [
                'users' => $users, 'quests' => $quests, 'form' => $form->createView(),
                'projects' => $paginator->paginate(
                    $projects,
                    $request->query->getInt('page', 1),
                    8
                )]);
        }
    }

    /**
     * @Route("/projects/new", name="new_project")
     * @IsGranted("ROLE_ADMIN")
     */
    public function new_project(Request $request, Session $session)
    {
        $project = new Projects();
        $project->setName($request->query->get('project_form')['Name']);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($project);
        $entityManager->flush();

        $session->set('message', 'Projekt został dodany');

        return $this->redirectToRoute('show_all_projects');
    }

    /**
     * @Route("/projects/edit/{id}", name="edit_project")
     * @IsGranted("ROLE_ADMIN")
     */
    public function edit_project(Projects $project, Request $request, Session $session)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $project->setName($request->query->get('project_form')['Name']);
        $entityManager->flush();

        $session->set('message', 'Projekt został zmodyfikowany');

        return $this->redirectToRoute('show_all_projects');
    }

    /**
     * @Route("/projects/delete/{id}", name="delete_project")
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete_project(Projects $project, Request $request, Session $session)
    {
        if(!$this->isGranted('ROLE_ADMIN'))
            return new Response('Forbidden access');

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($project);
        $entityManager->flush();

        $session->set('message', 'Projekt został usunięty');

        return $this->redirectToRoute('show_all_projects');
    }
}
