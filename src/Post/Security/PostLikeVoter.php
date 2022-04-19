<?php

declare(strict_types=1);

namespace Future\Blog\Post\Security;

use Future\Blog\Core\Entity\User;
use Future\Blog\Post\Entity\Post;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class PostLikeVoter extends Voter
{
    public const POST_LIKE = 'post_like';

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!\in_array($attribute, [self::POST_LIKE], true)) {
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
            case self::POST_LIKE:
                return $this->canPostLike($post, $user);

                break;
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canPostLike($post, $user)
    {
        return ($user->getId() !== $post->getOwner()->getId()) ? true : false;
    }
}
