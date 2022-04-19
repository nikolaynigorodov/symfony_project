<?php

declare(strict_types=1);

namespace Future\Blog\Post\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Future\Blog\Core\Entity\User;
use Future\Blog\Post\Entity\Post;
use Future\Blog\Post\Entity\PostLikes;

/**
 * @method PostLikes|null find($id, $lockMode = null, $lockVersion = null)
 * @method PostLikes|null findOneBy(array $criteria, array $orderBy = null)
 * @method PostLikes[]    findAll()
 * @method PostLikes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostLikesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PostLikes::class);
    }

    public function findLikesByUser(User $user, Post $post): ?PostLikes
    {
        return $this->createQueryBuilder('pl')
            ->where('pl.user = :user')
            ->setParameter('user', $user->getId())
            ->andWhere('pl.post = :post')
            ->setParameter('post', $post->getId())
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    public function findAllLikesForPost(Post $post): ?string
    {
        return $this->createQueryBuilder('pl')
            ->select('count(pl.id)')
            ->where('pl.post = :post')
            ->setParameter('post', $post->getId())
            ->getQuery()
            ->getSingleScalarResult()
            ;
    }

    // /**
    //  * @return PostLikes[] Returns an array of PostLikes objects
    //  */
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
    public function findOneBySomeField($value): ?PostLikes
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
