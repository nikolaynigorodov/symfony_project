<?php

declare(strict_types=1);

namespace Future\Blog\User\UserManager;

use Doctrine\ORM\EntityManagerInterface;
use Future\Blog\Core\Entity\User;
use Future\Blog\Stripe\Manager\StripeCli;

class StripeUserManager
{
    private EntityManagerInterface $em;

    private StripeCli $stripeCli;

    public function __construct(EntityManagerInterface $em, stripeCli $stripeCli)
    {
        $this->em = $em;
        $this->stripeCli = $stripeCli;
    }

    public function saveStripeCustomerId(User $user, string $customerId): void
    {
        if ($customerId) {
            $user->setStripeCustomerId($customerId);
            $this->em->flush();
        }
    }

    public function checkCustomerId(User $user)
    {
        if ($user->getStripeCustomerId()) {
            $customer = $this->stripeCli->stripeGetCustomer($user->getStripeCustomerId());
        } else {
            $customer = $this->stripeCli->stripeCreateCustomer($user);
            if ($customer->id) {
                $this->saveStripeCustomerId($user, $customer->id);
            }
        }

        return $customer;
    }
}
