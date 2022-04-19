<?php

declare(strict_types=1);

namespace Future\Blog\User\Controller;

use Future\Blog\Post\Entity\Post;
use Future\Blog\Post\Form\PostStatusType;
use Future\Blog\Post\Repository\PostRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserPostsController extends AbstractController
{
    /**
     * @var PaginatorInterface
     */
    private $paginator;

    /**
     * @var PostRepository
     */
    private $postRepository;

    public function __construct(PostRepository $postRepository, PaginatorInterface $paginator)
    {
        $this->paginator = $paginator;
        $this->postRepository = $postRepository;
    }

    public function __invoke(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
        $query = $this->postRepository->findPostsByUserWithStatus($this->getUser(), [Post::POST_STATUS_PUBLISHED, Post::POST_STATUS_DRAFT, Post::POST_STATUS_DELAYED]);
        $pagination = $this->paginator->paginate($query, $request->query->getInt('page', 1), 5);

        $formSearch = $this->createForm(PostStatusType::class);
        $formSearch->handleRequest($request);
        if ($formSearch->isSubmitted() && $formSearch->isValid()) {
            $searchStatus = $formSearch->get('status')->getData();
            if ($searchStatus) {
                $query = $this->postRepository->findPostsByUserWithStatus($this->getUser(), $searchStatus);
                $pagination = $this->paginator->paginate($query, $request->query->getInt('page', 1), 5);
            }
        }

        return $this->render('user/post/user_posts.html.twig', [
            'form' => $formSearch->createView(),
            'data' => $pagination,
        ]);
    }
}
