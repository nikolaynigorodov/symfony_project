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
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserEmailResetConfirmController extends AbstractController
{
    private TranslatorInterface $translator;

    private UserTokenManager $userTokenManager;

    private TokenConfirmRepository $tokenConfirmRepository;

    private UserManager $userManager;

    private TokenStorageInterface $tokenStorage;

    public function __construct(UserTokenManager $userTokenManager, TranslatorInterface $translator, TokenConfirmRepository $tokenConfirmRepository, UserManager $userManager, TokenStorageInterface $tokenStorage)
    {
        $this->userTokenManager = $userTokenManager;
        $this->translator = $translator;
        $this->tokenConfirmRepository = $tokenConfirmRepository;
        $this->userManager = $userManager;
        $this->tokenStorage = $tokenStorage;
    }

    public function __invoke(string $token, Request $request): Response
    {
        $userToken = $this->tokenConfirmRepository->findOneByTypeAndHash(
            TokenConfirm::EMAIL_RESET,
            $token
        );

        if ($userToken && !$this->userTokenManager->isAllowForUser($userToken->getUser()) && $this->userTokenManager->getDiffDate($userToken)) {
            $changeEmail = $this->userManager->changeEmail($userToken);
            if ($changeEmail) {
                $this->userTokenManager->removeTokenConfirm($userToken);
                $this->tokenStorage->setToken();
                $this->addFlash('success', $this->translator->trans('confirm.email_reset.message_success'));
            } else {
                $this->addFlash('success', $this->translator->trans('confirm.email_reset.message_error'));
            }

            return $this->redirectToRoute('app_login');
        }

        throw $this->createNotFoundException('Page not found!');
    }
}
