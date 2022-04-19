<?php

declare(strict_types=1);

namespace Future\Blog\Core\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class ContactDto
{
    /**
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private ?string $email;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(
     *     max=255,
     * )
     */
    private ?string $name;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(
     *     max=1000,
     * )
     */
    private ?string $message;

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

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
