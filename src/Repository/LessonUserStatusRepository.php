<?php

namespace App\Repository;

use App\Entity\LessonUserStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method LessonUserStatus|null find($id, $lockMode = null, $lockVersion = null)
 * @method LessonUserStatus|null findOneBy(array $criteria, array $orderBy = null)
 * @method LessonUserStatus[]    findAll()
 * @method LessonUserStatus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LessonUserStatusRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, LessonUserStatus::class);
    }

//    /**
//     * @return LessonUserStatus[] Returns an array of LessonUserStatus objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?LessonUserStatus
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
