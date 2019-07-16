<?php

namespace App\Service;

use App\Entity\Quests;
use http\Env\Request;

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
}