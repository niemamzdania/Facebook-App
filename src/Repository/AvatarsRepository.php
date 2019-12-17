<?php

namespace App\Repository;

use App\Entity\Avatars;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Avatars|null find($id, $lockMode = null, $lockVersion = null)
 * @method Avatars|null findOneBy(array $criteria, array $orderBy = null)
 * @method Avatars[]    findAll()
 * @method Avatars[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AvatarsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Avatars::class);
    }

    // /**
    //  * @return Avatars[] Returns an array of Avatars objects
    //  */

    public function findAvatarById($id)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.id = :val')
            ->setParameter('val', $id)
            ->orderBy('a.id', 'ASC')
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findAvatarByUserId($user_id): ?Avatars
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.user = :val')
            ->setParameter('val', $user_id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findAllAvatars()
    {
        return $this->createQueryBuilder('a')
            ->orderBy('a.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

}
