<?php

declare(strict_types=1);

namespace Future\Blog\User\Security;

use Future\Blog\Core\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserProfileEditVoter extends Voter
{
    public const USER_PROFILE_EDIT = 'user_profile_edit';

    /**
     * @param User $subject
     */
    protected function supports(string $attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!\in_array($attribute, [self::USER_PROFILE_EDIT], true)) {
            return false;
        }

        if (!$subject instanceof User) {
            return false;
        }

        return true;
    }

    /**
     * @param User $subject
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {
        if (!$subject instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        $user = $subject;
        $currentUser = $token->getUser();
        if (!$currentUser) {
            return false;
        }

        switch ($attribute) {
            case self::USER_PROFILE_EDIT:
                return $this->canEdit($user, $currentUser);

                break;
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canEdit(User $user, $currentUser)
    {
        return $user->getId() === $currentUser->getId();
    }
}
