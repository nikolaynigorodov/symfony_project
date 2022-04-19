<?php

declare(strict_types=1);

namespace Future\Blog\Common\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

abstract class BaseFixture extends Fixture
{
    private bool $prependClassNameToReference = false;

    private bool $autoIndexReferences = false;

    private ?int $referenceIndex = null;

    private bool $adjustCreatedAndUpdatedAt = false;

    private int $createdAndUpdatedAtInterval = 10;

    private int $createdAndUpdatedAtOffset = -120;

    private ?\DateTime $createdAndUpdatedAtDate = null;

    /**
     * @param string $name
     * @param int|string $reference
     */
    public function getReference($name, $reference = null): object
    {
        return parent::getReference($reference !== null ? $name . ':' . ((string) $reference) : $name);
    }

    protected function prependClassNameToReference(bool $value): self
    {
        $this->prependClassNameToReference = $value;

        return $this;
    }

    protected function autoIndexReferences(bool $value): self
    {
        if (!$this->prependClassNameToReference) {
            throw new \LogicException("'prependClassNameToReference' should be enabled");
        }

        $this->autoIndexReferences = $value;

        return $this;
    }

    protected function adjustCreatedAndUpdatedAt(bool $value): self
    {
        $this->adjustCreatedAndUpdatedAt = $value;

        return $this;
    }

    protected function createdAndUpdatedAtInterval(int $value): self
    {
        $this->createdAndUpdatedAtInterval = $value;

        return $this;
    }

    protected function createdAndUpdatedAtOffset(int $value): self
    {
        $this->createdAndUpdatedAtOffset = $value;

        return $this;
    }

    protected function persist(ObjectManager $manager, object $entity, ?string $reference = null): void
    {
        $manager->persist($entity);

        if ($reference === null && $this->autoIndexReferences) {
            if ($this->referenceIndex === null) {
                $this->referenceIndex = 0;
            }

            $reference = (string) ++$this->referenceIndex;
        }

        if ($reference !== null) {
            if ($this->prependClassNameToReference) {
                $reference = \get_class($entity) . ':' . $reference;
            }

            $this->addReference($reference, $entity);
        }

        if ($this->adjustCreatedAndUpdatedAt) {
            $createdAtExists = method_exists($entity, 'setCreatedAt') && method_exists($entity, 'getCreatedAt');
            $updatedAtExists = method_exists($entity, 'setUpdatedAt') && method_exists($entity, 'getUpdatedAt');
            $setCreatedAt = $createdAtExists && $entity->getCreatedAt() === null;
            $setUpdatedAt = $updatedAtExists && $entity->getUpdatedAt() === null;

            if ($setCreatedAt || $setUpdatedAt) {
                if ($this->createdAndUpdatedAtDate === null) {
                    $this->createdAndUpdatedAtDate = new \DateTime("now {$this->createdAndUpdatedAtOffset} seconds");
                } else {
                    $this->createdAndUpdatedAtDate = clone $this->createdAndUpdatedAtDate;
                    $this->createdAndUpdatedAtDate->add(new \DateInterval("PT{$this->createdAndUpdatedAtInterval}S"));
                }

                if ($setCreatedAt) {
                    $entity->setCreatedAt($this->createdAndUpdatedAtDate);
                }

                if ($setUpdatedAt) {
                    $entity->setUpdatedAt($this->createdAndUpdatedAtDate);
                }
            }
        }
    }
}
