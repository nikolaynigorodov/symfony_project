<?php

declare(strict_types=1);

namespace Future\Blog\Post\Entity;

use Doctrine\ORM\Mapping as ORM;
use Future\Blog\Common\Entity\Traits\IdentifiableEntityTrait;
use Future\Blog\Common\Entity\Traits\TimestampableEntityTrait;
use Future\Blog\Core\Entity\User;
use Future\Blog\Post\Repository\CommentRepository;

/**
 * @ORM\Entity(repositoryClass=CommentRepository::class)
 */
class Comment
{
    use IdentifiableEntityTrait;
    use TimestampableEntityTrait;

    public const PENDING = 0;
    public const APPROVED = 1;
    public const DECLINED = 2;
    public const AVAILABLE_TYPES = [
        self::PENDING => 'PENDING',
        self::APPROVED => 'APPROVED',
        self::DECLINED => 'DECLINED',
    ];

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id", nullable=false)
     */
    private User $author;

    /**
     * @ORM\Column(type="text")
     */
    private string $message;

    /**
     * @ORM\Column(type="integer")
     */
    private int $status;

    /**
     * @ORM\ManyToOne(targetEntity=Post::class)
     * @ORM\JoinColumn(name="post_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    private Post $post;

    public function __construct(string $message, User $user, Post $post, int $status)
    {
        $this->message = $message;
        $this->author = $user;
        $this->post = $post;
        $this->status = $status;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(User $author): self
    {
        $this->author = $author;

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

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(Post $post): self
    {
        $this->post = $post;

        return $this;
    }

    public function getStatusAdmin(): string
    {
        return self::AVAILABLE_TYPES[$this->status];
    }
}
