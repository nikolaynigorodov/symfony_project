<?php

declare(strict_types=1);

namespace Future\Blog\Post\Security;

use Future\Blog\Core\Entity\User;
use Future\Blog\Post\Entity\Post;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class PostEditVoter extends Voter
{
    public const EDIT = 'edit';
    public const DELETE = 'delete';

    /**
     * @param Post $subject
     */
    protected function supports(string $attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!\in_array($attribute, [self::EDIT, self::DELETE], true)) {
            return false;
        }

        if (!$subject instanceof Post) {
            return false;
        }

        return true;
    }

    /**
     * @param Post $subject
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        $post = $subject;

        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($post, $user);

                break;

            case self::DELETE:
                return $this->canEdit($post, $user);

                break;
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canEdit(Post $post, User $user): bool
    {
        return ($post->getOwner()->getId() === $user->getId() && $post->getStatus() !== Post::POST_STATUS_BLOCKED) ? true : false;
    }
}
