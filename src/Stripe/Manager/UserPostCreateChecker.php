<?php

declare(strict_types=1);

namespace Future\Blog\Stripe\Manager;

use Future\Blog\Core\Entity\User;
use Future\Blog\Post\Entity\Post;
use Future\Blog\Post\Repository\PostRepository;

class UserPostCreateChecker
{
    private PostRepository $postRepository;

    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    public function userCheck(User $user): bool
    {
        $checkUserSubscriptionPay = $this->checkUserSubscriptionPay($user);
        if (!$checkUserSubscriptionPay) { // If not subscriptionPay
            $firstDayMonth = new \DateTime('first day of this month 00:00:00');
            $lastDayMonth = new \DateTime('last day of this month 00:00:00');
            $post = $this->postRepository->findPostsForMonth($user, $firstDayMonth, $lastDayMonth);
            if ($post < Post::SUBSCRIPTION_POST_LIMIT) {
                return true;
            }
        } else { // If there is a paid subscription
            return true;
        }

        return false;
    }

    protected function checkUserSubscriptionPay(User $user): bool
    {
        if ($user->getSubscriptionPay() || $user->getSubscriptionPayCheck()) {
            $nowDay = new \DateTime('now');
            $subscriptionPay = $user->getSubscriptionPay();
            if ($subscriptionPay) {
                return ($nowDay < $subscriptionPay->getFinish()) ? true : false;
            }
        }

        return false;
    }
}
