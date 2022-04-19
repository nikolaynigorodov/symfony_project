<?php

declare(strict_types=1);

namespace Future\Blog\Post\Form\DataTransformer;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class TagsTransformer implements DataTransformerInterface
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Transforms an object (tags) to a string.
     */
    public function transform($tagsArray): string
    {
        if ($tagsArray === null) {
            return '';
        }

        return implode(', ', $tagsArray);
    }

    /**
     * Transforms a string to an object.
     *
     * @param  string $tagsString
     */
    public function reverseTransform($tagsString): ?array
    {
        if (!$tagsString) {
            return null;
        }

        if (!preg_match('/^[a-zA-Zа-яА-Я0-9\d]/ui', $tagsString)) {
            throw new TransformationFailedException(
                'post.edit.tags_message'
            );
        }
        $arrayTags = explode(',', $tagsString);

        return $arrayTags;
    }
}
