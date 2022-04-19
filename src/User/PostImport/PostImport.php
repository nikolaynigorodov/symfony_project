<?php

declare(strict_types=1);

namespace Future\Blog\User\PostImport;

class PostImport
{
    private int $userId;

    private ?string $status;

    private ?string $fileName;

    private ?string $uniqueFileNameAndToken;

    public function __construct(int $userId, ?string $status, ?string $fileName, ?string $uniqueFileNameAndToken)
    {
        $this->userId = $userId;
        $this->status = $status;
        $this->fileName = $fileName;
        $this->uniqueFileNameAndToken = $uniqueFileNameAndToken;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): void
    {
        $this->status = $status;
    }

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function setFileName(?string $fileName): void
    {
        $this->fileName = $fileName;
    }

    public function getUniqueFileNameAndToken(): ?string
    {
        return $this->uniqueFileNameAndToken;
    }

    public function setUniqueFileNameAndToken(?string $uniqueFileNameAndToken): void
    {
        $this->uniqueFileNameAndToken = $uniqueFileNameAndToken;
    }
}
