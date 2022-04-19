<?php

declare(strict_types=1);

namespace Future\Blog\User\PostExport;

class PostExport
{
    private int $userId;

    private ?array $status;

    private ?array $category;

    private ?\DateTime $dateFrom;

    private ?\DateTime $dateTo;

    public function __construct(
        ?int $userId,
        ?array $status,
        ?array $category,
        ?\DateTime $dateFrom,
        ?\DateTime $dateTo
    ) {
        $this->userId = $userId;
        $this->status = $status;
        $this->category = $category;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    public function getStatus(): ?array
    {
        return $this->status;
    }

    public function setStatus(?array $status): void
    {
        $this->status = $status;
    }

    public function getCategory(): ?array
    {
        return $this->category;
    }

    public function setCategory(?array $category): void
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
