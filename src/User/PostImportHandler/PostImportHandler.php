<?php

declare(strict_types=1);

namespace Future\Blog\User\PostImportHandler;

use Doctrine\ORM\EntityManagerInterface;
use Future\Blog\Post\Entity\Post;
use Future\Blog\User\Entity\Import;
use Future\Blog\User\Mapper\UserPostsImportMapper;
use Future\Blog\User\PostImport\PostImport;
use Future\Blog\User\Repository\ImportRepository;
use Future\Blog\User\UserManager\PostImportLoggerManager;
use Future\Blog\User\UserManager\UserPostsImportManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PostImportHandler implements MessageHandlerInterface
{
    private ValidatorInterface $validator;

    private UserPostsImportManager $importManager;

    private UserPostsImportMapper $importMapper;

    private EntityManagerInterface $entityManager;

    private LoggerInterface $logger;

    private Import $import;

    private PostImportLoggerManager $importLoggerManager;

    private ImportRepository $importRepository;

    private string $pathToImportFolder;

    public function __construct(
        UserPostsImportManager $importManager,
        UserPostsImportMapper $importMapper,
        ValidatorInterface $validator,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        PostImportLoggerManager $importLoggerManager,
        ImportRepository $importRepository,
        string $pathToImportFolder
    ) {
        $this->validator = $validator;
        $this->importManager = $importManager;
        $this->importMapper = $importMapper;
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this->importLoggerManager = $importLoggerManager;
        $this->importRepository = $importRepository;
        $this->pathToImportFolder = $pathToImportFolder;
    }

    public function __invoke(PostImport $postImport): void
    {
        $allowedNamesForImport = Import::ALLOWED_NAMES_FOR_IMPORT;
        $batchSize = 20;
        $rowInFile = 0;
        $countImportPostTrue = 0;
        $positionsVariablesInFile = [];
        if ($reader = fopen($this->pathToImportFolder . $postImport->getFileName(), 'r')) {
            while (($data = fgetcsv($reader, 8000, ',')) !== false) {
                if ($data[0] === null && !isset($data[1])) { // check on empty row
                    $this->importLoggerManager->logging('Post Import Empty Row', $postImport, $rowInFile);
                    $this->importManager->sendEmailInvalidFile($postImport);

                    break;
                }

                if ($rowInFile === 0) {
                    $this->import = $this->importManager->createNewImport($postImport->getUniqueFileNameAndToken(), $postImport->getUserId());
                    $positionsVariablesInFile = array_flip(array_change_key_case($data, CASE_LOWER));
                    $result = array_diff($data, $allowedNamesForImport);
                    if (!empty($result)) {
                        $this->importManager->sendEmailInvalidFile($postImport);

                        break;
                    }
                    ++$rowInFile;

                    continue;
                }
                ++$rowInFile;

                if ($this->importManager->checkSubscriptionInImport($postImport)) {
                    switch ($postImport->getStatus()) {
                        case Post::POST_STATUS_PUBLISHED:
                            $postDto = $this->importMapper->setFromFileInPostDto($data, $positionsVariablesInFile, $postImport->getStatus(), $postImport);
                            $errors = $this->validator->validate($postDto);
                            if (\count($errors) > 0 || $postDto->isMistakeImageImportUrl()) {
                                $this->importManager->importReportError($errors, $this->import, $rowInFile);
                                if ($postDto->isMistakeImageImportUrl()) { // Error Incorrect URL Images
                                    $this->importManager->importReportErrorFromImageUrl($this->import, $rowInFile);
                                }

                                if ($postDto->getImageFile()) {
                                    $this->importManager->removeImages($postDto->getImageFile()->getFilename());
                                }
                            } else {
                                $this->importManager->saveImportPostPublished($postDto, $postImport);
                                ++$countImportPostTrue;
                            }

                            if (($rowInFile % $batchSize) === 0) {
                                $this->entityManager->flush();
                                $this->entityManager->clear();

                                // Reload the $this->import entity after clearing the EntityManager
                                $this->import = $this->importRepository->findOneByToken($postImport->getUniqueFileNameAndToken());
                            }

                            break;

                        case Post::POST_STATUS_DRAFT:
                            $postDraftDto = $this->importMapper->setFromFileInPostDraftDto($data, $positionsVariablesInFile, $postImport->getStatus(), $postImport);
                            $errors = $this->validator->validate($postDraftDto);
                            if (\count($errors) > 0 || $postDraftDto->isMistakeImageImportUrl()) {
                                $this->importManager->importReportError($errors, $this->import, $rowInFile);
                                if ($postDraftDto->isMistakeImageImportUrl()) { // Error Incorrect URL Images
                                    $this->importManager->importReportErrorFromImageUrl($this->import, $rowInFile);
                                }

                                if ($postDraftDto->getImageFile()) {
                                    $this->importManager->removeImages($postDraftDto->getImageFile()->getFilename());
                                }
                            } else {
                                $this->importManager->saveImportPostDraft($postDraftDto, $postImport);
                                ++$countImportPostTrue;
                            }
                            if (($rowInFile % $batchSize) === 0) {
                                $this->entityManager->flush();
                                $this->entityManager->clear();

                                // Reload the $this->import entity after clearing the EntityManager
                                $this->import = $this->importRepository->findOneByToken($postImport->getUniqueFileNameAndToken());
                            }

                            break;

                        default:
                            $this->importLoggerManager->logging('Post Import No Status', $postImport, $rowInFile);
                    }
                    $this->importManager->saveImportCountPosts($this->import, $rowInFile, $countImportPostTrue);
                    $this->entityManager->flush();
                } else { // No Subscription
                    $this->importManager->sendEmailNoSubscription($postImport);

                    break;
                }
            }
        } else {
            $this->importLoggerManager->logging('Post Import No File', $postImport, $rowInFile);
        }
        fclose($reader);
        $this->importManager->sendPostImportEmail($postImport);
    }
}
