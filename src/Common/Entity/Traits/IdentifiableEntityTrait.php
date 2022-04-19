<?php

declare(strict_types=1);

namespace Future\Blog\Common\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait IdentifiableEntityTrait
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected ?int $id = null;

    public function getId(): int
    {
        return $this->id;
    }
}
