<?php

declare(strict_types=1);

namespace Future\Blog\User\Dto;

use Future\Blog\User\Validator\Constraints as MyConstraintsValidator;
use Symfony\Component\Validator\Constraints as Assert;

class UserRegistration
{
    protected bool $activated = true;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=100)
     */
    private ?string $firstName;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=100)
     */
    private ?string $lastName;

    /**
     * @Assert\NotBlank()
     * @Assert\Email()
     * @MyConstraintsValidator\RegistrationEmail
     */
    private ?string $email;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=4096)
     */
    private ?string $plainPassword;

    /**
     * @return string
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName): void
    {
        $this->lastName = $lastName;
    }

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

    /**
     * @return string
     */
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    /**
     * @param string $plainPassword
     */
    public function setPlainPassword($plainPassword): void
    {
        $this->plainPassword = $plainPassword;
    }

    public function isActivated(): bool
    {
        return $this->activated;
    }

    public function setActivated(bool $activated): void
    {
        $this->activated = $activated;
    }

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
