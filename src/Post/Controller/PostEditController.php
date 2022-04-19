<?php

declare(strict_types=1);

namespace Future\Blog\Post\Controller;

use Future\Blog\Post\Dto\PostDraftDto;
use Future\Blog\Post\Dto\PostDto;
use Future\Blog\Post\Entity\Post;
use Future\Blog\Post\Form\PostEditType;
use Future\Blog\Post\Manager\PostManager;
use Future\Blog\Post\Mapper\PostEditMapper;
use Future\Blog\Post\Security\PostEditVoter;
use Future\Blog\Stripe\Manager\UserPostCreateChecker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class PostEditController extends AbstractController
{
    private TranslatorInterface $translator;

    private PostEditMapper $mapper;

    private PostManager $postManager;

    private UserPostCreateChecker $subscriptionPayCheck;

    public function __construct(
        TranslatorInterface $translator,
        PostEditMapper $mapper,
        PostManager $postManager,
        UserPostCreateChecker $subscriptionPayCheck
    ) {
        $this->translator = $translator;
        $this->mapper = $mapper;
        $this->postManager = $postManager;
        $this->subscriptionPayCheck = $subscriptionPayCheck;
    }

    public function __invoke(Post $post, Request $request): Response
    {
        $this->denyAccessUnlessGranted(PostEditVoter::EDIT, $post);

        $form = $this->createForm(PostEditType::class);
        $form->handleRequest($request);

        $dto = new PostDto();
        $postMapperToDto = $this->mapper->setPostDto($post, $dto);
        if ($form->get('update')->isClicked() || $form->get('saveArchive')->isClicked()) {
            $dto = new PostDto();
            $postMapperToDto = $this->mapper->setPostDto($post, $dto);
        } elseif ($form->get('saveDraft')->isClicked()) {
            $dto = new PostDraftDto();
            $postMapperToDto = $this->mapper->setPostDraftDto($post, $dto);
        }

        $formPublished = $this->createForm(PostEditType::class, $postMapperToDto);

        $formPublished->handleRequest($request);

        if ($formPublished->isSubmitted() && $formPublished->isValid()) {
            if ($post->getStatus() !== Post::POST_STATUS_PUBLISHED) {
                $userCheckCountPost = $this->subscriptionPayCheck->userCheck($this->getUser());
                if (!$userCheckCountPost && $form->get('update')->isClicked()) {
                    $this->addFlash('success', $this->translator->trans('post.create.need_subscription_pay'));

                    return $this->redirectToRoute('stripe_start');
                }
            }

            if ($form->get('update')->isClicked()) { // Save post with status PUBLISHED or DELAYED
                $date = $dto->getPublishingDate();
                if ($date !== null) {
                    $this->postManager->updatePost($post, $postMapperToDto, Post::POST_STATUS_DELAYED);
                    $this->addFlash('success', $this->translator->trans('post.update.success'));
                } else {
                    $this->postManager->updatePost($post, $postMapperToDto);
                    $this->addFlash('success', $this->translator->trans('post.update.success'));

                    return $this->redirectToRoute('post_post_show', ['id' => $post->getId()]);
                }
            } elseif ($form->get('saveArchive')->isClicked()) { // Save post with status ARCHIVED
                $this->postManager->updatePost($post, $postMapperToDto, Post::POST_STATUS_ARCHIVED);
                $this->addFlash('success', $this->translator->trans('post.update.success'));
            } elseif ($form->get('saveDraft')->isClicked()) { // Save post with status DRAFT
                $this->postManager->updatePostStatusDraft($post, $postMapperToDto, Post::POST_STATUS_DRAFT);
                $this->addFlash('success', $this->translator->trans('post.update.success'));
            }

            return $this->redirectToRoute('post_post_all_show');
        }

        return $this->render('post/post_edit.html.twig', [
            'post' => $post,
            'post_edit' => $formPublished->createView(),
        ]);
    }
}
