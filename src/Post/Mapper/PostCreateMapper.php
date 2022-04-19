<?php

declare(strict_types=1);

namespace Future\Blog\Post\Mapper;

use Future\Blog\Core\Entity\User;
use Future\Blog\Post\Entity\Post;

class PostCreateMapper
{
    public function setPost($postDto, User $user, string $status): Post
    {
        return new Post($postDto->getTitle(), $postDto->getSummary(), $postDto->getContent(), $user, $postDto->getCategory(), $status);
    }
}
