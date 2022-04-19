<?php

declare(strict_types=1);

namespace Future\Blog\Stripe\Manager;

use Future\Blog\Core\Entity\User;
use Stripe\Checkout\Session;
use Stripe\Customer;
use Stripe\Stripe;
use Stripe\StripeClient;

class StripeCli
{
    private string $stripeSk;

    public function __construct(string $stripeSk)
    {
        $this->stripeSk = $stripeSk;
    }

    public function stripeCreateSession(
        string $lookup_key,
        Customer $customer,
        string $success_url,
        string $cancel_url
    ): Session {
        Stripe::setApiKey($this->stripeSk);

        return \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price' => $lookup_key,
                'quantity' => 1,
            ]],
            'customer' => $customer,
            'mode' => 'subscription',
            'success_url' => $success_url,
            'cancel_url' => $cancel_url,
        ]);
    }

    public function stripeCreateCustomer(User $user): Customer
    {
        $stripe = $this->stripeGetClient();

        return $stripe->customers->create([
            'description' => 'My First Test Customer (created for API docs)',
            'email' => $user->getEmail(),
            'name' => $user->getFullName(),
        ]);
    }

    public function stripeGetCustomer(string $customerId): Customer
    {
        $stripe = $this->stripeGetClient();

        return $stripe->customers->retrieve(
            $customerId,
        );
    }

    public function stripeGetClient(): StripeClient
    {
        return new \Stripe\StripeClient(
            $this->stripeSk
        );
    }
}
