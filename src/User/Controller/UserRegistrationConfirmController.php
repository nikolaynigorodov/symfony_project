<?php

declare(strict_types=1);

namespace Future\Blog\User\Controller;

use Future\Blog\User\Entity\TokenConfirm;
use Future\Blog\User\Repository\TokenConfirmRepository;
use Future\Blog\User\UserManager\UserManager;
use Future\Blog\User\UserManager\UserTokenManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserRegistrationConfirmController extends AbstractController
{
    private TranslatorInterface $translator;

    private UserTokenManager $userTokenManager;

    private UserManager $userManager;

    private TokenConfirmRepository $tokenConfirmRepository;

    public function __construct(UserTokenManager $userTokenManager, TranslatorInterface $translator, UserManager $userManager, TokenConfirmRepository $tokenConfirmRepository)
    {
        $this->userTokenManager = $userTokenManager;
        $this->translator = $translator;
        $this->userManager = $userManager;
        $this->tokenConfirmRepository = $tokenConfirmRepository;
    }

    public function __invoke(string $token, Request $request): Response
    {
        $userToken = $this->tokenConfirmRepository->findOneByTypeAndHash(
            TokenConfirm::EMAIL_CONFIRM,
            $token
        );

        if ($userToken && !$this->userTokenManager->isAllowForUser($userToken->getUser()) && $this->userTokenManager->getDiffDate($userToken)) {
            $this->userManager->changeUserActivated($userToken);
            $this->userTokenManager->removeTokenConfirm($userToken);
            $this->addFlash('success', $this->translator->trans('confirm.registered.message_success'));
        }

        return $this->redirectToRoute('post_post_all_show');
    }
}
