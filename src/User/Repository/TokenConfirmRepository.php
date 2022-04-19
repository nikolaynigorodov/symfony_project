<?php

declare(strict_types=1);

namespace Future\Blog\User\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Future\Blog\User\Entity\TokenConfirm;

/**
 * @method TokenConfirm|null find($id, $lockMode = null, $lockVersion = null)
 * @method TokenConfirm|null findOneBy(array $criteria, array $orderBy = null)
 * @method TokenConfirm[]    findAll()
 * @method TokenConfirm[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TokenConfirmRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TokenConfirm::class);
    }

    public function findOneByTypeAndHash(int $type, string $token): ?TokenConfirm
    {
        return $this->findOneBy(['type' => $type, 'token' => $token]);
    }

    public function findByDateTime(\DateTime $dateTime): ?array
    {
        return $this->createQueryBuilder('t')
            ->where('t.createdAt <= :dateTime')
            ->setParameter('dateTime', $dateTime)
            ->orderBy('t.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    // /**
    //  * @return TokenConfirm[] Returns an array of TokenConfirm objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TokenConfirm
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
