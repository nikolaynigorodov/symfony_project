<?php

declare(strict_types=1);

namespace Future\Blog\Post\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class PostSearchDto
{
    /**
     * @Assert\NotBlank()
     */
    private ?string $title;

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }
}
