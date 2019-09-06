<?php

namespace App\Service;
use App\Entity\Users;
use App\Repository\UsersRepository;

class QuestsService
{
    public function __construct(UsersRepository $UsersRepository)
    {
        $this->UsersRepository = $UsersRepository;
    }
     
    public function saveNewQuest($entityManager, $quest, $request)
    {
        $dateInString = $request->request->get('EndDate');

        $date = new \DateTime($dateInString);

        $quest->setAddDate(new \DateTime());
        $quest->setEndDate($date);
        $quest->setContent($request->request->get('Content'));
        $quest->setStatus(0);

        $entityManager->persist($quest);
        $entityManager->flush();
    }

    public function saveEditedQuest($entityManager, $quest, $request)
    {
        $quest->setAddDate(new \DateTime());
        $quest->setStatus($request->request->get('Status'));
        dd($request);

        $dateInString = $request->request->get('EndDate');
        $date = new \DateTime($dateInString);
        $userInInt = $request->request->get('user') + 0;
        $user = new Users();
        $user = $this->UsersRepository->findUserById($userInInt);

        if($request->request->get('EndDate')) {
            $quest->setContent($request->request->get('Content'));
            $quest->setEndDate($date);
            $quest->setUser($user);
        }

        $entityManager->flush();
    }
}