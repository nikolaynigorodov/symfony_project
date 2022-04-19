<?php

declare(strict_types=1);

namespace Future\Blog\Post\Controller;

use Future\Blog\Post\Dto\PostLikeAjaxDto;
use Future\Blog\Post\Entity\Post;
use Future\Blog\Post\Manager\PostLikesManager;
use Future\Blog\Post\Security\PostLikeVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

class PostPostLikesAjaxController extends AbstractController
{
    private PostLikesManager $postLikesManager;

    private SerializerInterface $serializer;

    public function __construct(PostLikesManager $postLikesManager, SerializerInterface $serializer)
    {
        $this->postLikesManager = $postLikesManager;
        $this->serializer = $serializer;
    }

    public function __invoke(Post $post, Request $request): JsonResponse
    {
        $user = $this->getUser();
        $this->denyAccessUnlessGranted(PostLikeVoter::POST_LIKE, $post);

        $userLikes = $this->postLikesManager->checkPostLikes($user, $post);
        if (!$userLikes) {
            $this->postLikesManager->savePostLikes($user, $post);
            $likeUserStatus = true;
        } else {
            $this->postLikesManager->deletePostLikes($userLikes);
            $likeUserStatus = false;
        }
        $dto = new PostLikeAjaxDto($likeUserStatus, $post->getPostLikes()->count());

        return new JsonResponse([
            'data' => $this->serializer->serialize($dto, 'json'),
        ], 200);
    }
}
