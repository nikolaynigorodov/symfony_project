<?php

declare(strict_types=1);

namespace Future\Blog\Post\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Future\Blog\Common\Entity\Traits\IdentifiableEntityTrait;
use Future\Blog\Common\Entity\Traits\TimestampableEntityTrait;
use Future\Blog\Core\Entity\User;
use Future\Blog\Post\Repository\PostRepository;

/**
 * @ORM\Entity(repositoryClass=PostRepository::class)
 */
class Post
{
    use IdentifiableEntityTrait;
    use TimestampableEntityTrait;

    public const POST_STATUS_DRAFT = 'draft';
    public const POST_STATUS_DELAYED = 'delayed';
    public const POST_STATUS_PUBLISHED = 'published';
    public const POST_STATUS_ARCHIVED = 'archived';
    public const POST_STATUS_BLOCKED = 'blocked';

    public const SUBSCRIPTION_POST_LIMIT = 5;

    public const AVAILABLE_POSTS_STATUS = [
        self::POST_STATUS_PUBLISHED,
        self::POST_STATUS_DRAFT,
        self::POST_STATUS_DELAYED,
        self::POST_STATUS_ARCHIVED,
        self::POST_STATUS_BLOCKED,
    ];

    public const SEARCH_POST_STATUS = [
        self::POST_STATUS_PUBLISHED => self::POST_STATUS_PUBLISHED,
        self::POST_STATUS_DRAFT => self::POST_STATUS_DRAFT,
        self::POST_STATUS_DELAYED => self::POST_STATUS_DELAYED,
        self::POST_STATUS_ARCHIVED => self::POST_STATUS_ARCHIVED,
        self::POST_STATUS_BLOCKED => self::POST_STATUS_BLOCKED,
    ];

    public const POST_IMPORT_STATUS = [
        self::POST_STATUS_PUBLISHED => self::POST_STATUS_PUBLISHED,
        self::POST_STATUS_DRAFT => self::POST_STATUS_DRAFT,
    ];

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $title;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    private ?string $summary;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $content;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class)
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", nullable=true)
     */
    private ?Category $category;

    /**
     * @ORM\ManyToMany(targetEntity=Tag::class, mappedBy="posts")
     * @var Collection|Tag[]
     */
    private Collection $tags;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, fetch="EAGER")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id", nullable=false)
     */
    private User $owner;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private ?string $image = null;

    /**
     * @ORM\OneToMany(targetEntity=PostLikes::class, mappedBy="post")
     * @var Collection|PostLikes[]
     */
    private Collection $postLikes;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $status;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTime $publishingDate;

    public function __construct(
        string $title,
        $summary,
        ?string $content,
        User $owner,
        ?Category $category,
        string $status = self::POST_STATUS_PUBLISHED
    ) {
        $this->title = $title;
        $this->summary = $summary;
        $this->content = $content;
        $this->owner = $owner;
        $this->category = $category;
        $this->tags = new ArrayCollection();
        $this->postLikes = new ArrayCollection();
        $this->status = $status;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSummary(): ?string
    {
        return $this->summary;
    }

    public function setSummary(string $summary): self
    {
        $this->summary = $summary;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection|Tag[]
     */
    public function getTags(): ?Collection
    {
        return $this->tags;
    }

    public function setTags($tags): void
    {
        $this->deleteTags();
        if ($tags) {
            foreach ($tags as $tag) {
                $this->addTags($tag);
            }
        }
    }

    public function getOwner(): User
    {
        return $this->owner;
    }

    public function setOwner(User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return string
     */
    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): void
    {
        $this->image = $image;
    }

    /**
     * @return Collection|PostLikes[]
     */
    public function getPostLikes(): Collection
    {
        return $this->postLikes;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getPublishingDate(): ?\DateTimeInterface
    {
        return $this->publishingDate;
    }

    public function setPublishingDate(?\DateTimeInterface $publishingDate): self
    {
        $this->publishingDate = $publishingDate;

        return $this;
    }

    public function addTags(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
            $tag->addPost($this);
        }

        return $this;
    }

    public function deleteTags(): self
    {
        $tags = $this->getTags();
        if ($tags) {
            foreach ($tags as $tag) {
                $this->removeTags($tag);
            }
        }

        return $this;
    }

    public function removeTags(Tag $tag): self
    {
        if ($this->tags->removeElement($tag)) {
            $tag->removePost($this);
        }

        return $this;
    }

    public function addPostsLike(PostLikes $postsLike): self
    {
        if (!$this->postLikes->contains($postsLike)) {
            $this->postLikes[] = $postsLike;
            $postsLike->setPost($this);
        }

        return $this;
    }

    public function removePostsLike(PostLikes $postsLike): self
    {
        if ($this->postLikes->removeElement($postsLike)) {
            // set the owning side to null (unless already changed)
            if ($postsLike->getPost() === $this) {
                $postsLike->setPost(null);
            }
        }

        return $this;
    }
}
