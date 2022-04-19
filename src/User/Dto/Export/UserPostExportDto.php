<?php

declare(strict_types=1);

namespace Future\Blog\User\Dto\Export;

use Doctrine\Common\Collections\ArrayCollection;
use Future\Blog\Post\Entity\Category;

class UserPostExportDto
{
    private ?array $status;

    /**
     * @var ArrayCollection|Category[]
     */
    private ?ArrayCollection $category;

    private ?\DateTime $dateFrom;

    private ?\DateTime $dateTo;

    public function getStatus(): ?array
    {
        return $this->status;
    }

    public function setStatus(?array $status): void
    {
        $this->status = $status;
    }

    /**
     * @return ArrayCollection|Category[]
     */
    public function getCategory(): ?ArrayCollection
    {
        return $this->category;
    }

    /**
     * @param ArrayCollection|Category[] $category
     */
    public function setCategory($category): void
    {
        $this->category = $category;
    }

    public function getDateFrom(): ?\DateTime
    {
        return $this->dateFrom;
    }

    public function setDateFrom(?\DateTime $dateFrom): void
    {
        $this->dateFrom = $dateFrom;
    }

    public function getDateTo(): ?\DateTime
    {
        return $this->dateTo;
    }

    public function setDateTo(?\DateTime $dateTo): void
    {
        $this->dateTo = $dateTo;
    }
}
