<?php

namespace App\Service;

use App\Entity\Photos;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;

class PostsService
{
    public function saveNewPost($entityManager, $post, $directory, $request)
    {
        $date = new \DateTime();

        $data = $request->request->get('post_form');

        $post->setTitle($data['Title']);
        $post->setContent($data['Content']);
        $post->setDate($date);

        $entityManager->persist($post);
        $entityManager->flush();

        $file = $request->files->get('post_form')['name'];

        if ($file) {
            $photo = new Photos();

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

    public function saveEditedPost($entityManager, $post, $photo, $directory, $request)
    {
        $date = $post->getDate();
        $dateInString = $date->format('Y-m');
        $currentDate = new \DateTime();
        $currentDateInString = $currentDate->format('Y-m');

        $fileName = md5(time());
        $fileName = substr($fileName, 0, 32);

        $file = $request->files->get('post_form')['name'];

        if(!$photo && $file)
        {
            $photo = new Photos();

            $directory = $directory . "/" . $currentDateInString;

            $extension = $request->files->get('post_form')['name']->guessExtension();
            $fileName = $fileName . "." . $extension;

            $file->move($directory, $fileName);

            $photo->setName($fileName);
            $photo->setPost($post);

            $entityManager->persist($photo);
            $entityManager->flush();
        }
        elseif($photo && $file)
        {
            $oldDirectory = $directory . "/" . $dateInString;

            $finder = new Finder();
            $finder->files()->in($oldDirectory)->name($photo->getName());

            foreach ($finder as $currentPhoto) {
                $fileSystem = new Filesystem();

                $fileSystem->remove([$currentPhoto->getPathname()]);
            }

            $extension = $request->files->get('post_form')['name']->guessExtension();
            $fileName = $fileName . "." . $extension;

            $directory = $directory . "/" . $currentDateInString;

            $file->move($directory, $fileName);

            $photo->setName($fileName);

            $entityManager->flush();
        }

        $post->setTitle($request->request->get('post_form')['Title']);
        $post->setContent($request->request->get('post_form')['Content']);
        $post->setDate($currentDate);

        $entityManager->flush();
    }
}