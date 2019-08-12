<?php

namespace App\Controller;

use App\Entity\Posts;
use App\Form\AddPostFormType;
use App\Service\PostsService;
use App\Repository\PostsRepository;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostsController extends AbstractController
{
    /**
     * @Route("/posts", name="show_posts")
     */
    public function show_posts()
    {
        $posts = $this->getDoctrine()->getRepository(Posts::class)->findAllPosts();

        return $this->render('posts/show_posts.html.twig', [
            'posts' => $posts,
        ]);
    }

    /**
     * @Route("/post/new", name="add_post")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function add_post(Request $request, PostsService $postsService)
    {
        $post = new Posts();

        $form = $this->createForm(AddPostFormType::class, $post);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $post->setUser($this->getUser());

            $entityManager = $this->getDoctrine()->getmanager();

            $postsService->setDateToPost($entityManager, $post);
            
            //dd($post);die;
            return $this->redirectToRoute('show_posts');
        }
        
        
        return $this->render('posts/new_post.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/post/edit/{id}", name="edit_post")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function edit_post(Request $request, Posts $post)
    {
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

            return $this->redirectToRoute('show_posts');
        }

        return $this->render('posts/edit_post.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/post/delete/{id}", name="delete_post")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function delete_post(Posts $post)
    {
        if($this->getUser() != $post->getUser()){
            return new Response('Forbidden access');
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($post);
        $entityManager->flush();

        return $this->redirectToRoute('show_posts');
    }
}
