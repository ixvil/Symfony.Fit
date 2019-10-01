<?php

namespace App\Repository;

use App\Entity\SalaryByMonth;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method SalaryByMonth|null find($id, $lockMode = null, $lockVersion = null)
 * @method SalaryByMonth|null findOneBy(array $criteria, array $orderBy = null)
 * @method SalaryByMonth[]    findAll()
 * @method SalaryByMonth[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SalaryByMonthRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, SalaryByMonth::class);
    }

//    /**
//     * @return SalaryByMonth[] Returns an array of SalaryByMonth objects
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
    public function findOneBySomeField($value): ?SalaryByMonth
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
