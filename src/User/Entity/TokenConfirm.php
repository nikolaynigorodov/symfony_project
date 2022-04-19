<?php

declare(strict_types=1);

namespace Future\Blog\User\Entity;

use Doctrine\ORM\Mapping as ORM;
use Future\Blog\Common\Entity\Traits\IdentifiableEntityTrait;
use Future\Blog\Common\Entity\Traits\TimestampableEntityTrait;
use Future\Blog\Core\Entity\User;
use Future\Blog\User\Repository\TokenConfirmRepository;

/**
 * @ORM\Entity(repositoryClass=TokenConfirmRepository::class)
 */
class TokenConfirm
{
    use IdentifiableEntityTrait;
    use TimestampableEntityTrait;

    public const EMAIL_CONFIRM = 1;
    public const PASSWORD_RESET = 2;
    public const EMAIL_RESET = 3;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    private User $user;

    /**
     * @ORM\Column(type="integer")
     */
    private int $type;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $token;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $emailReset;

    public function __construct(User $user, string $token, int $type, string $emailReset = null)
    {
        $this->user = $user;
        $this->token = $token;
        $this->type = $type;
        $this->emailReset = $emailReset;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getEmailReset(): ?string
    {
        return $this->emailReset;
    }

    public function setEmailReset(?string $emailReset): self
    {
        $this->emailReset = $emailReset;

        return $this;
    }
}
