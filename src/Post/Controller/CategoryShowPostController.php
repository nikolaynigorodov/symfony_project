<?php

declare(strict_types=1);

namespace Future\Blog\Post\Controller;

use Future\Blog\Post\Entity\Category;
use Future\Blog\Post\Repository\PostRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CategoryShowPostController extends AbstractController
{
    private PaginatorInterface $paginator;

    private PostRepository $postRepository;

    public function __construct(PostRepository $postRepository, PaginatorInterface $paginator)
    {
        $this->paginator = $paginator;
        $this->postRepository = $postRepository;
    }

    public function __invoke(Category $category, Request $request): Response
    {
        $query = $this->postRepository->findByCategoryOrderedByIdQuery($category->getId());
        $pagination = $this->paginator->paginate($query, $request->query->getInt('page', 1), 5);

        return $this->render('category/category_show_post.html.twig', [
            'data' => $pagination,
        ]);
    }
}
