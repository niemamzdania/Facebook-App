<?php

namespace App\Repository;

use App\Entity\Quests;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Quests|null find($id, $lockMode = null, $lockVersion = null)
 * @method Quests|null findOneBy(array $criteria, array $orderBy = null)
 * @method Quests[]    findAll()
 * @method Quests[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuestsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Quests::class);
    }

    // /**
    //  * @return Quests[] Returns an array of Quests objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('q.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Quests
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
