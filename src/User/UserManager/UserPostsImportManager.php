<?php

declare(strict_types=1);

namespace Future\Blog\User\UserManager;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Future\Blog\Core\Entity\User;
use Future\Blog\Core\FileUploader\FilePostImportHelper;
use Future\Blog\Core\Repository\UserRepository;
use Future\Blog\Post\Dto\PostDraftDto;
use Future\Blog\Post\Dto\PostDto;
use Future\Blog\Post\Entity\Post;
use Future\Blog\Post\Entity\Tag;
use Future\Blog\Post\Mapper\PostCreateMapper;
use Future\Blog\Post\Repository\TagRepository;
use Future\Blog\Stripe\Manager\UserPostCreateChecker;
use Future\Blog\User\Dto\Import\UserPostImportDto;
use Future\Blog\User\Entity\Import;
use Future\Blog\User\Entity\ImportReport;
use Future\Blog\User\PostImport\PostImport;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserPostsImportManager
{
    private FilePostImportHelper $fileUploader;

    private string $postImportDirectory;

    private PostCreateMapper $createMapper;

    private TagRepository $tagRepository;

    private EntityManagerInterface $entityManager;

    private UserRepository $userRepository;

    private MailerInterface $mailer;

    private TranslatorInterface $translator;

    private string $emailFrom;

    private UserPostCreateChecker $subscriptionPayCheck;

    public function __construct(
        FilePostImportHelper $fileUploader,
        string $postImportDirectory,
        PostCreateMapper $createMapper,
        TagRepository $tagRepository,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        MailerInterface $mailer,
        TranslatorInterface $translator,
        string $emailFrom,
        UserPostCreateChecker $subscriptionPayCheck
    ) {
        $this->fileUploader = $fileUploader;
        $this->postImportDirectory = $postImportDirectory;
        $this->createMapper = $createMapper;
        $this->tagRepository = $tagRepository;
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->mailer = $mailer;
        $this->translator = $translator;
        $this->emailFrom = $emailFrom;
        $this->subscriptionPayCheck = $subscriptionPayCheck;
    }

    public function uploadImportReturnNameFileInDto(
        UserPostImportDto $dto,
        string $uniqueFileNameAndToken
    ): UserPostImportDto {
        $importUploadedFile = $dto->getImportFile();
        if ($importUploadedFile) {
            $importFileName = $this->fileUploader->uploadFileImportPost($importUploadedFile, $this->postImportDirectory, $uniqueFileNameAndToken);
            $dto->setImportFileName($importFileName);
        }

        return $dto;
    }

    public function checkImageUrlOrNo(string $url): array
    {
        preg_match('/^(http|https|www)/', $url, $output_array);

        return $output_array;
    }

    public function copyImportImageUrl(string $url, PostImport $postImport): ?File
    {
        return $this->fileUploader->downloadImageByUrl($url, $postImport);
    }

    public function removeImages(string $imageName): void
    {
        $this->fileUploader->removeUrlImages($imageName);
    }

    public function saveImportCountPosts(Import $import, int $countPostsAll, int $countPostsTrue): void
    {
        $import->setCountPostsAll($countPostsAll - 1);
        $import->setCountPostsTrue($countPostsTrue);
    }

    public function checkSubscriptionInImport(PostImport $postImport): bool
    {
        $user = $this->findUserFromImport($postImport->getUserId());

        return $this->subscriptionPayCheck->userCheck($user);
    }

    public function saveImportPostPublished(PostDto $postDto, PostImport $postImport): Post
    {
        $user = $this->findUserFromImport($postImport->getUserId());
        $post = $this->createMapper->setPost($postDto, $user, $postImport->getStatus());
        $post->setPublishingDate(new \DateTime('now'));
        if ($postDto->getViewImage()) {
            $post->setImage($postDto->getViewImage());
        }

        if ($postDto->getTags()) {
            $post->setTags($this->findAndSaveTags($postDto->getTags()));
        }

        $this->entityManager->persist($post);

        return $post;
    }

    public function saveImportPostDraft(PostDraftDto $postDto, PostImport $postImport): Post
    {
        $user = $this->findUserFromImport($postImport->getUserId());
        $post = $this->createMapper->setPost($postDto, $user, $postImport->getStatus());
        if ($postDto->getViewImage()) {
            $post->setImage($postDto->getViewImage());
        }

        if ($postDto->getTags()) {
            $post->setTags($this->findAndSaveTags($postDto->getTags()));
        }

        $this->entityManager->persist($post);

        return $post;
    }

    public function findUserFromImport(int $userId): User
    {
        return $this->userRepository->find($userId);
    }

    public function importReportError(ConstraintViolationList $errors, Import $import, int $rowInFile): void
    {
        foreach ($errors as $error) {
            $importReport = new ImportReport($import, $rowInFile, $error->getPropertyPath(), $error->getMessage());
            $this->entityManager->persist($importReport);
        }
        $this->entityManager->flush();
    }

    public function importReportErrorFromImageUrl(Import $import, int $rowInFile): void
    {
        $property = 'photo';
        $message = $this->translator->trans('post.import.photo_message_error');
        $importReport = new ImportReport($import, $rowInFile, $property, $message);
        $this->entityManager->persist($importReport);
        $this->entityManager->flush();
    }

    public function createNewImport(string $token, int $userId): Import
    {
        $user = $this->findUserFromImport($userId);
        $import = new Import($token, $user);
        $this->entityManager->persist($import);
        $this->entityManager->flush();

        return $import;
    }

    /**
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function sendEmailInvalidFile(PostImport $postImport): void
    {
        $user = $this->findUserFromImport($postImport->getUserId());
        $email = (new TemplatedEmail())
            ->from($this->emailFrom)
            ->to($user->getEmail())
            ->subject('Importing an invalid file.')
            ->text('Importing an invalid file.')
            ->htmlTemplate('user/post/import/email/invalid_file.html.twig')

            ->context([
            ])
        ;

        $this->mailer->send($email);
    }

    /**
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function sendPostImportEmail(PostImport $postImport): void
    {
        $user = $this->findUserFromImport($postImport->getUserId());
        $token = $postImport->getUniqueFileNameAndToken();
        $email = (new TemplatedEmail())
            ->from($this->emailFrom)
            ->to($user->getEmail())
            ->subject('Post Import')
            ->text('Post Import')
            ->htmlTemplate('user/post/import/email/post_import_email.html.twig')

            ->context([
                'token' => $token,
            ])
        ;

        $this->mailer->send($email);
    }

    /**
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function sendEmailNoSubscription(PostImport $postImport): void
    {
        $user = $this->findUserFromImport($postImport->getUserId());
        $email = (new TemplatedEmail())
            ->from($this->emailFrom)
            ->to($user->getEmail())
            ->subject('Importing you are not subscription.')
            ->text('Importing you are not subscription.')
            ->htmlTemplate('user/post/import/email/subscription_false.html.twig')

            ->context([
            ])
        ;

        $this->mailer->send($email);
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
