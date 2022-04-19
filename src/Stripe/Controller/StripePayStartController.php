<?php

declare(strict_types=1);

namespace Future\Blog\Stripe\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class StripePayStartController extends AbstractController
{
    public function __invoke(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        return $this->render('stripe/stripe_start.html.twig', [
            'user' => $this->getUser(),
        ]);
    }
}
