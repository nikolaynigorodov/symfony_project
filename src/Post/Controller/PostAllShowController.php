<?php

declare(strict_types=1);

namespace Future\Blog\Post\Controller;

use Future\Blog\Post\Dto\PostSearchDto;
use Future\Blog\Post\Form\PostSearchType;
use Future\Blog\Post\Repository\PostRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PostAllShowController extends AbstractController
{
    private PaginatorInterface $paginator;

    private PostRepository $postRepository;

    public function __construct(PostRepository $postRepository, PaginatorInterface $paginator)
    {
        $this->paginator = $paginator;
        $this->postRepository = $postRepository;
    }

    public function __invoke(Request $request): Response
    {
        $searchDto = new PostSearchDto();
        $formSearch = $this->createForm(PostSearchType::class, $searchDto, [
            'action' => $this->generateUrl('post_post_search'),
        ]);

        $query = $this->postRepository->findAllQuery();
        $pagination = $this->paginator->paginate($query, $request->query->getInt('page', 1), 5);

        return $this->render('post/post_all_show.html.twig', [
            'form' => $formSearch->createView(),
            'data' => $pagination,
        ]);
    }
}
