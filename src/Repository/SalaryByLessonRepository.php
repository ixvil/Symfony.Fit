<?php

namespace App\Repository;

use App\Entity\SalaryByLesson;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method SalaryByLesson|null find($id, $lockMode = null, $lockVersion = null)
 * @method SalaryByLesson|null findOneBy(array $criteria, array $orderBy = null)
 * @method SalaryByLesson[]    findAll()
 * @method SalaryByLesson[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SalaryByLessonRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, SalaryByLesson::class);
    }

//    /**
//     * @return SalaryByLesson[] Returns an array of SalaryByLesson objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SalaryByLesson
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
