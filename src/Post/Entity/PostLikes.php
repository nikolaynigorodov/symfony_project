<?php

declare(strict_types=1);

namespace Future\Blog\Post\Entity;

use Doctrine\ORM\Mapping as ORM;
use Future\Blog\Common\Entity\Traits\IdentifiableEntityTrait;
use Future\Blog\Core\Entity\User;
use Future\Blog\Post\Repository\PostLikesRepository;

/**
 * @ORM\Entity(repositoryClass=PostLikesRepository::class)
 */
class PostLikes
{
    use IdentifiableEntityTrait;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="postLikes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Post::class, inversedBy="postLikes")
     */
    private $post;

    public function __construct(User $user, Post $post)
    {
        $this->user = $user;
        $this->post = $post;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): self
    {
        $this->post = $post;

        return $this;
    }
}
