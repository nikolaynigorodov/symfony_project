<?php

declare(strict_types=1);

namespace Future\Blog\User\PostExportHandler;

use Future\Blog\Core\Repository\UserRepository;
use Future\Blog\Post\Repository\PostRepository;
use Future\Blog\User\Mapper\UserPostsExportMapper;
use Future\Blog\User\PostExport\PostExport;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class PostExportHandler implements MessageHandlerInterface
{
    private PostRepository $postRepository;

    private UserRepository $userRepository;

    private UserPostsExportMapper $postsExportMapper;

    public function __construct(
        PostRepository $postRepository,
        UserRepository $userRepository,
        UserPostsExportMapper $postsExportMapper
    ) {
        $this->postRepository = $postRepository;
        $this->userRepository = $userRepository;
        $this->postsExportMapper = $postsExportMapper;
    }

    public function __invoke(PostExport $postExport): void
    {
        $arrayWithTitle = [
            'title' => 'title',
            'content' => 'content',
            'summary' => 'summary',
            'category' => 'category',
            'tags' => 'tags',
            'photo' => 'photo',
        ];

        $posts = $this->postRepository->findPostForExport($postExport);

        $uniqueFileName = uniqid() . '.csv';
        $user = $this->userRepository->find($postExport->getUserId());
        if ($user) {
            $userEmail = $user->getEmail();
            $writer = fopen('public/export/' . $uniqueFileName, 'w+');
            fputcsv($writer, $arrayWithTitle);
            foreach ($posts->toIterable() as $post) {
                $result = $this->postsExportMapper->fillingTheArrayForPost($post, $arrayWithTitle);
                fputcsv($writer, $result);
            }
            fclose($writer);
            $this->postsExportMapper->sendPostExportEmail($userEmail, $uniqueFileName);
        }
    }
}
