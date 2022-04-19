<?php

declare(strict_types=1);

namespace Future\Blog\Post\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Future\Blog\Core\Entity\User;
use Future\Blog\Core\FileUploader\FileUploader;
use Future\Blog\Post\Dto\PostDraftDto;
use Future\Blog\Post\Dto\PostDto;
use Future\Blog\Post\Entity\Post;
use Future\Blog\Post\Entity\Tag;
use Future\Blog\Post\Mapper\PostCreateMapper;
use Future\Blog\Post\Repository\TagRepository;

class PostManager
{
    private FileUploader $fileUploader;

    private string $imageDirectory = 'post/image';

    private EntityManagerInterface $entityManager;

    private TagRepository $tagRepository;

    private PostCreateMapper $createMapper;

    public function __construct(
        FileUploader $fileUploader,
        EntityManagerInterface $entityManager,
        TagRepository $tagRepository,
        PostCreateMapper $createMapper
    ) {
        $this->fileUploader = $fileUploader;
        $this->entityManager = $entityManager;
        $this->tagRepository = $tagRepository;
        $this->createMapper = $createMapper;
    }

    public function savePost(PostDto $postDto, User $user, string $status = Post::POST_STATUS_PUBLISHED): Post
    {
        $post = $this->createMapper->setPost($postDto, $user, $status);
        $imageObjectUploadedFile = $postDto->getImageFile();
        if ($imageObjectUploadedFile) {
            $imageFileName = $this->fileUploader->upload($imageObjectUploadedFile, $this->imageDirectory);
            $post->setImage($imageFileName);
        }

        if ($postDto->getTags()) {
            $post->setTags($this->findAndSaveTags($postDto->getTags()));
        }

        if ($status === Post::POST_STATUS_DELAYED || $status === Post::POST_STATUS_PUBLISHED) {
            $post->setPublishingDate($postDto->getPublishingDate());
        } elseif ($status === Post::POST_STATUS_PUBLISHED) {
            $post->setPublishingDate($postDto->getCreatePostTimeNow());
        }
        $this->entityManager->persist($post);
        $this->entityManager->flush();

        return $post;
    }

    public function savePostStatusDraft(
        PostDraftDto $postDto,
        User $user,
        string $status = Post::POST_STATUS_PUBLISHED
    ): Post {
        $post = $this->createMapper->setPost($postDto, $user, $status);
        $imageObjectUploadedFile = $postDto->getImageFile();
        if ($imageObjectUploadedFile) {
            $imageFileName = $this->fileUploader->upload($imageObjectUploadedFile, $this->imageDirectory);
            $post->setImage($imageFileName);
        }

        if ($postDto->getTags()) {
            $post->setTags($this->findAndSaveTags($postDto->getTags()));
        }

        if ($status === Post::POST_STATUS_DELAYED || $status === Post::POST_STATUS_PUBLISHED) {
            $post->setPublishingDate($postDto->getCreatePostTimeNow());
        }
        $this->entityManager->persist($post);
        $this->entityManager->flush();

        return $post;
    }

    public function updatePost(Post $post, PostDto $postDto, string $status = Post::POST_STATUS_PUBLISHED): void
    {
        $post->setTitle($postDto->getTitle());
        $post->setSummary($postDto->getSummary());
        $post->setContent($postDto->getContent());
        $post->setCategory($postDto->getCategory());
        $post->setStatus($status);
        if ($status === Post::POST_STATUS_DELAYED) {
            $post->setPublishingDate($postDto->getPublishingDate());
        }
        $oldImage = $post->getImage();
        $imageObjectUploadedFile = $postDto->getImageFile();
        if ($imageObjectUploadedFile) {
            $imageFileName = $this->fileUploader->upload($imageObjectUploadedFile, $this->imageDirectory);
            $post->setImage($imageFileName);
            if ($oldImage) { // Delete Old Avatar
                $this->fileUploader->removeImages($oldImage, $this->imageDirectory);
            }
        }

        if (!$postDto->getTags()) {
            $post->setTags([]);
        } else {
            $post->setTags($this->findAndSaveTags($postDto->getTags()));
        }
        $this->entityManager->flush();
    }

    /**
     * @param PostDraftDto[] $postDto
     */
    public function updatePostStatusDraft(
        Post $post,
        PostDraftDto $postDto,
        string $status = Post::POST_STATUS_PUBLISHED
    ): void {
        $post->setTitle($postDto->getTitle());
        $post->setSummary($postDto->getSummary());
        $post->setContent($postDto->getContent());
        $post->setCategory($postDto->getCategory());
        $post->setStatus($status);
        if ($status === Post::POST_STATUS_DELAYED) {
            $post->setPublishingDate($postDto->getPublishingDate());
        }
        $oldImage = $post->getImage();
        $imageObjectUploadedFile = $postDto->getImageFile();
        if ($imageObjectUploadedFile) {
            $imageFileName = $this->fileUploader->upload($imageObjectUploadedFile, $this->imageDirectory);
            $post->setImage($imageFileName);
            if ($oldImage) { // Delete Old Avatar
                $this->fileUploader->removeImages($oldImage, $this->imageDirectory);
            }
        }

        if (!$postDto->getTags()) {
            $post->setTags([]);
        } else {
            $post->setTags($this->findAndSaveTags($postDto->getTags()));
        }
        $this->entityManager->flush();
    }

    private function findAndSaveTags(array $arrayTags): ArrayCollection
    {
        $tagCollections = new ArrayCollection();

        if ($arrayTags) {
            foreach ($arrayTags as $title) {
                $findTag = $this->tagRepository->findOneByTitle(trim($title));
                if (!$findTag) {
                    $newTag = new Tag();
                    $newTag->setTitle(trim($title));
                    $this->entityManager->persist($newTag);
                    $this->entityManager->flush();
                    $tagCollections->add($newTag);
                } else {
                    $tagCollections->add($findTag);
                }
            }
        }

        return $tagCollections;
    }
}
