<?php

namespace App\Service;

use App\Entity\Posts;

class PostsService
{
    public function setDateToPost($entityManager, $post)
    {
        $post->setDate(new \DateTime());

        $entityManager->persist($post);
        $entityManager->flush();
    }
}