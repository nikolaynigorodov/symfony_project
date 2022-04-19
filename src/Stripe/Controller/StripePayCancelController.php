<?php

declare(strict_types=1);

namespace Future\Blog\Stripe\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class StripePayCancelController extends AbstractController
{
    public function __invoke(Request $request): Response
    {
        return $this->render('stripe/stripe_cancel.html.twig', [
            'user' => $this->getUser(),
        ]);
    }
}
