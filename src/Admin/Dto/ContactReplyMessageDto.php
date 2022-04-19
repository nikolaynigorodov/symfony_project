<?php

declare(strict_types=1);

namespace Future\Blog\Admin\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class ContactReplyMessageDto
{
    /**
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private ?string $email;

    /**
     * @Assert\NotBlank()
     */
    private ?string $message;

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): self
    {
        $this->message = $message;

        return $this;
    }
}
