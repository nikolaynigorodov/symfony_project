<?php

declare(strict_types=1);

namespace Future\Blog\Post\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Future\Blog\Core\Entity\User;
use Future\Blog\Post\Entity\Post;
use Future\Blog\Post\Entity\PostLikes;
use Future\Blog\Post\Repository\PostLikesRepository;

class PostLikesManager
{
    private EntityManagerInterface $entityManager;

    private PostLikesRepository $postLikesRepository;

    public function __construct(EntityManagerInterface $entityManager, PostLikesRepository $postLikesRepository)
    {
        $this->entityManager = $entityManager;
        $this->postLikesRepository = $postLikesRepository;
    }

    public function checkPostLikes(?User $user, Post $post): ?PostLikes
    {
        if (!$user) {
            return null;
        }

        return $this->postLikesRepository->findLikesByUser($user, $post);
    }

    public function savePostLikes(User $user, Post $post): void
    {
        $postLikes = new PostLikes($user, $post);
        $this->entityManager->persist($postLikes);
        $this->entityManager->flush();
    }

    public function deletePostLikes(PostLikes $postLikes): void
    {
        $this->entityManager->remove($postLikes);
        $this->entityManager->flush();
    }

    public function findAllLikes(Post $post): ?string
    {
        return $this->postLikesRepository->findAllLikesForPost($post);
    }
}
