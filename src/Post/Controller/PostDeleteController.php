<?php

declare(strict_types=1);

namespace Future\Blog\Post\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Future\Blog\Post\Entity\Post;
use Future\Blog\Post\Form\PostDeleteType;
use Future\Blog\Post\Security\PostEditVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class PostDeleteController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    private TranslatorInterface $translator;

    public function __construct(EntityManagerInterface $entityManager, TranslatorInterface $translator)
    {
        $this->entityManager = $entityManager;
        $this->translator = $translator;
    }

    public function __invoke(Post $post, Request $request): Response
    {
        $this->denyAccessUnlessGranted(PostEditVoter::DELETE, $post);

        $form = $this->createForm(PostDeleteType::class, $post);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('yes')->isClicked()) {
                $this->deletePost($post);
                $this->addFlash('success', $this->translator->trans('post.delete_success'));
            }

            return $this->redirectToRoute('user_post_user_posts');
        }

        return $this->render('post/post_delete.html.twig', [
            'post' => $post,
            'post_delete' => $form->createView(),
        ]);
    }

    private function deletePost(Post $post): void
    {
        $this->entityManager->remove($post);
        $this->entityManager->flush();
    }
}
