<?php

declare(strict_types=1);

namespace Future\Blog\User\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class PasswordResetGetPasswordDto
{
    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=4096)
     */
    private ?string $password;

    /**
     * @return string
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword($password): void
    {
        $this->password = $password;
    }
}
