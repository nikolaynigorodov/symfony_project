<?php

declare(strict_types=1);

namespace Future\Blog\Post\Dto;

class PostLikeAjaxDto
{
    private bool $likeStatus;

    private ?int $likeCount;

    public function __construct(bool $likeStatus, int $likeCount)
    {
        $this->likeStatus = $likeStatus;
        $this->likeCount = $likeCount;
    }

    public function getLikeStatus(): bool
    {
        return $this->likeStatus;
    }

    public function getLikeCount(): ?int
    {
        return $this->likeCount;
    }
}
