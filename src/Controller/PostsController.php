<?php

namespace App\Controller;

use App\Entity\Posts;
use App\Form\AddPostFormType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostsController extends AbstractController
{
    /**
     * @Route("/posts", name="all_posts")
     */
    public function show_posts()
    {
        $posts = $this->getDoctrine()->getRepository(Posts::class)->findAllPosts();

        return $this->render('posts/index.html.twig', [
            'controller_name' => 'PostsController',
        ]);
    }

    /**
     * @Route("/new_post", name="add_post")'
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function add_post(Request $request)
    {
        $post = new Posts();

        $form = $this->createForm(AddPostFormType::class, $post);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $post->setDate(new \DateTime());
            $post->setUser($this->getUser());

            $entityManager = $this->getDoctrine()->getmanager();
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('test');
        }

        return $this->render('posts/new_post.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/edit_post/{id}", name="edit_post")'
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function edit_post(Request $request, $id)
    {
        $post = $this->getDoctrine()->getRepository(Posts::class)->findPostById($id);

        if(!$post){
            return new Response('Post not found');
        }

        if($this->getUser() != $post->getUser()){
            return new Response('Forbidden access');
        }

        $form = $this->createForm(AddPostFormType::class, $post);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $post->setDate(new \DateTime());
            $post->setUser($this->getUser());

            $entityManager = $this->getDoctrine()->getmanager();
            $entityManager->flush();

            return $this->redirectToRoute('test');
        }

        return $this->render('posts/new_post.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
