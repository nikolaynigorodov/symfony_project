<?php

declare(strict_types=1);

namespace Future\Blog\Stripe\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Future\Blog\Core\Entity\User;
use Future\Blog\Core\Repository\UserRepository;
use Future\Blog\Stripe\Entity\SubscriptionPay;
use Future\Blog\Stripe\Repository\SubscriptionPayRepository;
use Psr\Log\LoggerInterface;
use Stripe\Subscription;

class SubscriptionPayManager
{
    private EntityManagerInterface $em;

    private UserRepository $userRepository;

    private SubscriptionPayRepository $subscriptionPayRepository;

    private LoggerInterface $logger;

    public function __construct(
        EntityManagerInterface $em,
        UserRepository $userRepository,
        SubscriptionPayRepository $subscriptionPayRepository,
        LoggerInterface $logger
    ) {
        $this->em = $em;
        $this->userRepository = $userRepository;
        $this->subscriptionPayRepository = $subscriptionPayRepository;
        $this->logger = $logger;
    }

    public function saveStripeSubscriptionPay(
        User $user,
        int $periodStart,
        int $periodEnd,
        string $subscriptionId
    ): void {
        $start = new \DateTime();
        $finish = new \DateTime();
        if ($user->getSubscriptionPay()) {
            $this->em->remove($user->getSubscriptionPay());
            $this->em->flush();
            $this->em->refresh($user);
        }
        $subscriptionPay = new SubscriptionPay($user, $subscriptionId, $start->setTimestamp($periodStart), $finish->setTimestamp($periodEnd));
        $this->em->persist($subscriptionPay);
        $user->setSubscriptionPayCheck(true);
        $this->em->flush();
    }

    public function userSaveSubscriptionPay(Subscription $paymentIntent): void
    {
        $customer = $paymentIntent->customer;
        $user = $this->userRepository->findOneBy(['stripeCustomerId' => $customer]);
        if ($user) {
            $subscriptionId = $paymentIntent->id;
            $periodStart = $paymentIntent->current_period_start;
            $periodEnd = $paymentIntent->current_period_end;
            $this->saveStripeSubscriptionPay($user, $periodStart, $periodEnd, $subscriptionId);
        } else {
            $this->logger->error('Stripe userSaveSubscriptionPay not found user on id', [
                'customer' => $customer,
            ]);
        }
    }

    public function deleteSubscriptionPay(Subscription $paymentIntent): void
    {
        $subscriptionPay = $this->subscriptionPayRepository->findOneBy(['stripe_subscription_id' => $paymentIntent->id]);

        if ($subscriptionPay) {
            $user = $subscriptionPay->getUser();
            $user->setSubscriptionPayCheck(false);
            $this->em->flush();
        } else {
            $this->logger->error('Stripe deleteSubscriptionPay not found subscription on paymentId.', [
                'paymentId' => $paymentIntent->id,
                'payment' => $paymentIntent,
            ]);
        }
    }

    public function updateSubscriptionPay(Subscription $paymentIntent): void
    {
        $subscriptionPay = $this->subscriptionPayRepository->findOneBy(['stripeSubscriptionId' => $paymentIntent->id]);
        if ($subscriptionPay) {
            $this->fillInDateOnUpdateSubscriptionPay($paymentIntent, $subscriptionPay);
        } else {
            $this->logger->error('Stripe updateSubscriptionPay not found subscription on paymentId.', [
                'paymentId' => $paymentIntent->id,
                'payment' => $paymentIntent,
            ]);
        }
    }

    public function fillInDateOnUpdateSubscriptionPay(
        Subscription $paymentIntent,
        SubscriptionPay $subscriptionPay
    ): void {
        $start = new \DateTime();
        $finish = new \DateTime();

        $periodStart = $paymentIntent->current_period_start;
        $periodEnd = $paymentIntent->current_period_end;
        $subscriptionPay->setStart($start->setTimestamp($periodStart));
        $subscriptionPay->setFinish($finish->setTimestamp($periodEnd));
        $user = $subscriptionPay->getUser();
        $user->setSubscriptionPayCheck(true);
        $this->em->flush();
    }
}
