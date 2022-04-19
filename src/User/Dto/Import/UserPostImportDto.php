<?php

declare(strict_types=1);

namespace Future\Blog\User\Dto\Import;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

class UserPostImportDto
{
    /**
     * @Assert\NotBlank()
     */
    private ?string $status;

    /**
     * @Assert\File(
     *     maxSize="4m",
     *     maxSizeMessage="uuser.post.import.image_size",
     *     mimeTypes={"text/csv", "text/plain"},
     *     mimeTypesMessage="user.post.import.import_type"
     * )
     */
    private ?File $importFile = null;

    private ?string $importFileName = null;

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): void
    {
        $this->status = $status;
    }

    public function getImportFile(): ?File
    {
        return $this->importFile;
    }

    public function setImportFile(?File $importFile): void
    {
        $this->importFile = $importFile;
    }

    public function getImportFileName(): ?string
    {
        return $this->importFileName;
    }

    public function setImportFileName(?string $importFileName): void
    {
        $this->importFileName = $importFileName;
    }
}
