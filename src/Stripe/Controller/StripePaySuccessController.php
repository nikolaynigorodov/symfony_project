<?php

declare(strict_types=1);

namespace Future\Blog\Stripe\Controller;

use Future\Blog\Stripe\Manager\SubscriptionPayManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class StripePaySuccessController extends AbstractController
{
    private SubscriptionPayManager $subscriptionPayManager;

    public function __construct(SubscriptionPayManager $subscriptionPayManager)
    {
        $this->subscriptionPayManager = $subscriptionPayManager;
    }

    public function __invoke(Request $request): Response
    {
        return $this->render('stripe/stripe_success.html.twig', [
            'user' => $this->getUser(),
        ]);
    }
}
