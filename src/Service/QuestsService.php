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

    public function saveEditedQuest($entityManager, $quest, $data)
    {
        $date = new \DateTime($data['EndDate']);

        $quest->setAddDate(new \DateTime());
        $quest->setEndDate($date);
        $quest->setContent($data['Content']);
        $quest->setStatus($data['Status']);

        $entityManager->flush();
    }
}