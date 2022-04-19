<?php

declare(strict_types=1);

namespace Future\Blog\Post\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Future\Blog\Core\Entity\User;
use Future\Blog\Post\Entity\Post;
use Future\Blog\User\Entity\Subscription;
use Future\Blog\User\PostExport\PostExport;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function findAllQuery(): Query
    {
        return $this->createQueryBuilder('p')
            ->where('p.status = :status')
            ->setParameter('status', Post::POST_STATUS_PUBLISHED)
            ->orderBy('p.id', 'DESC')
            ->getQuery()
        ;
    }

    public function findPostsByUserWithStatus(User $user, array $status): Query
    {
        return $this->createQueryBuilder('p')
            ->where('p.owner = :p_owner')
            ->setParameter('p_owner', $user)
            ->andWhere('p.status IN (:status)')
            ->setParameter('status', $status)
            ->orderBy('p.id', 'DESC')
            ->getQuery()
        ;
    }

    public function findPostsByUserStatusPublished(User $user, string $status = Post::POST_STATUS_PUBLISHED): Query
    {
        return $this->createQueryBuilder('p')
            ->where('p.owner = :p_owner')
            ->setParameter('p_owner', $user)
            ->andWhere('p.status = :status')
            ->setParameter('status', $status)
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ;
    }

    public function findPostByTitleOrSummary(string $searchText): Query
    {
        return $this->createQueryBuilder('p')
            ->where('p.title LIKE :p_title')
            ->orWhere('p.summary LIKE :p_summary')
            ->setParameter('p_title', "%{$searchText}%")
            ->setParameter('p_summary', "%{$searchText}%")
            ->andWhere('p.status = :status')
            ->setParameter('status', Post::POST_STATUS_PUBLISHED)
            ->getQuery()
        ;
    }

    public function findByCategoryOrderedByIdQuery($categoryId): Query
    {
        return $this->createQueryBuilder('p')
            ->where('p.category = :p_category')
            ->setParameter('p_category', $categoryId)
            ->orderBy('p.id', 'DESC')
            ->getQuery()
        ;
    }

    public function findPostsForMonth(User $user, \DateTime $firstDayMonth, \DateTime $lastDayMonth): ?string
    {
        return $this->createQueryBuilder('p')
            ->select('count(p.id)')
            ->where('p.owner = :p_owner')
            ->setParameter('p_owner', $user)
            ->andWhere('p.createdAt BETWEEN :first AND :last')
            ->setParameter('first', $firstDayMonth, Types::DATETIME_MUTABLE)
            ->setParameter('last', $lastDayMonth, Types::DATETIME_MUTABLE)
            ->andWhere('p.status = :status')
            ->setParameter('status', Post::POST_STATUS_PUBLISHED)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function findByTagQuery($tagId): Query
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.tags', 't')
            ->where('t.id = :tag_id')
            ->setParameter('tag_id', $tagId)
            ->orderBy('p.id', 'DESC')
            ->getQuery()
        ;
    }

    public function findBySubscription(Subscription $subscription, string $status): ?array
    {
        return $this->createQueryBuilder('p')
            ->where('p.category IN (:categories)')
            ->setParameter('categories', $subscription->getCategory())
            ->andWhere('p.createdAt >= :created_at')
            ->setParameter('created_at', $subscription->getUpdatedAt())
            ->andWhere('p.status = :status')
            ->setParameter('status', $status)
            ->andWhere('p.owner != :owner')
            ->setParameter('owner', $subscription->getOwner())
            ->orderBy('p.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findByStatusDelayed(\DateTime $nowDateTime): ?array
    {
        return $this->createQueryBuilder('p')
            ->where('p.status = :status')
            ->setParameter('status', Post::POST_STATUS_DELAYED)
            ->andWhere('p.publishingDate <= :nowDateTime')
            ->setParameter('nowDateTime', $nowDateTime)
            ->orderBy('p.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findPostForExport(PostExport $postExport): Query
    {
        $qb = $this->createQueryBuilder('p')
            ->where('p.owner = :p_owner')
            ->setParameter('p_owner', $postExport->getUserId())
            ->orderBy('p.id', 'ASC')
        ;

        if ($postExport->getStatus()) {
            $qb->andWhere('p.status IN (:status)')
                ->setParameter('status', $postExport->getStatus())
            ;
        }

        if ($postExport->getCategory()) {
            $qb->andWhere('p.category IN (:categories)')
                ->setParameter('categories', $postExport->getCategory())
            ;
        }

        if ($postExport->getDateFrom() && $postExport->getDateTo()) {
            $qb->andWhere('p.createdAt BETWEEN :first AND :last')
                ->setParameter('first', $postExport->getDateFrom(), Types::DATETIME_MUTABLE)
                ->setParameter('last', $postExport->getDateTo(), Types::DATETIME_MUTABLE)
            ;
        } elseif ($postExport->getDateFrom()) {
            $nowDateTime = new \DateTime('now');
            $qb->andWhere('p.createdAt BETWEEN :first AND :last')
                ->setParameter('first', $postExport->getDateFrom(), Types::DATETIME_MUTABLE)
                ->setParameter('last', $nowDateTime, Types::DATETIME_MUTABLE)
            ;
        }

        return $qb->getQuery();
    }

    // /**
    //  * @return Post[] Returns an array of Post objects
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
    public function findOneBySomeField($value): ?Post
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
