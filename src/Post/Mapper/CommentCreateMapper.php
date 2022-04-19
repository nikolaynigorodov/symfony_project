<?php

declare(strict_types=1);

namespace Future\Blog\Post\Mapper;

use Future\Blog\Core\Entity\User;
use Future\Blog\Post\Dto\CommentCreateDto;
use Future\Blog\Post\Entity\Comment;
use Future\Blog\Post\Entity\Post;

class CommentCreateMapper
{
    public function setDtoToPost(CommentCreateDto $commentCreateDto, User $user, Post $post): Comment
    {
        return new Comment($commentCreateDto->getMessage(), $user, $post, Comment::PENDING);
    }
}
