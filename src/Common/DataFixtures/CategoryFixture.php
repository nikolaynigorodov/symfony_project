<?php

declare(strict_types=1);

namespace Future\Blog\Common\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Future\Blog\Post\Entity\Category;

class CategoryFixture extends Fixture
{
    public static function getReferenceKey($i)
    {
        return sprintf('category_%s', $i);
    }

    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i < 11; $i++) {
            $category = new Category();
            $category->setTitle($i . ' Category ' . $i);
            $manager->persist($category);
            $this->addReference(self::getReferenceKey($i), $category);
        }
        $manager->flush();
    }
}
