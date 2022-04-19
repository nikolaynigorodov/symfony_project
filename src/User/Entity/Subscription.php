<?php

declare(strict_types=1);

namespace Future\Blog\User\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Future\Blog\Common\Entity\Traits\IdentifiableEntityTrait;
use Future\Blog\Common\Entity\Traits\TimestampableEntityTrait;
use Future\Blog\Core\Entity\User;
use Future\Blog\Post\Entity\Category;
use Future\Blog\User\Repository\SubscriptionRepository;

/**
 * @ORM\Entity(repositoryClass=SubscriptionRepository::class)
 */
class Subscription
{
    use IdentifiableEntityTrait;
    use TimestampableEntityTrait;

    /**
     * @ORM\ManyToMany(targetEntity=Category::class)
     * @var Category[]|Collection
     */
    private Collection $categories;

    /**
     * @ORM\OneToOne(targetEntity=User::class, inversedBy="subscription")
     */
    private User $owner;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return Category[]|Collection
     */
    public function getCategory(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        $this->categories->removeElement($category);

        return $this;
    }
}
