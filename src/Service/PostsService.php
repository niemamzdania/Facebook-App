<?php

namespace App\Service;

class PostsService
{
    public function saveNewPost($entityManager, $post, $data)
    {
        $post->setDate(new \DateTime());
        $post->setTitle($data['add_post_form']['Title']);
        $post->setContent($data['add_post_form']['Content']);

        $entityManager->persist($post);
        $entityManager->flush();
    }

    public function saveEditedPost($entityManager, $post, $data)
    {
        $post->setDate(new \DateTime());
        $post->setTitle($data['add_post_form']['Title']);
        $post->setContent($data['add_post_form']['Content']);
        
        $entityManager->flush();
    }
}