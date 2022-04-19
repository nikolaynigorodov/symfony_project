<?php

declare(strict_types=1);

namespace Future\Blog\User\Controller;

use Future\Blog\User\Dto\PasswordResetGetPasswordDto;
use Future\Blog\User\Entity\TokenConfirm;
use Future\Blog\User\Form\PasswordResetGetPasswordType;
use Future\Blog\User\Repository\TokenConfirmRepository;
use Future\Blog\User\UserManager\UserManager;
use Future\Blog\User\UserManager\UserTokenManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserPasswordResetConfirmController extends AbstractController
{
    private TranslatorInterface $translator;

    private UserTokenManager $userTokenManager;

    private TokenConfirmRepository $tokenConfirmRepository;

    private UserManager $userManager;

    public function __construct(UserTokenManager $userTokenManager, TranslatorInterface $translator, TokenConfirmRepository $tokenConfirmRepository, UserManager $userManager)
    {
        $this->userTokenManager = $userTokenManager;
        $this->translator = $translator;
        $this->tokenConfirmRepository = $tokenConfirmRepository;
        $this->userManager = $userManager;
    }

    public function __invoke(string $token, Request $request): Response
    {
        $userToken = $this->tokenConfirmRepository->findOneByTypeAndHash(
            TokenConfirm::PASSWORD_RESET,
            $token
        );

        if ($userToken && !$this->userTokenManager->isAllowForUser($userToken->getUser()) && $this->userTokenManager->getDiffDate($userToken)) {
            $dto = new PasswordResetGetPasswordDto();
            $form = $this->createForm(PasswordResetGetPasswordType::class, $dto);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $changePassword = $this->userManager->changePassword($dto, $userToken);
                if ($changePassword) {
                    $this->userTokenManager->removeTokenConfirm($userToken);
                    $this->addFlash('success', $this->translator->trans('confirm.password_reset.message_success'));
                } else {
                    $this->addFlash('success', $this->translator->trans('confirm.password_reset.message_error'));
                }

                return $this->redirectToRoute('app_login');
            }

            return $this->render('user/token_confirm/password_reset_confirm.html.twig', [
                'form' => $form->createView(),
            ]);
        }

        throw $this->createNotFoundException('Page not found!');
    }
}
