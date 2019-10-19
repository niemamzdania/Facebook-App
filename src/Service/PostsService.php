<?php

namespace App\Service;

use App\Entity\Photos;
use Cloudinary\Search;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;

class PostsService
{
    public function saveNewPost($entityManager, $post, $tempDirectory, $request)
    {
        $session = $request->getSession();

        if($session->get('file')['post_form']['name']['name'] != NULL) {
            $realPath = $tempDirectory . '/' . $session->get('file')['post_form']['name']['name'];
            $file = new File($realPath);
        }

        $date = new \DateTime();

        $data = $session->get('data');

        $post->setTitle($data['Title']);
        $post->setContent($data['Content']);
        $post->setDate($date);

        $entityManager->persist($post);
        $entityManager->flush();

        if (isset($file)) {
            $photo = new Photos();

            $dateInString = $date->format('Y-m');

            //Config of server
            \Cloudinary::config(array(
                "cloud_name" => "przemke",
                "api_key" => "884987643496832",
                "api_secret" => "9KWlEeWnpdqZyo2GlohdLAqibeU",
                "secure" => true
            ));

            $upload = \Cloudinary\Uploader::upload($realPath, ['folder' => $dateInString]);

            $fileName = substr($upload['public_id'], 8);

            $photo->setName($fileName);
            $photo->setPost($post);

            $entityManager->persist($photo);
            $entityManager->flush();
        }
    }

    public function saveEditedPost($entityManager, $post, $photo, $tempDirectory, $request)
    {
        $session = $request->getSession();

        if($session->get('file')['post_form']['name']['name'] != NULL) {
            $realPath = $tempDirectory . '/' . $session->get('file')['post_form']['name']['name'];
            $file = new File($realPath);
        }

        $date = $post->getDate();
        $dateInString = $date->format('Y-m');
        $currentDate = new \DateTime();
        $currentDateInString = $currentDate->format('Y-m');

        //Config of server
        \Cloudinary::config(array(
            "cloud_name" => "przemke",
            "api_key" => "884987643496832",
            "api_secret" => "9KWlEeWnpdqZyo2GlohdLAqibeU",
            "secure" => true
        ));

        /*
        $fileName = md5(time());
        $fileName = substr($fileName, 0, 32);
        */

        //New image to post
        if(!$photo && isset($file))
        {
            $photo = new Photos();

            $upload = \Cloudinary\Uploader::upload($realPath, ['folder' => $currentDateInString]);

            $fileName = substr($upload['public_id'], 8);

            $photo->setName($fileName);
            $photo->setPost($post);

            $entityManager->persist($photo);
            $entityManager->flush();
        }
        //Change photo in post
        elseif($photo && isset($file))
        {
            $search = new Search();
            $searchData = "folder=".$dateInString." AND filename=".$photo->getName();
            $result = $search
                ->expression($searchData)
                ->max_results(1)
                ->execute();

            \Cloudinary\Uploader::destroy($result['resources'][0]['public_id']);

            $upload = \Cloudinary\Uploader::upload($realPath, ['folder' => $currentDateInString]);

            $fileName = substr($upload['public_id'], 8);

            $photo->setName($fileName);

            $entityManager->flush();
        }

        $data = $session->get('data');

        $post->setTitle($data['Title']);
        $post->setContent($data['Content']);
        $post->setDate($currentDate);

        $entityManager->flush();
    }
}