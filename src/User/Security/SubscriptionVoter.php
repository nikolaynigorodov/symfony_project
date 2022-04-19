<?php

declare(strict_types=1);

namespace Future\Blog\User\Security;

use Future\Blog\Core\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class SubscriptionVoter extends Voter
{
    public const SUBSCRIPTION = 'subscription';

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!\in_array($attribute, [self::SUBSCRIPTION], true)) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        switch ($attribute) {
            case self::SUBSCRIPTION:
                return $this->canSubscription($token, $user);

                break;
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canSubscription($token, $user)
    {
        return ($this->security->isGranted('ROLE_USER')) ? true : false;
    }
}
