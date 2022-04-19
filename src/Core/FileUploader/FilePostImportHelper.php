<?php

declare(strict_types=1);

namespace Future\Blog\Core\FileUploader;

use Future\Blog\User\PostImport\PostImport;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpClient\CurlHttpClient;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FilePostImportHelper
{
    private string $targetDirectory;

    private SluggerInterface $slugger;

    private Filesystem $fileSystem;

    private string $imageDirectory;

    private LoggerInterface $logger;

    public function __construct(
        string $targetDirectory,
        string $imageDirectory,
        SluggerInterface $slugger,
        Filesystem $fileSystem,
        LoggerInterface $logger
    ) {
        $this->targetDirectory = $targetDirectory;
        $this->slugger = $slugger;
        $this->fileSystem = $fileSystem;
        $this->imageDirectory = $imageDirectory;
        $this->logger = $logger;
    }

    public function getTargetDirectory(?string $anotherDirectory): string
    {
        if ($anotherDirectory) {
            $this->targetDirectory = $anotherDirectory;
        }

        return $this->targetDirectory;
    }

    public function uploadFileImportPost(UploadedFile $file, string $anotherDirectory, string $nameOnlyUnique): string
    {
        $fileName = $nameOnlyUnique . '.' . $file->getClientOriginalExtension();
        $file->move($this->getTargetDirectory($anotherDirectory), $fileName);

        return $fileName;
    }

    public function removeUrlImages(string $fileName): void
    {
        $fileImage = $this->getTargetDirectory($this->imageDirectory) . $fileName;
        $this->fileSystem->remove($fileImage);
    }

    public function copyImportImage(string $originFileName, string $newFileName): string
    {
        $newName = '';
        $exist = $this->fileSystem->exists($this->imageDirectory . $originFileName);
        if ($exist) {
            $fileExtension = $this->getExtension($originFileName);
            $newName = $newFileName . '.' . $fileExtension;
            $this->fileSystem->copy($this->imageDirectory . $originFileName, $this->imageDirectory . $newName, true);
        }

        return $newName;
    }

    public function downloadImageByUrl(string $url, PostImport $postImport): ?File
    {
        try {
            $client = new CurlHttpClient();
            $response = $client->request('GET', $url);
            if ($response->getStatusCode() === 200) {
                $info = $response->getHeaders();
                preg_match('/\/([\S]+)$/', $info['content-type'][0], $result);
                if ($result[1]) {
                    $extension = $result[1];
                    $newName = 'import_url_photo_' . uniqid() . '.' . $extension;
                    $fileHandler = fopen($this->imageDirectory . $newName, 'w');
                    foreach ($client->stream($response) as $chunk) {
                        fwrite($fileHandler, $chunk->getContent());
                    }

                    return new File($this->imageDirectory . $newName);
                }
                $this->logger->error('Post Import Url Images', [
                    'status' => $postImport->getStatus(),
                    'user' => $postImport->getUserId(),
                    'file_name' => $postImport->getFileName(),
                    'error' => 'error no extension',
                ]);
            } else {
                $this->logger->error('Post Import Url Images', [
                    'status' => $postImport->getStatus(),
                    'user' => $postImport->getUserId(),
                    'file_name' => $postImport->getFileName(),
                    'error' => 'error status response != 200',
                ]);
            }
        } catch (\Symfony\Component\HttpClient\Exception\TransportException | \Exception | \Throwable $exception) {
            $this->logger->error('Post Import Url Images', [
                'status' => $postImport->getStatus(),
                'user' => $postImport->getUserId(),
                'file_name' => $postImport->getFileName(),
                'error' => $exception->getMessage(),
            ]);

            return null;
        }

        return null;
    }

    public function getExtension(string $fileName): string
    {
        return substr($fileName, strrpos($fileName, '.') + 1);
    }
}
