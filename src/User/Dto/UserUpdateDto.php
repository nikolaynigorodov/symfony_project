<?php

declare(strict_types=1);

namespace Future\Blog\User\Dto;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

class UserUpdateDto
{
    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=100)
     */
    private ?string $firstName;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=100)
     */
    private ?string $lastName;

    private ?string $viewAvatar = null;

    /**
     * @Assert\Image(
     *     maxHeight=400,
     *     maxHeightMessage="user.edit.avatar.image_height",
     *     maxWidth=400,
     *     maxWidthMessage="user.edit.avatar.image_width",
     * )
     * @Assert\File(
     *     maxSize="4m",
     *     maxSizeMessage="user.edit.avatar.image_size",
     *     mimeTypes={"image/jpeg", "image/jpg", "image/png"},
     *     mimeTypesMessage="user.edit.avatar.image_type"
     * )
     */
    private ?File $avatarFile = null;

    /**
     * @return string
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getViewAvatar(): ?string
    {
        return $this->viewAvatar;
    }

    public function setViewAvatar(?string $viewAvatar): void
    {
        $this->viewAvatar = $viewAvatar;
    }

    public function getAvatarFile(): ?File
    {
        return $this->avatarFile;
    }

    public function setAvatarFile(?File $avatarFile): void
    {
        $this->avatarFile = $avatarFile;
    }
}
