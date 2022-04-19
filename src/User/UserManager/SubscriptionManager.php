<?php

declare(strict_types=1);

namespace Future\Blog\User\UserManager;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Future\Blog\User\Dto\SubscriptionDto;
use Future\Blog\User\Entity\Subscription;
use Psr\Container\ContainerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class SubscriptionManager
{
    private EntityManagerInterface $em;

    private ContainerInterface $container;

    private TokenStorageInterface $storage;

    public function __construct(
        EntityManagerInterface $em,
        ContainerInterface $container,
        TokenStorageInterface $storage
    ) {
        $this->em = $em;
        $this->container = $container;
        $this->storage = $storage;
    }

    /**
     *  @return Subscription
     */
    public function saveSubscription(SubscriptionDto $subscriptionDto): void
    {
        $token = $this->storage->getToken();
        $user = $token->getUser();
        if ($subscriptionDto->getCategory()) {
            $now = new \DateTime();
            $subscription = new Subscription();
            $subscription->setOwner($user);
            $subscription->setUpdatedAt($now);
            $this->saveSubscriptionCategory($subscription, $subscriptionDto->getCategory());
            $this->em->persist($subscription);
            $this->em->flush();
        }
    }

    public function deleteSubscription(array $entities): void
    {
        foreach ($entities as $entity) {
            $this->em->remove($entity);
        }
        $this->em->flush();
    }

    protected function saveSubscriptionCategory(Subscription $subscription, ArrayCollection $categories): void
    {
        if (!$categories) {
            return;
        }

        foreach ($categories as $category) {
            $subscription->addCategory($category);
        }
    }
}
