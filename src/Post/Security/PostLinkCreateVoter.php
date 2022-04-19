<?php

declare(strict_types=1);

namespace Future\Blog\Post\Security;

use Future\Blog\Core\Entity\User;
use Future\Blog\Stripe\Manager\UserPostCreateChecker;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class PostLinkCreateVoter extends Voter
{
    public const CREATE_POST = 'post_link_create';

    private Security $security;

    private UserPostCreateChecker $postCreateChecker;

    public function __construct(Security $security, UserPostCreateChecker $postCreateChecker)
    {
        $this->security = $security;
        $this->postCreateChecker = $postCreateChecker;
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

        if ($attribute) {
            return $this->canCreate($token, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canCreate($token, $user): bool
    {
        return $this->postCreateChecker->userCheck($user);
    }
}
