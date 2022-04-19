<?php

declare(strict_types=1);

namespace Future\Blog\User\UserManager;

use Future\Blog\Core\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class UserTokenEmailManager
{
    private MailerInterface $mailer;

    private string $emailFrom;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
        $this->emailFrom = $_ENV['EMAIL_SEND_FROM'];
    }

    /**
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function sendConfirmEmail(User $user, string $token): void
    {
        $email = (new TemplatedEmail())
            ->from($this->emailFrom)
            ->to($user->getEmail())
            ->subject('Confirm Email')
            ->text('Confirm Email From Registration')
            ->htmlTemplate('user/registration/email/confirm_registration_email.html.twig')

            ->context([
                'token' => $token,
            ])
        ;

        $this->mailer->send($email);
    }

    /**
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function sendResetPasswordEmail(User $user, string $token): void
    {
        $email = (new TemplatedEmail())
            ->from($this->emailFrom)
            ->to($user->getEmail())
            ->subject('Password Reset')
            ->text('Password Reset')
            ->htmlTemplate('user/token_confirm/email/password_reset.html.twig')

            ->context([
                'token' => $token,
            ])
        ;

        $this->mailer->send($email);
    }

    /**
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function sendResetEmail(User $user, string $token, string $newEmail): void
    {
        $email = (new TemplatedEmail())
            ->from($this->emailFrom)
            ->to($user->getEmail())
            ->subject('Email Change')
            ->text('Email Change')
            ->htmlTemplate('user/token_confirm/email/email_reset.html.twig')

            ->context([
                'token' => $token,
                'newEmail' => $newEmail,
            ])
        ;

        $this->mailer->send($email);
    }
}
