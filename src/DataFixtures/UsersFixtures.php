<?php

namespace App\DataFixtures;

use App\Entity\Users;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UsersFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $admin = new Users();
        $admin->setLogin('admin');
        $admin->setRoles(["ROLE_ADMIN"]);
        $admin->setPassword(
            $this->passwordEncoder->encodePassword($admin, 'qwerty7')
        );
        $admin->setEmail('admin@admin.net');
        $manager->persist($admin);

        $user = new Users();
        $user->setLogin('przemke');
        $user->setRoles(["ROLE_USER"]);
        $user->setPassword(
            $this->passwordEncoder->encodePassword($user, 'qwerty')
        );
        $user->setEmail('przemke@przemke.net');
        $manager->persist($user);

        $manager->flush();
    }
}
