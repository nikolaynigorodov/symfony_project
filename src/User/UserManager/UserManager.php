<?php

declare(strict_types=1);

namespace Future\Blog\User\UserManager;

use Doctrine\ORM\EntityManagerInterface;
use Future\Blog\Core\Entity\User;
use Future\Blog\User\Dto\PasswordResetGetPasswordDto;
use Future\Blog\User\Dto\UserRegistration;
use Future\Blog\User\Entity\TokenConfirm;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Encoder\SelfSaltingEncoderInterface;

class UserManager
{
    private EncoderFactoryInterface $encoderFactory;

    private EntityManagerInterface $em;

    private MailerInterface $mailer;

    public function __construct(
        EncoderFactoryInterface $encoderFactory,
        EntityManagerInterface $em,
        MailerInterface $mailer
    ) {
        $this->encoderFactory = $encoderFactory;
        $this->em = $em;
        $this->mailer = $mailer;
    }

    public function create(UserRegistration $dto, bool $isActivated = false): User
    {
        [$password, $salt] = $this->getHashedPasswordAndSalt($dto->getPlainPassword());
        $user = new User($dto->getEmail(), $password, $dto->getFirstName(), $dto->getLastName(), ['ROLE_USER']);
        if ($isActivated) {
            $user->setActivated(true);
        }
        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    public function changePassword(PasswordResetGetPasswordDto $dto, TokenConfirm $tokenConfirm): bool
    {
        $user = $tokenConfirm->getUser();
        [$password, $salt] = $this->getHashedPasswordAndSalt($dto->getPassword());
        if ($user && $dto->getPassword()) {
            $user->setPassword($password);
            $this->em->flush();

            return true;
        }

        return false;
    }

    public function changeEmail(TokenConfirm $tokenConfirm): bool
    {
        $user = $tokenConfirm->getUser();
        if ($user) {
            $user->setEmail($tokenConfirm->getEmailReset());
            $this->em->flush();

            return true;
        }

        return false;
    }

    public function changeUserActivated(TokenConfirm $tokenConfirm): void
    {
        $tokenConfirm->getUser()->setActivated(true);
        $this->em->flush();
    }

    private function getHashedPasswordAndSalt(string $plainPassword): array
    {
        $encoder = $this->encoderFactory->getEncoder(User::class);
        $salt = null;

        if (!$encoder instanceof SelfSaltingEncoderInterface) {
            $salt = $this->generateSalt();
        }

        $password = $encoder->encodePassword($plainPassword, $salt);

        return [$password, $salt];
    }

    private function generateSalt(): string
    {
        return base64_encode(random_bytes(30));
    }
}
