<?php

declare(strict_types=1);

namespace Future\Blog\Stripe\Controller;

use Future\Blog\Stripe\Manager\StripeCli;
use Future\Blog\User\UserManager\StripeUserManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class StripePayCreateCheckoutSessionController extends AbstractController
{
    private StripeCli $stripeCli;

    private StripeUserManager $stripeUserManager;

    private string $lookupKey;

    public function __construct(StripeCli $stripeCli, StripeUserManager $stripeUserManager, string $lookupKey)
    {
        $this->stripeCli = $stripeCli;
        $this->stripeUserManager = $stripeUserManager;
        $this->lookupKey = $lookupKey;
    }

    public function __invoke(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        // $lookup_key = $request->request->get('lookup_key');
        $lookup_key = $this->lookupKey;
        $success_url = $this->generateUrl('stripe_success_url', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $cancel_url = $this->generateUrl('stripe_cancel_url', [], UrlGeneratorInterface::ABSOLUTE_URL);

        $customer = $this->stripeUserManager->checkCustomerId($this->getUser());
        $checkout_session = $this->stripeCli->stripeCreateSession($lookup_key, $customer, $success_url, $cancel_url);

        return $this->redirect($checkout_session->url, 303);
    }
}
