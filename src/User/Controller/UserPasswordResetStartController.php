<?php

declare(strict_types=1);

namespace Future\Blog\User\Controller;

use Future\Blog\Core\Repository\UserRepository;
use Future\Blog\User\Dto\UserEmailResetGetEmailDto;
use Future\Blog\User\Entity\TokenConfirm;
use Future\Blog\User\Form\UserResetGetEmailType;
use Future\Blog\User\UserManager\UserTokenManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserPasswordResetStartController extends AbstractController
{
    private TranslatorInterface $translator;

    private UserRepository $userRepository;

    private UserTokenManager $userTokenManager;

    public function __construct(
        UserRepository $userRepository,
        UserTokenManager $userTokenManager,
        TranslatorInterface $translator
    ) {
        $this->translator = $translator;
        $this->userRepository = $userRepository;
        $this->userTokenManager = $userTokenManager;
    }

    public function __invoke(Request $request): Response
    {
        $resetPasswordEmail = new UserEmailResetGetEmailDto();
        $form = $this->createForm(UserResetGetEmailType::class, $resetPasswordEmail);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->userRepository->findOneByEmail($resetPasswordEmail->getEmail());
            if ($user) {
                $this->userTokenManager->passwordReset($user, TokenConfirm::PASSWORD_RESET);
                $this->addFlash('success', $this->translator->trans('user_password_reset_message_success'));

                return $this->redirectToRoute('app_login');
            }
        }

        return $this->render('user/token_confirm/password_reset.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
