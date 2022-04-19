<?php

declare(strict_types=1);

namespace Future\Blog\User\Dto;

use Doctrine\Common\Collections\ArrayCollection;
use Future\Blog\Post\Entity\Category;

class SubscriptionDto
{
    /**
     * @var ArrayCollection|Category[]
     */
    private ArrayCollection $category;

    /**
     * @return ArrayCollection
     */
    public function getCategory(): ?ArrayCollection
    {
        return $this->category;
    }

    public function setCategory($category): self
    {
        $this->category = $category;

        return $this;
    }
}
