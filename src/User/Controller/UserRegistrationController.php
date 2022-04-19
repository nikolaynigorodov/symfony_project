<?php

declare(strict_types=1);

namespace Future\Blog\User\Controller;

use Future\Blog\User\Dto\UserRegistration;
use Future\Blog\User\Entity\TokenConfirm;
use Future\Blog\User\Form\UserRegistrationType;
use Future\Blog\User\UserManager\UserManager;
use Future\Blog\User\UserManager\UserTokenManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserRegistrationController extends AbstractController
{
    private UserManager $userManager;

    private TranslatorInterface $translator;

    private UserTokenManager $userTokenManager;

    public function __construct(
        UserManager $userManager,
        TranslatorInterface $translator,
        UserTokenManager $userTokenManager
    ) {
        $this->userManager = $userManager;
        $this->translator = $translator;
        $this->userTokenManager = $userTokenManager;
    }

    public function __invoke(Request $request): Response
    {
        $userRegistration = new UserRegistration();
        $form = $this->createForm(UserRegistrationType::class, $userRegistration);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->userManager->create($userRegistration, false);
            $this->userTokenManager->emailConfirm($user, TokenConfirm::EMAIL_CONFIRM);
            $this->addFlash('success', $this->translator->trans('registered.success'));

            return $this->redirectToRoute('post_post_all_show');
        }

        return $this->render('user/registration.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
