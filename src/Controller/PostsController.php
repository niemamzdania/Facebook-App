<?php

namespace App\Controller;

use App\Entity\Photos;
use App\Entity\Posts;
use App\Form\PostFormType;
use App\Service\PostsService;

use Facebook\Facebook;
use Facebook\FileUpload\FacebookFile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Cloudinary\Search;

class PostsController extends AbstractController
{
    /**
     * @Route("/post/send/{id}", name="send_post")
     */
    public function send_post(Posts $post, Session $session)
    {
        $user = $this->getUser();

        $appId = $user->getAppId();
        $appSecret = $user->getAppSecret();
        $pageId = $user->getPageId();
        $userAccessToken = $user->getUserAccessToken();

        $entityManager = $this->getDoctrine()->getManager();
        $post->setStatus(true);
        $entityManager->flush();
        $message = $post->getContent();
        $photo = $this->getDoctrine()->getRepository(Photos::Class)->findPhotoByPostId($post->getId());

        if ($photo != NULL) {
            $directory = $this->getParameter('upload_directory');
            $date = $post->getDate();
            $dateInString = $date->format('Y-m');
            $directory = $directory . '/' . $dateInString;

            $finder = new Finder();
            $finder->files()->in($directory)->name($photo->getName());

            foreach ($finder as $currentPhoto) {
                $photoPath = $currentPhoto->getPathName();
            }
        }

        $fb = new Facebook([
            'app_id' => $appId,
            'app_secret' => $appSecret,
            'default_graph_version' => 'v4.0'
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

        if (isset($photoPath)) {
            $photoToUpload = new FacebookFile($photoPath);

            $fb->setDefaultAccessToken($foreverPageAccessToken);
            $fb->sendRequest('POST', "$pageId/photos", [
                'message' => $message,
                'picture' => $photoToUpload,
            ]);
        } else {
            $fb->setDefaultAccessToken($foreverPageAccessToken);
            $fb->sendRequest('POST', "$pageId/feed", [
                'message' => $message,
            ]);
        }

        $session->set('message', 'Post został opublikowany');

        return $this->redirectToRoute('show_posts');
    }

    /**
     * @Route("/post/save", name="save_post")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function save_post(Request $request, PostsService $postsService, Session $session)
    {
        $session = $request->getSession();

        $post = $this->getDoctrine()->getRepository(Posts::class)->findPostById($session->get('id'));
        $post_number = $post->getId();

        if (!$post)
            return new Response('Nie znaleziono postu do edycji.');

        $photo = $this->getDoctrine()->getRepository(Photos::class)->findPhotoByPostId($post->getId());

        $entityManager = $this->getDoctrine()->getmanager();

        $tempDirectory = $this->getParameter('upload_temp_directory');

        $postsService->saveEditedPost($entityManager, $post, $photo, $tempDirectory, $request);
        $session->set('message', 'Post został zmodyfikowany');

        $photo = $this->getDoctrine()->getRepository(Photos::class)->findPhotoByPostId($post->getId());
        if($photo) {
            //Config of server
            \Cloudinary::config(array(
                "cloud_name" => "przemke",
                "api_key" => "884987643496832",
                "api_secret" => "9KWlEeWnpdqZyo2GlohdLAqibeU",
                "secure" => true
            ));
            $post = $this->getDoctrine()->getRepository(Posts::class)->findPostById($post->getId());
            $search = new Search();
            $searchData = "folder=".$post->getDate()->format('Y-m')." AND filename=".$photo->getName();
            $result = $search
                ->expression($searchData)
                ->max_results(1)
                ->execute();
            if(isset($result['resources'][0])) {
                $photoPath = $result['resources'][0]['url'];
                return $this->redirectToRoute('show_user_posts', ['photoPath' => $photoPath, 'message' => $session->get('message')]);
            }
        }

        return $this->redirectToRoute('show_user_posts', ['message' => $session->get('message')]);
    }

    /**
     * @Route("/post/new", name="new_post")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function new_post(Request $request, Session $session)
    {
        $post = new Posts();

        $form = $this->createForm(PostFormType::class, $post);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $session = $request->getSession();
            $session->set('data', $request->request->get('post_form'));
            $session->set('file', $_FILES);

            if (isset($_FILES['post_form'])) {
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
    public function add_post(Session $session, Request $request, PostsService $postsService)
    {
        $post = new Posts();

        $post->setUser($this->getUser());

        $entityManager = $this->getDoctrine()->getManager();

        $directory = $this->getParameter('upload_directory');

        $tempDirectory = $this->getParameter('upload_temp_directory');

        $postsService->saveNewPost($entityManager, $post, $tempDirectory, $request);

        $session->set('message', 'Post został poprawnie utworzony');

        return $this->redirectToRoute('show_user_posts');
    }

    /**
     * @Route("/post/{id}", name="show_post")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function show_post(Request $request, Posts $post)
    {
        if ($this->getUser() != $post->getUser() && !$this->isGranted("ROLE_ADMIN")) {
            return new Response('Forbidden access');
        }

        $photo = $this->getDoctrine()->getRepository(Photos::Class)->findPhotoByPostId($post->getId());

        if ($photo != NULL) {
            //Config of server
            \Cloudinary::config(array(
                "cloud_name" => "przemke",
                "api_key" => "884987643496832",
                "api_secret" => "9KWlEeWnpdqZyo2GlohdLAqibeU",
                "secure" => true
            ));

            $search = new Search();
            $searchData = "folder=".$photo->getPost()->getDate()->format('Y-m')." AND filename=".$photo->getName();
            $result = $search
                ->expression($searchData)
                ->max_results(1)
                ->execute();

            if (isset($result['resources'][0])) {
                return $this->render('posts/show_post.html.twig', ['post' => $post, 'photoPath' => $result['resources'][0]['url']]);
            }

            $entityManager = $this->getDoctrine()->getmanager();
            $entityManager->remove($photo);
            $entityManager->flush();
        }

        return $this->render('posts/show_post.html.twig', ['post' => $post]);
    }

    /**
     * @Route("/posts", name="show_posts")
     * @IsGranted("ROLE_ADMIN")
     */
    public function show_all_posts(Request $request, PaginatorInterface $paginator, Session $session)
    {
        $posts = $this->getDoctrine()->getRepository(Posts::class)->findAllPosts();

        if ($session->get('message')) {
            $message = $session->get('message');
            $session->remove('message');
            return $this->render('posts/show_posts.html.twig', ['message' => $message, 'posts' => $paginator->paginate(
                $posts,
                $request->query->getInt('page', 1),
                7
            )]);
        } else {
            return $this->render('posts/show_posts.html.twig', ['posts' => $paginator->paginate(
                $posts,
                $request->query->getInt('page', 1),
                8
            )]);
        }
    }

    /**
     * @Route("/user/posts", name="show_user_posts")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function show_user_posts(Request $request, PaginatorInterface $paginator, Session $session)
    {
        $posts = $this->getDoctrine()->getRepository(Posts::class)->findPostsByUserId($this->getUser()->getId());

        if ($session->get('message')) {
            $message = $session->get('message');
            $session->remove('message');
            return $this->render('posts/show_posts.html.twig', ['message' => $message, 'posts' => $paginator->paginate(
                $posts,
                $request->query->getInt('page', 1),
                7
            )]);
        } else {
            return $this->render('posts/show_posts.html.twig', ['posts' => $paginator->paginate(
                $posts,
                $request->query->getInt('page', 1),
                8
            )]);
        }
    }

    /**
     * @Route("/post/edit/{id}", name="edit_post")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function edit_post(Posts $post, Request $request, Session $session)
    {
        if ($this->getUser() != $post->getUser()) {
            return new Response('Forbidden access');
        }

        $form = $this->createForm(PostFormType::class, $post);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $session = $request->getSession();
            $session->set('data', $request->request->get('post_form'));
            $session->set('id', $post->getId());
            $session->set('file', $_FILES);

            if (isset($_FILES['post_form'])) {
                move_uploaded_file($_FILES['post_form']['tmp_name']['name'], "uploads/" . $_FILES['post_form']['name']['name']);
            }

            return $this->redirect($this->generateUrl('save_post'));
        }

        $photo = $this->getDoctrine()->getRepository(Photos::class)->findPhotoByPostId($post->getId());

        if ($session->get('message')) {
            $message = $session->get('message');
            $session->remove('message');
        }

        if ($photo) {
            $dateInString = $post->getDate()->format('Y-m');

            //Config of server
            \Cloudinary::config(array(
                "cloud_name" => "przemke",
                "api_key" => "884987643496832",
                "api_secret" => "9KWlEeWnpdqZyo2GlohdLAqibeU",
                "secure" => true
            ));

            $search = new Search();
            $searchData = "folder=".$dateInString." AND filename=".$photo->getName();
            $result = $search
                ->expression($searchData)
                ->max_results(1)
                ->execute();

            if (isset($result['resources'][0])) {
                $photoPath = $result['resources'][0]['url'];
                if (isset($message)) {
                    return $this->render('posts/edit_post.html.twig', [
                        'form' => $form->createView(), 'photoPath' => $photoPath, 'message' => $message, 'id' => $post->getId(),
                    ]);
                } else {
                    return $this->render('posts/edit_post.html.twig', [
                        'form' => $form->createView(), 'photoPath' => $photoPath, 'id' => $post->getId(),
                    ]);
                }
            } else {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($photo);
                $entityManager->flush();
            }
        }

        if (isset($message)) {
            return $this->render('posts/edit_post.html.twig', [
                'form' => $form->createView(), 'id' => $post->getId(), 'message' => $message,
            ]);
        } else {
            return $this->render('posts/edit_post.html.twig', [
                'form' => $form->createView(), 'id' => $post->getId(),
            ]);
        }
    }

    /**
     * @Route("/post/delete/{id}", name="delete_post")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function delete_post(Posts $post, Request $request, Session $session)
    {
        if ($this->getUser() != $post->getUser() && !$this->isGranted("ROLE_ADMIN")) {
            return new Response('Forbidden access');
        }

        $photo = $this->getDoctrine()->getRepository(Photos::class)->findPhotoByPostId($post->getId());

        $entityManager = $this->getDoctrine()->getManager();

        if ($photo) {
            //Config of server
            \Cloudinary::config(array(
                "cloud_name" => "przemke",
                "api_key" => "884987643496832",
                "api_secret" => "9KWlEeWnpdqZyo2GlohdLAqibeU",
                "secure" => true
            ));

            //Delete photo from server and database
            $search = new Search();
            $searchData = "folder=" . $photo->getPost()->getDate()->format('Y-m') . " AND filename=" . $photo->getName();
            $result = $search
                ->expression($searchData)
                ->max_results(1)
                ->execute();

            \Cloudinary\Uploader::destroy($result['resources'][0]['public_id']);

            $entityManager->remove($photo);
            $entityManager->flush();
        }

        if (!$request->request->get('onlyPhoto')) {
            $entityManager->remove($post);
            $entityManager->flush();

            $session->set('message', 'Post został usunięty');

            return $this->redirectToRoute('show_user_posts');
        }
        else{
            $session->set('message', 'Zdjęcie zostało usunięte');
        }

        return $this->redirectToRoute('edit_post', ['id' => $post->getId()]);
    }
}
