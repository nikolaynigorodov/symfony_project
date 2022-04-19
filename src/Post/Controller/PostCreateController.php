<?php

declare(strict_types=1);

namespace Future\Blog\Post\Controller;

use Future\Blog\Post\Dto\PostDraftDto;
use Future\Blog\Post\Dto\PostDto;
use Future\Blog\Post\Entity\Post;
use Future\Blog\Post\Form\PostCreateType;
use Future\Blog\Post\Manager\PostManager;
use Future\Blog\Stripe\Manager\UserPostCreateChecker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class PostCreateController extends AbstractController
{
    private TranslatorInterface $translator;

    private PostManager $postManager;

    private UserPostCreateChecker $subscriptionPayCheck;

    public function __construct(
        TranslatorInterface $translator,
        PostManager $postManager,
        UserPostCreateChecker $subscriptionPayCheck
    ) {
        $this->translator = $translator;
        $this->postManager = $postManager;
        $this->subscriptionPayCheck = $subscriptionPayCheck;
    }

    public function __invoke(Request $request): Response
    {
        $this->denyAccessUnlessGranted('create_post');
        $userCheckPay = $this->subscriptionPayCheck->userCheck($this->getUser());
        if (!$userCheckPay) {
            $this->addFlash('success', $this->translator->trans('post.create.need_subscription_pay'));

            return $this->redirectToRoute('stripe_start');
        }
        $form = $this->createForm(PostCreateType::class);
        $form->handleRequest($request);
        $saveClicked = !$form->isSubmitted() || $form->get('save')->isClicked();

        $dto = $saveClicked ? new PostDto() : new PostDraftDto();
        $formPublished = $this->createForm(PostCreateType::class, $dto);
        $formPublished->handleRequest($request);

        if ($formPublished->isSubmitted() && $formPublished->isValid()) {
            if ($saveClicked) {
                $date = $dto->getPublishingDate();
                if ($date !== null) {
                    $this->postManager->savePost($dto, $this->getUser(), Post::POST_STATUS_DELAYED);
                    $this->addFlash('success', $this->translator->trans('post.create.success'));

                    return $this->redirectToRoute('post_post_all_show');
                }

                $newPost = $this->postManager->savePost($dto, $this->getUser(), Post::POST_STATUS_PUBLISHED);
                $this->addFlash('success', $this->translator->trans('post.create.success'));

                return $this->redirectToRoute('post_post_show', ['id' => $newPost->getId()]);
            }
            $this->postManager->savePostStatusDraft($dto, $this->getUser(), Post::POST_STATUS_DRAFT);
            $this->addFlash('success', $this->translator->trans('post.create.success'));

            return $this->redirectToRoute('post_post_all_show');
        }

        return $this->render('post/post_create.html.twig', [
            'post_create' => $formPublished->createView(),
        ]);
    }
}
