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
    public function send_post(PaginatorInterface $paginator, $id)
    {
        $user = $this->getUser();
               
        $appId = $user->getAppId();
        $appSecret = $user->getAppSecret();
        $pageId = $user->getPageId();
        $userAccessToken = $user->getUserAccessToken();

        $post = $this->getDoctrine()->getRepository(Posts::Class)->find($id);
        $message = $post->getContent();
        $photo = $this->getDoctrine()->getRepository(Photos::Class)->findPhotoByPostId($post->getId()); 
        
        if($photo!=NULL)
            {
                $directory = $this->getParameter('upload_directory');
                $date = $post->getDate();
                $dateInString = $date->format('Y-m');
                $directory = $directory . '/' . $dateInString;
                
                $finder = new Finder();
                $finder->files()->in($directory)->name($photo->getName());

                foreach ($finder as $currentPhoto) {
                    //dd($currentPhoto);
                    $photoPath = $currentPhoto->getPathName();
                    //dd($photoPath);
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
        
        $picture = 'http://www.codexworld.com/wp-content/uploads/2015/12/www-codexworld-com-programming-blog.png';

        $fb->setDefaultAccessToken($foreverPageAccessToken);
        $fb->sendRequest('POST', "$pageId/feed", [
            'message' => $message,
            'photo' => 'https://images.pexels.com/photos/1040626/pexels-photo-1040626.jpeg',
        ]);
        //http://127.0.0.1:8000/uploads/photos/2019-08/9f83dd59bd856af25ecd7425a0e52c47.png
        //var_dump($fb->sendRequest('GET', '/debug_token', ['input_token' => $foreverPageAccessToken])->getDecodedBody());
        
        return $this->redirectToRoute('show_posts');
    }

    /**
     * @Route("/post/save", name="save_post")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function save_post(Request $request, PostsService $postsService)
    {
        $session = $request->getSession();

        $post = $this->getDoctrine()->getRepository(Posts::class)->findPostById($session->get('id'));
        $post_number = $post->getId();
        
        if(!$post)
            return new Response('Post to edit not found');

        $photo = $this->getDoctrine()->getRepository(Photos::class)->findPhotoByPostId($post->getId());

        $directory = $this->getParameter('upload_directory');

        $entityManager = $this->getDoctrine()->getmanager();

        $tempDirectory = $this->getParameter('upload_temp_directory');

        $postsService->saveEditedPost($entityManager, $post, $photo, $directory, $tempDirectory, $request);

        return $this->redirect('/post/edit/'.$post_number);
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

        $photo = $this->getDoctrine()->getRepository(Photos::Class)->findPhotoByPostId($post->getId());

        if($photo!=NULL)
        {
        $directory = $this->getParameter('upload_directory');
        $date = $post->getDate();
        $dateInString = $date->format('Y-m');
        $directory = $directory . "/" . $dateInString;
    
        $finder = new Finder();

            $finder->files()->in($directory)->name($photo->getName());

            foreach ($finder as $file) {
                $photoPath = $dateInString . '/' . $file->getRelativePathname();
            }
            return $this->render('posts/show_post.html.twig', ['post' => $post, 'photoPath' => $photoPath]);
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
    public function edit_post(Posts $post, Request $request)
    {
        if ($this->getUser() != $post->getUser()) {
            return new Response('Forbidden access');
        }

        $form = $this->createForm(PostFormType::class, $post);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $session = $request->getSession();
            $session->set('data', $request->request->get('post_form'));
            $session->set('id', $post->getId());
            $session->set('file', $_FILES);

            if(isset($_FILES['post_form'])) {
                move_uploaded_file($_FILES['post_form']['tmp_name']['name'], "uploads/" . $_FILES['post_form']['name']['name']);
            }

            return $this->redirect($this->generateUrl('save_post'));
        }

        $photo = $this->getDoctrine()->getRepository(Photos::class)->findPhotoByPostId($post->getId());

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
