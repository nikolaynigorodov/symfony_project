<?php

declare(strict_types=1);

namespace Future\Blog\Common\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Future\Blog\Post\Entity\Comment;

class CommentsFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 10; $i++) {
            $message = 'Some text from comments ' . $i;
            for ($j = 1; $j <= 5; $j++) {
                $comment = new Comment(
                    $message,
                    $this->getReference(UserFixture::getReferenceKey($i)),
                    $this->getReference(PostFixture::getReferenceKey($i)),
                    Comment::APPROVED
                );
                $manager->persist($comment);
            }
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixture::class, PostFixture::class,
        ];
    }
}
