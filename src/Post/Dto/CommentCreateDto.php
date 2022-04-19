<?php

declare(strict_types=1);

namespace Future\Blog\Post\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class CommentCreateDto
{
    /**
     * @Assert\NotBlank()
     * @Assert\Length(
     *     max=500,
     * )
     */
    private ?string $message;

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }
}
