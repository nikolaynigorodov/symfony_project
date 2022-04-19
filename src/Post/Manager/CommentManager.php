<?php

declare(strict_types=1);

namespace Future\Blog\Post\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Future\Blog\Core\Entity\User;
use Future\Blog\Post\Dto\CommentCreateDto;
use Future\Blog\Post\Entity\Comment;
use Future\Blog\Post\Entity\Post;
use Future\Blog\Post\Mapper\CommentCreateMapper;

class CommentManager
{
    private EntityManagerInterface $entityManager;

    private CommentCreateMapper $commentCreateMapper;

    public function __construct(EntityManagerInterface $entityManager, CommentCreateMapper $commentCreateMapper)
    {
        $this->entityManager = $entityManager;
        $this->commentCreateMapper = $commentCreateMapper;
    }

    public function saveComment(CommentCreateDto $commentDto, User $user, Post $post): Comment
    {
        $comment = $this->commentCreateMapper->setDtoToPost($commentDto, $user, $post);
        $this->entityManager->persist($comment);
        $this->entityManager->flush();

        return $comment;
    }
}
