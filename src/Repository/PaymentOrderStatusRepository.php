<?php

namespace App\Repository;

use App\Entity\PaymentOrderStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method PaymentOrderStatus|null find($id, $lockMode = null, $lockVersion = null)
 * @method PaymentOrderStatus|null findOneBy(array $criteria, array $orderBy = null)
 * @method PaymentOrderStatus[]    findAll()
 * @method PaymentOrderStatus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaymentOrderStatusRepository extends ServiceEntityRepository
{
    const STATUS_NEW = 1;
    const STATUS_PAID = 2;
    const STATUS_CANCELED = 3;

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PaymentOrderStatus::class);
    }

//    /**
//     * @return PaymentOrderStatus[] Returns an array of PaymentOrderStatus objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PaymentOrderStatus
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
