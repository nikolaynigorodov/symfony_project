<?php

declare(strict_types=1);

namespace Future\Blog\Core\Entity;

use Doctrine\ORM\Mapping as ORM;
use Future\Blog\Common\Entity\Traits\IdentifiableEntityTrait;
use Future\Blog\Core\Repository\UserRepository;
use Future\Blog\Stripe\Entity\SubscriptionPay;
use Future\Blog\User\Entity\Import;
use Future\Blog\User\Entity\Subscription;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use IdentifiableEntityTrait;

    /**
     * @ORM\Column(type="boolean")
     */
    protected bool $blocked = false;

    /**
     * @ORM\Column(type="boolean")
     */
    protected bool $activated = false;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private string $email;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private string $firstName;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private string $lastName;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private ?string $avatar = null;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private array $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private string $password;

    /**
     * @ORM\OneToOne(targetEntity=SubscriptionPay::class, mappedBy="user")
     */
    private $subscriptionPay;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private ?string $stripeCustomerId;

    /**
     * @ORM\Column(type="boolean")
     */
    private $subscriptionPayCheck = false;

    /**
     * @ORM\OneToOne(targetEntity=Subscription::class, mappedBy="owner")
     */
    private $subscription;

    /**
     * @ORM\OneToOne(targetEntity=Import::class, mappedBy="user")
     */
    private $import;

    public function __construct(string $email, string $pass, string $firstName, string $lastName, array $roles)
    {
        $this->email = $email;
        $this->password = $pass;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->roles = $roles;
    }

    public function __toString()
    {
        return $this->getFullName();
    }

    public function isBlocked(): bool
    {
        return $this->blocked;
    }

    public function setBlocked(bool $blocked): void
    {
        $this->blocked = $blocked;
    }

    public function isActivated(): bool
    {
        return $this->activated;
    }

    public function setActivated(bool $activated): void
    {
        $this->activated = $activated;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    public function setAvatar(string $avatar): void
    {
        $this->avatar = $avatar;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getSubscriptionPay(): ?SubscriptionPay
    {
        return $this->subscriptionPay;
    }

    public function setSubscriptionPay(SubscriptionPay $subscriptionPay): self
    {
        // set the owning side of the relation if necessary
        if ($subscriptionPay->getUser() !== $this) {
            $subscriptionPay->setUser($this);
        }

        $this->subscriptionPay = $subscriptionPay;

        return $this;
    }

    /**
     * @return string
     */
    public function getStripeCustomerId(): ?string
    {
        return $this->stripeCustomerId;
    }

    public function setStripeCustomerId(string $stripeCustomerId): void
    {
        $this->stripeCustomerId = $stripeCustomerId;
    }

    public function getSubscriptionPayCheck(): bool
    {
        return $this->subscriptionPayCheck;
    }

    public function setSubscriptionPayCheck(bool $subscriptionPayCheck): self
    {
        $this->subscriptionPayCheck = $subscriptionPayCheck;

        return $this;
    }

    public function getSubscription(): ?Subscription
    {
        return $this->subscription;
    }

    public function setSubscription(?Subscription $subscription): self
    {
        // unset the owning side of the relation if necessary
        if ($subscription === null && $this->subscription !== null) {
            $this->subscription->setOwner(null);
        }

        // set the owning side of the relation if necessary
        if ($subscription !== null && $subscription->getOwner() !== $this) {
            $subscription->setOwner($this);
        }

        $this->subscription = $subscription;

        return $this;
    }

    public function getImport(): ?Import
    {
        return $this->import;
    }

    public function setImport(Import $import): self
    {
        // set the owning side of the relation if necessary
        if ($import->getUser() !== $this) {
            $import->setUser($this);
        }

        $this->import = $import;

        return $this;
    }

    public function getFullName(): string
    {
        return $this->lastName . ' ' . $this->firstName;
    }

    public function isActive(): bool
    {
        if ($this->isActivated() && $this->isBlocked()) {
            return true;
        }

        return false;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return $this->email;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}
