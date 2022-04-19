<?php

declare(strict_types=1);

namespace Future\Blog\Common\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Future\Blog\Post\Entity\Post;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PostFixture extends Fixture implements DependentFixtureInterface
{
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public static function getReferenceKey($i)
    {
        return sprintf('post_%s', $i);
    }

    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 10; $i++) {
            for ($p = 1; $p <= 5; $p++) {
                $post = new Post($this->textPost($p)['title'], $this->textPost($p)['summary'], $this->textPost($p)['content'], $this->getReference(UserFixture::getReferenceKey($i)), $this->getReference(CategoryFixture::getReferenceKey($i)));
                $post->addTags($this->getReference(TagFixture::getReferenceKey($i)));
                $post->addTags($this->getReference(TagFixture::getReferenceKey($i + 1)));
                $post->setStatus(Post::POST_STATUS_PUBLISHED);
                $post->setPublishingDate(new \DateTime('now'));
                $manager->persist($post);
            }
            $this->addReference(self::getReferenceKey($i), $post);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            TagFixture::class, CategoryFixture::class, UserFixture::class,
        ];
    }

    private function textPost($v): array
    {
        return [
            'title' => 'Some title for post ' . $v,
            'summary' => 'Some text from summary_' . $v,
            'content' => 'This text from content_' . $v . 'is very short.',
        ];
    }
}
