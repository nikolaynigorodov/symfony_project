<?php

declare(strict_types=1);

namespace Future\Blog\Post\Mapper;

use Future\Blog\Post\Dto\PostDraftDto;
use Future\Blog\Post\Dto\PostDto;
use Future\Blog\Post\Entity\Post;

class PostEditMapper
{
    public function setPostDto(Post $post, PostDto $dto): PostDto
    {
        $postEdit = $dto;
        $postEdit->setTitle($post->getTitle());
        $postEdit->setSummary($post->getSummary());
        $postEdit->setContent($post->getContent());
        $postEdit->setCategory($post->getCategory());
        $postEdit->setTags($this->arrayTags($post));
        $postEdit->setStatus($post->getStatus());
        if ($post->getStatus() === Post::POST_STATUS_DELAYED) {
            $postEdit->setPublishingDate($post->getPublishingDate());
        }

        if ($post->getImage()) {
            $postEdit->setViewImage($post->getImage());
        }

        return $postEdit;
    }

    public function setPostDraftDto(Post $post, PostDraftDto $dto): PostDraftDto
    {
        $postEdit = $dto;
        $postEdit->setTitle($post->getTitle());
        $postEdit->setSummary($post->getSummary());
        $postEdit->setContent($post->getContent());
        $postEdit->setCategory($post->getCategory());
        $postEdit->setTags($this->arrayTags($post));
        $postEdit->setStatus($post->getStatus());
        if ($post->getStatus() === Post::POST_STATUS_DELAYED) {
            $postEdit->setPublishingDate($post->getPublishingDate());
        }

        if ($post->getImage()) {
            $postEdit->setViewImage($post->getImage());
        }

        return $postEdit;
    }

    private function arrayTags(Post $post): array
    {
        $array = [];
        if ($post->getTags()) {
            foreach ($post->getTags() as $tag) {
                $array[] = $tag->getTitle();
            }
        }

        return $array;
    }
}
