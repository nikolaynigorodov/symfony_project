<?php

declare(strict_types=1);

namespace Future\Blog\Post\Controller;

use Future\Blog\Post\Dto\CommentCreateDto;
use Future\Blog\Post\Entity\Post;
use Future\Blog\Post\Form\CommentCreateType;
use Future\Blog\Post\Manager\CommentManager;
use Future\Blog\Post\Manager\PostLikesManager;
use Future\Blog\Post\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class PostShowController extends AbstractController
{
    private CommentManager $commentManager;

    private TranslatorInterface $translator;

    private CommentRepository $commentRepository;

    private PostLikesManager $postLikesManager;

    public function __construct(
        CommentManager $commentManager,
        CommentRepository $commentRepository,
        TranslatorInterface $translator,
        PostLikesManager $postLikesManager
    ) {
        $this->commentManager = $commentManager;
        $this->translator = $translator;
        $this->commentRepository = $commentRepository;
        $this->postLikesManager = $postLikesManager;
    }

    public function __invoke(Post $post, Request $request): Response
    {
        $commentDto = new CommentCreateDto();
        $formCreateComment = $this->createForm(CommentCreateType::class, $commentDto);
        $formCreateComment->handleRequest($request);
        if ($formCreateComment->isSubmitted() && $formCreateComment->isValid()) {
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            $this->commentManager->saveComment($commentDto, $this->getUser(), $post);
            $this->addFlash('success', $this->translator->trans('post.create_comment_message'));

            return $this->redirect($request->getUri());
        }
        $comments = $this->commentRepository->findCommentsWithStatusApproved($post);
        $checkUserLikes = $this->postLikesManager->checkPostLikes($this->getUser(), $post);
        $usersLikes = $post->getPostLikes()->count();
        // $usersLikes = $this->postLikesManager->findAllLikes($post);

        return $this->render('post/post_show.html.twig', [
            'post' => $post,
            'comments' => $comments,
            'likes' => $usersLikes,
            'checkUserLikes' => $checkUserLikes,
            'form_comment_create' => $formCreateComment->createView(),
        ]);
    }
}
