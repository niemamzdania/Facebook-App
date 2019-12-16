<?php

namespace App\Service;

use App\Entity\Projects;

class QuestsService
{
    public function saveNewQuest($entityManager, $quest, $project, $request)
    {
        $dateInString = $request->request->get('EndDate');

        $date = new \DateTime($dateInString);

        $quest->setAddDate(new \DateTime());
        $quest->setEndDate($date);
        $quest->setContent($request->request->get('Content'));
        $quest->setStatus(0);

        $quest->setProject($project);

        $entityManager->persist($quest);
        $entityManager->flush();
    }

    public function saveEditedQuest($entityManager, $quest, $request)
    {
        $quest->setStatus($request->request->get('Status') + 0);

        $dateInString = $request->request->get('EndDate');
        $date = new \DateTime($dateInString);

        if($request->request->get('EndDate')) {
            $quest->setContent($request->request->get('Content'));
            $quest->setEndDate($date);
        }

        $entityManager->flush();
    }
}