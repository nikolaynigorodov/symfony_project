<?php

declare(strict_types=1);

namespace Future\Blog\Post\Controller;

use Future\Blog\Post\Entity\Tag;
use Future\Blog\Post\Repository\PostRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TagShowPostController extends AbstractController
{
    private PaginatorInterface $paginator;

    private PostRepository $postRepository;

    public function __construct(PostRepository $postRepository, PaginatorInterface $paginator)
    {
        $this->paginator = $paginator;
        $this->postRepository = $postRepository;
    }

    public function __invoke(Tag $tag, Request $request): Response
    {
        $query = $this->postRepository->findByTagQuery($tag->getId());
        $pagination = $this->paginator->paginate($query, $request->query->getInt('page', 1), 5);

        return $this->render('tag/tag_show_post.html.twig', [
            'data' => $pagination,
        ]);
    }
}
