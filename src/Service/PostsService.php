<?php

namespace App\Service;

class PostsService
{
    public function saveNewPost($entityManager, $post, $photo, $directory, $request)
    {
        $data = $request->request->get('post_form');

        $post->setTitle($data['Title']);
        $post->setContent($data['Content']);
        $post->setDate(new \DateTime());

        $entityManager->persist($post);
        $entityManager->flush();

        $file = $request->files->get('post_form')['name'];

        if($file) {
            $date = new \DateTime();
            $dateInString = $date->format('Y-m');
            $directory = $directory . "/" . $dateInString;

            $fileName = md5(time());
            $fileName = substr($fileName, 0, 32);

            $extension = $request->files->get('post_form')['name']->guessExtension();
            $fileName = $fileName . "." . $extension;

            $file->move($directory, $fileName);

            $photo->setName($fileName);
            $photo->setPost($post);

            $entityManager->persist($photo);
            $entityManager->flush();
        }
    }

    public function saveEditedPost($entityManager, $post, $data)
    {
        $post->setDate(new \DateTime());
        $post->setTitle($data['post_form']['Title']);
        $post->setContent($data['post_form']['Content']);

        $entityManager->flush();
    }
}