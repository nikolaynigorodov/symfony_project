<?php

declare(strict_types=1);

namespace Future\Blog\Core\FileUploader;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{
    private string $targetDirectory;

    private SluggerInterface $slugger;

    private Filesystem $fileSystem;

    private string $imageDirectory;

    public function __construct(
        string $targetDirectory,
        string $imageDirectory,
        SluggerInterface $slugger,
        Filesystem $fileSystem
    ) {
        $this->targetDirectory = $targetDirectory;
        $this->slugger = $slugger;
        $this->fileSystem = $fileSystem;
        $this->imageDirectory = $imageDirectory;
    }

    public function getTargetDirectory(?string $anotherDirectory): string
    {
        if ($anotherDirectory) {
            $this->targetDirectory = $anotherDirectory;
        }

        return $this->targetDirectory;
    }

    public function upload(UploadedFile $file, ?string $anotherDirectory = null): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

        $file->move($this->getTargetDirectory($anotherDirectory), $fileName);

        return $fileName;
    }

    public function removeImages(string $fileName, ?string $anotherDirectory = null): void
    {
        $fileImage = $this->getTargetDirectory($anotherDirectory) . $fileName;
        $this->fileSystem->remove($fileImage);
    }
}
