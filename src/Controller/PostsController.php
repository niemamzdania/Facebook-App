<?php

namespace App\Controller;

use App\Entity\Posts;
use App\Form\PostFormType;
use App\Service\PostsService;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Facebook\Facebook;

class PostsController extends AbstractController
{
     /**
     * @Route("/post/send", name="send_post")
     */
    public function send_post(PaginatorInterface $paginator)
    {
        $appId = '1305065672989564';
        $appSecret = '7bd264052cfb10caa9b0e15d54867f0e';
        $pageId = '107637360600429';
        $userAccessToken = 'EAASi80fY23wBAFM3rKkDmTZAr7aDZAZCRYT6nvjk0Vo8JLZCY9DJKd2s1VVJ1K73PeXe6QlsZC3oDnwzZCrHLxxZAoPZBF1nhRnycRCjUGqpBWHhHWkI11PmVKHvDY3PKZBzb56PEWgr8ZCCbZBZBYx18pyZAIN6Ff1rbb4R5oLIOwZBDJNZAkK8BU1CUfNnZC5C3CBEmaJeijSHK0iOfekzpMErHNIYszfK36MCwOPKtCKaZC5suygZDZD';

        $fb = new Facebook([
            'app_id' => $appId,
            'app_secret' => $appSecret,
            'default_graph_version' => 'v2.5'
        ]);

        $longLivedToken = $fb->getOAuth2Client()->getLongLivedAccessToken($userAccessToken);

        $fb->setDefaultAccessToken($longLivedToken);

        $response = $fb->sendRequest('GET', $pageId, ['fields' => 'access_token'])
            ->getDecodedBody();

        $foreverPageAccessToken = $response['access_token'];

        $fb = new Facebook([
            'app_id' => $appId,
            'app_secret' => $appSecret,
            'default_graph_version' => 'v4.0'
        ]);

        $fb->setDefaultAccessToken($foreverPageAccessToken);
        $fb->sendRequest('POST', "$pageId/feed", [
            'message' => 'Test Post',
            //'link' => 'http://blog.damirmiladinov.com',
        ]);
        
        var_dump($fb->sendRequest('GET', '/debug_token', ['input_token' => $foreverPageAccessToken])->getDecodedBody());

        return new Response("ok");
    }
    
    /**
     * @Route("/posts", name="show_posts")
     */
    public function show_posts(Request $request, PaginatorInterface $paginator)
    {
        $posts = $this->getDoctrine()->getRepository(Posts::class)->findAllPosts();

        return $this->render('posts/show_posts.html.twig', ['posts' => $paginator->paginate(
            $posts,
            $request->query->getInt('page', 1),
            12
        )]);
    }

    /**
     * @Route("/post/new", name="new_post")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function new_post(Request $request)
    {
        $post = new Posts();

        $form = $this->createForm(PostFormType::class, $post, array('method' => 'POST', 'action' => $this->generateUrl('add_post')));

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

        return $this->redirectToRoute('main_page');
    }

    /**
     * @Route("/post/edit/{id}", name="edit_post")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function edit_post(Posts $post)
    {
        if ($this->getUser() != $post->getUser()) {
            return new Response('Forbidden access');
        }

        $form = $this->createForm(PostFormType::class, $post, array('method' => 'POST', 'action' => $this->generateUrl('save_post')));

        return $this->render('posts/edit_post.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/post/save", name="save_post")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function save_post(PostsService $postsService)
    {
        $post = $this->getDoctrine()->getRepository(Posts::class)->findPostById($_POST['post_form']['id']);

        $post->setUser($this->getUser());

        $entityManager = $this->getDoctrine()->getmanager();

        $postsService->saveEditedPost($entityManager, $post, $_POST);

        return $this->redirectToRoute('main_page');
    }

    /**
     * @Route("/post/delete/{id}", name="delete_post")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function delete_post(Posts $post)
    {
        if ($this->getUser() != $post->getUser()) {
            return new Response('Forbidden access');
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($post);
        $entityManager->flush();

        return new Response("Ok");
        //return $this->redirectToRoute('main_page');
    }
}
