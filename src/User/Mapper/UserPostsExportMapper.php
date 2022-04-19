<?php

declare(strict_types=1);

namespace Future\Blog\User\Mapper;

use Doctrine\Common\Collections\ArrayCollection;
use Future\Blog\Core\Entity\User;
use Future\Blog\Post\Entity\Post;
use Future\Blog\User\Dto\Export\UserPostExportDto;
use Future\Blog\User\PostExport\PostExport;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class UserPostsExportMapper
{
    private MailerInterface $mailer;

    private string $hostSite;

    private string $pathToImage;

    private string $emailFrom;

    public function __construct(MailerInterface $mailer, string $hostSite, string $pathToImage, string $emailFrom)
    {
        $this->mailer = $mailer;
        $this->hostSite = $hostSite;
        $this->pathToImage = $pathToImage;
        $this->emailFrom = $emailFrom;
    }

    public function setUserPostExportDto(UserPostExportDto $dto, User $user): PostExport
    {
        $category = $dto->getCategory();

        if ($category) {
            $category = $this->objectCategoryToArray($category);
        }

        return new PostExport($user->getId(), $dto->getStatus(), $category, $dto->getDateFrom(), $dto->getDateTo());
    }

    public function fillingTheArrayForPost(Post $post, $arrayTitleInFile): array
    {
        if ($post) {
            $arrayTitleInFile['title'] = $post->getTitle();
            $arrayTitleInFile['content'] = $post->getContent() ?: '';
            $arrayTitleInFile['summary'] = $post->getSummary() ?: '';
            $arrayTitleInFile['category'] = $post->getCategory() ? $post->getCategory()->getTitle() : '';
            $arrayTitleInFile['tags'] = $post->getTags() ? implode(',', $this->arrayTagsInString($post)) : '';
            $arrayTitleInFile['photo'] = $post->getImage() ? $this->hostSite . $this->pathToImage . $post->getImage() : '';
        }

        return $arrayTitleInFile;
    }

    /**
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function sendPostExportEmail(string $email, string $fileName): void
    {
        $email = (new TemplatedEmail())
            ->from($this->emailFrom)
            ->to($email)
            ->subject('Post Export')
            ->text('Poss Export')
            ->htmlTemplate('user/post/export/email/post_export_email.html.twig')

            ->context([
                'fileName' => $fileName,
            ])
        ;

        $this->mailer->send($email);
    }

    protected function objectCategoryToArray(ArrayCollection $categories): array
    {
        $array = [];
        foreach ($categories as $category) {
            $array[] = $category->getId();
        }

        return $array;
    }

    private function arrayTagsInString(Post $post): array
    {
        $array = [];
        if ($post->getTags()) {
            foreach ($post->getTags() as $tag) {
                $array[] = $tag->getTitle();
            }
        }

        return $array;
    }
}
