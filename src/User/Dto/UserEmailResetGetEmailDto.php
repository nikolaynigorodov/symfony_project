<?php

declare(strict_types=1);

namespace Future\Blog\User\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class UserEmailResetGetEmailDto
{
    /**
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private ?string $email;

    /**
     * @return string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email): void
    {
        $this->email = $email;
    }
}
