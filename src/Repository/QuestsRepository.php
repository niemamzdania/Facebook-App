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

    public function findByUserId($user_id)
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.user = :val')
            ->setParameter('val', $user_id)
            ->orderBy('q.endDate', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findQuestById($id): ?Quests
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.id = :val')
            ->setParameter('val', $id)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    public function findAllQuests()
    {
        return $this->createQueryBuilder('q')
            ->orderBy('q.endDate', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findQuestsByProjectId($project_id): array
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.project = :val')
            ->setParameter('val', $project_id)
            ->getQuery()
            ->getResult()
            ;
    }
}
