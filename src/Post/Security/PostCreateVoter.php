<?php

declare(strict_types=1);

namespace Future\Blog\Post\Security;

use Future\Blog\Core\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class PostCreateVoter extends Voter
{
    public const CREATE_POST = 'create_post';

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject): bool
    {
        // if the attribute isn't one we support, return false
        if (!\in_array($attribute, [self::CREATE_POST], true)) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        switch ($attribute) {
            case self::CREATE_POST:
                return $this->canCreate($token, $user);

                break;
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canCreate($token, $user): bool
    {
        return $this->security->isGranted('ROLE_USER');
    }
}
