<?php

declare(strict_types=1);

namespace Future\Blog\Common\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Future\Blog\Post\Entity\Tag;

class TagFixture extends Fixture
{
    public static function getReferenceKey($i)
    {
        return sprintf('tag_%s', $i);
    }

    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i < 20; $i++) {
            $tag = new Tag();
            $tag->setTitle($i . ' Tag ' . $i);
            $manager->persist($tag);
            $this->addReference(self::getReferenceKey($i), $tag);
        }
        $manager->flush();
    }
}
