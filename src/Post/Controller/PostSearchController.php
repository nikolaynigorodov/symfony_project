<?php

declare(strict_types=1);

namespace Future\Blog\Post\Controller;

use Future\Blog\Post\Form\PostSearchType;
use Future\Blog\Post\Repository\PostRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class PostSearchController extends AbstractController
{
    private PostRepository $postRepository;

    private PaginatorInterface $paginator;

    private TranslatorInterface $translator;

    public function __construct(
        PostRepository $postRepository,
        PaginatorInterface $paginator,
        TranslatorInterface $translator
    ) {
        $this->postRepository = $postRepository;
        $this->paginator = $paginator;
        $this->translator = $translator;
    }

    public function __invoke(Request $request): Response
    {
        $formSearch = $this->createForm(PostSearchType::class);
        $formSearch->handleRequest($request);
        if ($formSearch->isSubmitted() && $formSearch->isValid()) {
            $searchText = $formSearch->get('title')->getData();
            $query = $this->postRepository->findPostByTitleOrSummary($searchText);

            $pagination = $this->paginator->paginate($query, $request->query->getInt('page', 1), 3);

            return $this->render('post/post_search.html.twig', [
                'data' => $pagination,
            ]);
        }

        throw $this->createNotFoundException('Page not found!');
    }
}
