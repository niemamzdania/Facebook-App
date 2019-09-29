<?php

namespace App\Service;

use App\Entity\Photos;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;

class PostsService
{
    public function saveNewPost($entityManager, $post, $directory, $tempDirectory, $request)
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
            $directory = $directory . "/" . $dateInString;

            $fileName = md5(time());
            $fileName = substr($fileName, 0, 32);

            $extension = $file->guessExtension();
            $fileName = $fileName . "." . $extension;

            $file->move($directory, $fileName);

            $photo->setName($fileName);
            $photo->setPost($post);

            $entityManager->persist($photo);
            $entityManager->flush();
        }
    }

    public function saveEditedPost($entityManager, $post, $photo, $directory, $tempDirectory, $request)
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

        $fileName = md5(time());
        $fileName = substr($fileName, 0, 32);

        if(!$photo && isset($file))
        {
            $photo = new Photos();

            $directory = $directory . "/" . $currentDateInString;

            $extension = $file->guessExtension();
            $fileName = $fileName . "." . $extension;

            $file->move($directory, $fileName);

            $photo->setName($fileName);
            $photo->setPost($post);

            $entityManager->persist($photo);
            $entityManager->flush();
        }
        elseif($photo && isset($file))
        {
            $oldDirectory = $directory . "/" . $dateInString;

            $finder = new Finder();
            $finder->files()->in($oldDirectory)->name($photo->getName());

            foreach ($finder as $currentPhoto) {
                $fileSystem = new Filesystem();

                $fileSystem->remove([$currentPhoto->getPathname()]);
            }

            $extension = $file->guessExtension();
            $fileName = $fileName . "." . $extension;

            $directory = $directory . "/" . $currentDateInString;

            $file->move($directory, $fileName);

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