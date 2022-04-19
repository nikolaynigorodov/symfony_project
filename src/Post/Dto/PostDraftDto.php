<?php

declare(strict_types=1);

namespace Future\Blog\Post\Dto;

use Future\Blog\Post\Entity\Category;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

class PostDraftDto
{
    /**
     * @Assert\NotBlank()
     * @Assert\Length(
     *     max=255,
     *     maxMessage="post.max.title"
     * )
     */
    private ?string $title;

    private ?string $summary = null;

    private ?string $content = null;

    private ?string $viewImage = null;

    /**
     * @Assert\Image(
     *     maxHeight=400,
     *     maxHeightMessage="post.edit.image_height",
     *     maxWidth=400,
     *     maxWidthMessage="post.edit.image_width",
     * )
     * @Assert\File(
     *     maxSize="6m",
     *     maxSizeMessage="post.edit.image_size",
     *     mimeTypes={"image/jpeg", "image/jpg", "image/png"},
     *     mimeTypesMessage="post.edit.image_type"
     * )
     */
    private ?File $imageFile = null;

    private ?Category $category = null;

    private ?array $tags = null;

    private ?string $status;

    private bool $mistakeImageImportUrl = false;

    /**
     * @Assert\GreaterThan("+1 hours")
     */
    private ?\DateTime $publishingDate;

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSummary(): ?string
    {
        return $this->summary;
    }

    public function setSummary(?string $summary): self
    {
        $this->summary = $summary;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getViewImage(): ?string
    {
        return $this->viewImage;
    }

    public function setViewImage(?string $viewImage): void
    {
        $this->viewImage = $viewImage;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageFile(?File $imageFile): void
    {
        $this->imageFile = $imageFile;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): void
    {
        $this->category = $category;
    }

    public function getTags(): ?array
    {
        return $this->tags;
    }

    public function setTags(?array $tags): void
    {
        $this->tags = $tags;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): void
    {
        $this->status = $status;
    }

    public function isMistakeImageImportUrl(): bool
    {
        return $this->mistakeImageImportUrl;
    }

    public function setMistakeImageImportUrl(bool $mistakeImageImportUrl): void
    {
        $this->mistakeImageImportUrl = $mistakeImageImportUrl;
    }

    public function getPublishingDate(): ?\DateTime
    {
        return $this->publishingDate;
    }

    public function setPublishingDate(?\DateTime $publishingDate): void
    {
        $this->publishingDate = $publishingDate;
    }

    public function getCreatePostTimeNow(string $stringNameTime = 'now'): ?\DateTime
    {
        return new \DateTime($stringNameTime);
    }
}
