<?php

declare(strict_types=1);

namespace Future\Blog\User\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Future\Blog\Common\Entity\Traits\IdentifiableEntityTrait;
use Future\Blog\Core\Entity\User;
use Future\Blog\User\Repository\ImportRepository;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=ImportRepository::class)
 */
class Import
{
    use IdentifiableEntityTrait;

    public const ALLOWED_NAMES_FOR_IMPORT = [
        0 => 'title',
        1 => 'content',
        2 => 'summary',
        3 => 'category',
        4 => 'tags',
        5 => 'photo',
    ];

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    protected ?\DateTime $createdAt = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $token;

    /**
     * @ORM\OneToMany(targetEntity=ImportReport::class, mappedBy="import")
     */
    private $importReports;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $countPostsAll;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $countPostsTrue;

    public function __construct(string $token, User $user)
    {
        $this->token = $token;
        $this->importReports = new ArrayCollection();
        $this->user = $user;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
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

    /**
     * @return Collection|ImportReport[]
     */
    public function getImportReports(): Collection
    {
        return $this->importReports;
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

    public function getCountPostsAll(): ?int
    {
        return $this->countPostsAll;
    }

    public function setCountPostsAll(?int $countPostsAll): self
    {
        $this->countPostsAll = $countPostsAll;

        return $this;
    }

    public function getCountPostsTrue(): ?int
    {
        return $this->countPostsTrue;
    }

    public function setCountPostsTrue(?int $countPostsTrue): self
    {
        $this->countPostsTrue = $countPostsTrue;

        return $this;
    }

    public function addImportReport(ImportReport $importReport): self
    {
        if (!$this->importReports->contains($importReport)) {
            $this->importReports[] = $importReport;
            $importReport->setImport($this);
        }

        return $this;
    }

    public function removeImportReport(ImportReport $importReport): self
    {
        if ($this->importReports->removeElement($importReport)) {
            // set the owning side to null (unless already changed)
            if ($importReport->getImport() === $this) {
                $importReport->setImport(null);
            }
        }

        return $this;
    }
}
