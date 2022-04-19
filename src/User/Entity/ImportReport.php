<?php

declare(strict_types=1);

namespace Future\Blog\User\Entity;

use Doctrine\ORM\Mapping as ORM;
use Future\Blog\Common\Entity\Traits\IdentifiableEntityTrait;
use Future\Blog\User\Repository\ImportReportRepository;

/**
 * @ORM\Entity(repositoryClass=ImportReportRepository::class)
 */
class ImportReport
{
    use IdentifiableEntityTrait;

    /**
     * @ORM\Column(type="integer")
     */
    private $rowFile;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $property;

    /**
     * @ORM\Column(type="string", length=500)
     */
    private $message;

    /**
     * @ORM\ManyToOne(targetEntity=Import::class)
     * @ORM\JoinColumn(name="import_id", referencedColumnName="id", nullable=false)
     */
    private $import;

    public function __construct(Import $import, int $rowFile, string $property, string $message)
    {
        $this->rowFile = $rowFile;
        $this->property = $property;
        $this->message = $message;
        $this->import = $import;
    }

    public function getRowFile(): ?int
    {
        return $this->rowFile;
    }

    public function setRowFile(int $rowFile): self
    {
        $this->rowFile = $rowFile;

        return $this;
    }

    public function getProperty(): ?string
    {
        return $this->property;
    }

    public function setProperty(string $property): self
    {
        $this->property = $property;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getImport(): ?Import
    {
        return $this->import;
    }

    public function setImport(?Import $import): self
    {
        $this->import = $import;

        return $this;
    }
}
