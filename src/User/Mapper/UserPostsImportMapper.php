<?php

declare(strict_types=1);

namespace Future\Blog\User\Mapper;

use Future\Blog\Core\Entity\User;
use Future\Blog\Post\Dto\PostDraftDto;
use Future\Blog\Post\Dto\PostDto;
use Future\Blog\Post\Repository\CategoryRepository;
use Future\Blog\User\Dto\Import\UserPostImportDto;
use Future\Blog\User\PostImport\PostImport;
use Future\Blog\User\UserManager\UserPostsImportManager;

class UserPostsImportMapper
{
    private UserPostsImportManager $postsImportManager;

    private CategoryRepository $categoryRepository;

    public function __construct(UserPostsImportManager $postsImportManager, CategoryRepository $categoryRepository)
    {
        $this->postsImportManager = $postsImportManager;
        $this->categoryRepository = $categoryRepository;
    }

    public function setUserPostImportDto(UserPostImportDto $dto, User $user, string $uniqueFileNameAndToken): PostImport
    {
        $this->postsImportManager->uploadImportReturnNameFileInDto($dto, $uniqueFileNameAndToken);

        return new PostImport($user->getId(), $dto->getStatus(), $dto->getImportFileName(), $uniqueFileNameAndToken);
    }

    public function setFromFileInPostDto(
        array $data,
        array $positionsInFile,
        string $status,
        PostImport $postImport
    ): PostDto {
        $postDto = new PostDto();
        $postDto->setTitle($data[$positionsInFile['title']]);
        $postDto->setContent($data[$positionsInFile['content']]);
        $postDto->setSummary($data[$positionsInFile['summary']]);
        $postDto->setStatus($status);
        $category = $this->categoryRepository->findOneByTitle($data[$positionsInFile['category']]);
        $postDto->setCategory($category);
        if ($data[$positionsInFile['tags']]) {
            $postDto->setTags(explode(',', $data[$positionsInFile['tags']]));
        }

        if ($data[$positionsInFile['photo']]) {
            if ($this->postsImportManager->checkImageUrlOrNo($data[$positionsInFile['photo']])) { // If download image by url
                $file = $this->postsImportManager->copyImportImageUrl($data[$positionsInFile['photo']], $postImport);
                if ($file) {
                    $newNameImage = $file->getFilename();
                    $postDto->setViewImage($newNameImage);
                    $postDto->setImageFile($file);
                } else {
                    $postDto->setMistakeImageImportUrl(true); // If No copy image from URL
                }
            }
        }

        return $postDto;
    }

    public function setFromFileInPostDraftDto(
        array $data,
        array $positionsInFile,
        string $status,
        PostImport $postImport
    ): PostDraftDto {
        $PostDraftDto = new PostDraftDto();
        $PostDraftDto->setTitle($data[$positionsInFile['title']]);
        $PostDraftDto->setContent($data[$positionsInFile['content']]);
        $PostDraftDto->setSummary($data[$positionsInFile['summary']]);
        $PostDraftDto->setStatus($status);
        $category = $this->categoryRepository->findOneByTitle($data[$positionsInFile['category']]);
        $PostDraftDto->setCategory($category);
        if ($data[$positionsInFile['tags']]) {
            $PostDraftDto->setTags(explode(',', $data[$positionsInFile['tags']]));
        }

        if ($data[$positionsInFile['photo']]) {
            if ($this->postsImportManager->checkImageUrlOrNo($data[$positionsInFile['photo']])) { // If download image by url
                $file = $this->postsImportManager->copyImportImageUrl($data[$positionsInFile['photo']], $postImport);
                if ($file) {
                    $newNameImage = $file->getFilename();
                    $PostDraftDto->setViewImage($newNameImage);
                    $PostDraftDto->setImageFile($file);
                } else {
                    $PostDraftDto->setMistakeImageImportUrl(true); // If No copy image from URL
                }
            }
        }

        return $PostDraftDto;
    }
}
