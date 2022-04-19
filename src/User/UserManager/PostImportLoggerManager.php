<?php

declare(strict_types=1);

namespace Future\Blog\User\UserManager;

use Future\Blog\User\PostImport\PostImport;
use Psr\Log\LoggerInterface;

class PostImportLoggerManager
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function logging(string $title, PostImport $postImport, int $rowInFile): void
    {
        $this->logger->error($title, [
            'status' => $postImport->getStatus(),
            'user' => $postImport->getUserId(),
            'file_name' => $postImport->getFileName(),
            'rowInFiles' => $rowInFile,
        ]);
    }
}
