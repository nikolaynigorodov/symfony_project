<?php

declare(strict_types=1);

namespace Future\Blog\Post\Controller;

use Future\Blog\Core\Entity\User;
use Future\Blog\Post\Repository\PostRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PostsProfileController extends AbstractController
{
    private PaginatorInterface $paginator;

    private PostRepository $postRepository;

    public function __construct(PostRepository $postRepository, PaginatorInterface $paginator)
    {
        $this->paginator = $paginator;
        $this->postRepository = $postRepository;
    }

    public function __invoke(User $user, Request $request): Response
    {
        $query = $this->postRepository->findPostsByUserStatusPublished($user);
        $pagination = $this->paginator->paginate($query, $request->query->getInt('page', 1), 5);

        return $this->render('post/posts_user_profile.html.twig', [
            'data' => $pagination,
            'user' => $user,
        ]);
    }
}
