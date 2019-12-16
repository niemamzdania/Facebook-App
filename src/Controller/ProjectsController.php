<?php

namespace App\Controller;

use App\Entity\Projects;
use App\Entity\Users;
use App\Entity\Quests;
use App\Form\ProjectFormType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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
    public function show_all_projects(Request $request, Session $session)
    {
        $projects = $this->getDoctrine()->getRepository(Projects::class)->findAllProjects();

        $users = $this->getDoctrine()->getRepository(Users::class)->findAllUsers();

        $quests = $this->getDoctrine()->getRepository(Quests::class)->findAllQuests();

        $project = new Projects();

        $form = $this->createForm(ProjectFormType::class, $project);

        return $this->render('projects/show_all_projects.html.twig', [
            'projects' => $projects, 'users' => $users, 'quests' => $quests, 'form' => $form->createView()
        ]);
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

        return $this->redirectToRoute('show_all_projects');
    }


}
