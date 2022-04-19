<?php

declare(strict_types=1);

namespace Future\Blog\Stripe\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Future\Blog\Stripe\Entity\SubscriptionPay;

/**
 * @method SubscriptionPay|null find($id, $lockMode = null, $lockVersion = null)
 * @method SubscriptionPay|null findOneBy(array $criteria, array $orderBy = null)
 * @method SubscriptionPay[]    findAll()
 * @method SubscriptionPay[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubscriptionPayRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SubscriptionPay::class);
    }

    // /**
    //  * @return SubscriptionPay[] Returns an array of SubscriptionPay objects
    //  */
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
    public function findOneBySomeField($value): ?SubscriptionPay
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
