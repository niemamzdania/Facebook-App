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
        $quest->setStatus(0);

        $entityManager->persist($quest);
        $entityManager->flush();
    }

    public function saveEditedQuest($entityManager, $quest, $request)
    {
        $quest->setAddDate(new \DateTime());
        $quest->setStatus($request->request->get('Status'));

        if($request->request->get('EndDate')) {
            $quest->setContent($request->request->get('Content'));
            $quest->setEndDate($request->request->get('EndDate'));
            $quest->setUserId($request->request->get('user'));
        }

        $entityManager->flush();
    }
}