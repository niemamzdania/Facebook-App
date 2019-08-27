<?php

namespace App\Controller;

use App\Entity\Photos;
use App\Entity\Posts;
use App\Form\PostFormType;
use App\Service\PostsService;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Facebook\Facebook;

class PostsController extends AbstractController
{
    /**
     * @Route("/post/send/{id}", name="send_post")
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
     * @Route("/post/save", name="save_post")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function save_post(Request $request, PostsService $postsService)
    {
        $post = $this->getDoctrine()->getRepository(Posts::class)->findPostById($request->request->get('post_form')['id']);

        if(!$post)
            return new Response('Post to edit not found');

        $photo = $this->getDoctrine()->getRepository(Photos::class)->findPhotoByPostId($post->getId());

        $directory = $this->getParameter('upload_directory');

        $entityManager = $this->getDoctrine()->getmanager();

        $postsService->saveEditedPost($entityManager, $post, $photo, $directory, $request);

        return $this->redirectToRoute('main_page');
    }

    /**
     * @Route("/post/new", name="new_post")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function new_post(Request $request)
    {
        $post = new Posts();

        $form = $this->createForm(PostFormType::class, $post);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $session = $request->getSession();
            $session->set('data', $request->request->get('post_form'));
            $session->set('file', $_FILES);

            if(isset($_FILES['post_form'])) {
                move_uploaded_file($_FILES['post_form']['tmp_name']['name'], "uploads/" . $_FILES['post_form']['name']['name']);
            }

            return $this->redirect($this->generateUrl('add_post'));
        }

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

        $entityManager = $this->getDoctrine()->getManager();

        $directory = $this->getParameter('upload_directory');

        $tempDirectory = $this->getParameter('upload_temp_directory');

        $postsService->saveNewPost($entityManager, $post, $directory, $tempDirectory, $request);

        return $this->redirectToRoute('show_posts');
    }

    /**
     * @Route("/post/{id}", name="show_post")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function show_post(Request $request, Posts $post)
    {
        if ($this->getUser() != $post->getUser()) {
            return new Response('Forbidden access');
        }

        return $this->render('posts/show_post.html.twig', ['post' => $post]);
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
     * @Route("/post/edit/{id}", name="edit_post")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function edit_post(Posts $post)
    {
        if ($this->getUser() != $post->getUser()) {
            return new Response('Forbidden access');
        }

        $photo = $this->getDoctrine()->getRepository(Photos::class)->findPhotoByPostId($post->getId());

        $form = $this->createForm(PostFormType::class, $post, array('method' => 'POST', 'action' => $this->generateUrl('save_post')));

        if ($photo) {
            $directory = $this->getParameter('upload_directory');

            $date = $post->getDate();
            $dateInString = $date->format('Y-m');

            $directory = $directory . "/" . $dateInString;

            $finder = new Finder();

            $finder->files()->in($directory)->name($photo->getName());

            foreach ($finder as $file) {
                $photoPath = $dateInString . '/' . $file->getRelativePathname();
            }

            if (isset($photoPath)) {
                return $this->render('posts/edit_post.html.twig', [
                    'form' => $form->createView(), 'photoPath' => $photoPath
                ]);
            }
            else {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($photo);
                $entityManager->flush();
            }
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

        $photo = $this->getDoctrine()->getRepository(Photos::class)->findPhotoByPostId($post->getId());

        $entityManager = $this->getDoctrine()->getManager();

        if($photo)
        {
            $directory = $this->getParameter('upload_directory');
            $date = $post->getDate();
            $dateInString = $date->format('Y-m');
            $directory = $directory . "/" . $dateInString;

            $finder = new Finder();
            $finder->files()->in($directory)->name($photo->getName());

            foreach ($finder as $currentPhoto) {
                $fileSystem = new Filesystem();

                $fileSystem->remove([$currentPhoto->getPathname()]);
            }

            $entityManager->remove($photo);
            $entityManager->flush();
        }

        $entityManager->remove($post);
        $entityManager->flush();


        return $this->redirectToRoute('show_posts');
    }
}
