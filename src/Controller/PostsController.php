<?php

namespace App\Controller;

use App\Entity\Posts;
use App\Form\AddPostFormType;
use App\Service\PostsService;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
     * @Route("/post/new", name="new_post")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function new_post(Request $request)
    {
        $post = new Posts();

        $form = $this->createForm(AddPostFormType::class, $post, array('method' => 'POST', 'action' => $this->generateUrl('add_post')));




        /*$form = $this->createFormBuilder($post)
            ->setAction($this->generateUrl('add_post'))
            ->setMethod('POST')
            ->add('Title', TextType::class, array('attr' => array('class' => 'form-control')))
            ->add('Content', TextareaType::class, array('required' => false, 'attr' => array('class' => 'form-control')))
            ->add('Submit', SubmitType::class, array('label' => 'Add Post', 'attr' => array('class' => 'btn btn-primary mt-3')))
            ->getForm();
*/
        return $this->render('posts/new_post.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/post/add", name="add_post")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function add_post(Request $request, PostsService $postsService)
    {
        $post = new Posts();

        $post->setUser($this->getUser());

        $entityManager = $this->getDoctrine()->getmanager();

        $postsService->saveNewPost($entityManager, $post, $_POST);

        return $this->redirectToRoute('mainpage');
    }

    /**
     * @Route("/post/edit/{id}", name="edit_post")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public
    function edit_post(Request $request, Posts $post)
    {
        if ($this->getUser() != $post->getUser()) {
            return new Response('Forbidden access');
        }

        $form = $this->createForm(AddPostFormType::class, $post);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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
    public
    function delete_post(Posts $post)
    {
        if ($this->getUser() != $post->getUser()) {
            return new Response('Forbidden access');
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($post);
        $entityManager->flush();

        return $this->redirectToRoute('show_posts');
    }
}
