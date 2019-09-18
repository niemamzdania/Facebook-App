<?php

namespace App\DataFixtures;

use App\Entity\Conversations;
use App\Entity\Users;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\Persistence\ObjectManager;

class ConversationsFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager)
    {
        $users = $manager->getRepository(Users::class)->findAllUsers();

        for($i = 0; $i < count($users)-1; $i++)
        {
            for($j = 1; $j < count($users); $j++)
            {
                if($i >= $j)
                    continue;
                $conversation = new Conversations();
                $conversation->setUser1($users[$i]);
                $conversation->setUser2($users[$j]);
                $manager->persist($conversation);
            }
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['group2'];
    }
}
