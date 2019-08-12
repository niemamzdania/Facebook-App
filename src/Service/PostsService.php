<?php

namespace App\Service;

class PostsService
{
    public function saveNewPost($entityManager, $post, $data)
    {
        //$aaa = $data[];
        //dd($aaa);

        //dd($data);
        $post->setDate(new \DateTime());
        //$post->setTitle($request->request->get('Title'));
        //$post->setContent($request->request->get('Content'));

        //dd($post);

        $entityManager->persist($post);
        $entityManager->flush();
    }
}