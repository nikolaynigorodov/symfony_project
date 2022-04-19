<?php

declare(strict_types=1);

namespace Future\Blog\Stripe\Entity;

use Doctrine\ORM\Mapping as ORM;
use Future\Blog\Common\Entity\Traits\IdentifiableEntityTrait;
use Future\Blog\Core\Entity\User;
use Future\Blog\Stripe\Repository\SubscriptionPayRepository;

/**
 * @ORM\Entity(repositoryClass=SubscriptionPayRepository::class)
 */
class SubscriptionPay
{
    use IdentifiableEntityTrait;

    /**
     * @ORM\OneToOne(targetEntity=User::class, inversedBy="subscriptionPay")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="datetime")
     */
    private $start;

    /**
     * @ORM\Column(type="datetime")
     */
    private $finish;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $stripeSubscriptionId;

    public function __construct(User $user, string $stripeSubscriptionId, \DateTime $start, \DateTime $finish)
    {
        $this->user = $user;
        $this->stripeSubscriptionId = $stripeSubscriptionId;
        $this->start = $start;
        $this->finish = $finish;
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

    public function getStart(): ?\DateTimeInterface
    {
        return $this->start;
    }

    public function setStart(\DateTimeInterface $start): self
    {
        $this->start = $start;

        return $this;
    }

    public function getFinish(): ?\DateTimeInterface
    {
        return $this->finish;
    }

    public function setFinish(\DateTimeInterface $finish): self
    {
        $this->finish = $finish;

        return $this;
    }

    public function getStripeSubscriptionId(): ?string
    {
        return $this->stripeSubscriptionId;
    }

    public function setStripeSubscriptionId(string $stripeSubscriptionId): self
    {
        $this->stripeSubscriptionId = $stripeSubscriptionId;

        return $this;
    }
}
