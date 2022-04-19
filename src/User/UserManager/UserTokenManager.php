<?php

declare(strict_types=1);

namespace Future\Blog\User\UserManager;

use Doctrine\ORM\EntityManagerInterface;
use Future\Blog\Core\Entity\User;
use Future\Blog\User\Entity\TokenConfirm;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class UserTokenManager
{
    private int $timeToEndConfirm;

    private EntityManagerInterface $em;

    private TokenGeneratorInterface $tokenGenerator;

    private UserTokenEmailManager $emailManager;

    public function __construct(
        EntityManagerInterface $em,
        UserTokenEmailManager $emailManager,
        TokenGeneratorInterface $tokenGenerator,
        int $timeToEndConfirm
    ) {
        $this->em = $em;
        $this->emailManager = $emailManager;
        $this->tokenGenerator = $tokenGenerator;
        $this->timeToEndConfirm = $timeToEndConfirm;
    }

    /**
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function emailConfirm(User $user, int $type): void
    {
        $token = $this->saveToken($user, $type);
        $this->emailManager->sendConfirmEmail($user, $token);
    }

    /**
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function passwordReset(User $user, int $type): void
    {
        $token = $this->saveToken($user, $type);
        $this->emailManager->sendResetPasswordEmail($user, $token);
    }

    /**
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function emailChange(User $user, int $type, string $changeEmail): void
    {
        $token = $this->saveToken($user, $type, $changeEmail);
        $this->emailManager->sendResetEmail($user, $token, $changeEmail);
    }

    public function saveToken(User $user, int $type, string $changeEmail = null): string
    {
        $token = $this->getGenerateToken();
        $tokenConfirmEntity = new TokenConfirm($user, $token, $type, $changeEmail);
        $this->em->persist($tokenConfirmEntity);
        $this->em->flush();

        return $token;
    }

    public function isAllowForUser(User $user): bool
    {
        return $user->isActive();
    }

    public function getDiffDate(TokenConfirm $tokenConfirm): bool
    {
        $nowDate = new \DateTime('now');

        /** @var \DateInterval $diff */
        $diff = date_diff($nowDate, $tokenConfirm->getCreatedAt());

        return ($diff->h < $this->timeToEndConfirm) ? true : false;
    }

    public function removeTokenConfirm(TokenConfirm $tokenConfirm): void
    {
        $this->em->remove($tokenConfirm);
        $this->em->flush();
    }

    /**
     * @return string
     */
    private function getGenerateToken()
    {
        return $this->tokenGenerator->generateToken();
    }
}
