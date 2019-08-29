<?php

namespace App\Service;

class QuestsService
{
    public function saveNewQuest($entityManager, $quest, $request)
    {
        $dateInString = $request->request->get('EndDate');

        $date = new \DateTime($dateInString);

        $quest->setAddDate(new \DateTime());
        $quest->setEndDate($date);
        $quest->setContent($request->request->get('Content'));
        $quest->setStatus($request->request->get('Status'));

        $entityManager->persist($quest);
        $entityManager->flush();
    }

    public function saveEditedQuest($entityManager, $quest, $user, $request)
    {
        $quest->setAddDate(new \DateTime());
        $quest->setStatus($request->request->get('Status'));
        dd($user->se);

        if($user->getRoles() == ["ROLE_ADMIN"])
        {
            dd('asdasd');
            $dateInString = $request->request->get('EndDate');
            $date = new \DateTime($dateInString);

            $quest->setEndDate($date);
            $quest->setContent($request->request->get('Content'));
        }

        $entityManager->flush();
    }
}