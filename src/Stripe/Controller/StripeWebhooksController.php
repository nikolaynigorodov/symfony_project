<?php

declare(strict_types=1);

namespace Future\Blog\Stripe\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Future\Blog\Core\Repository\UserRepository;
use Future\Blog\Stripe\Manager\SubscriptionPayManager;
use Psr\Log\LoggerInterface;
use Stripe\Webhook;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class StripeWebhooksController extends AbstractController
{
    private EntityManagerInterface $em;

    private SubscriptionPayManager $subscriptionPayManager;

    private UserRepository $userRepository;

    private string $endPointSecret;

    private LoggerInterface $logger;

    public function __construct(
        EntityManagerInterface $em,
        SubscriptionPayManager $subscriptionPayManager,
        UserRepository $userRepository,
        string $endPointSecret,
        LoggerInterface $logger
    ) {
        $this->em = $em;
        $this->subscriptionPayManager = $subscriptionPayManager;
        $this->userRepository = $userRepository;
        $this->endPointSecret = $endPointSecret;
        $this->logger = $logger;
    }

    public function __invoke(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->headers->get('Stripe-Signature');
        $event = null;

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                $this->endPointSecret
            );
        } catch (\UnexpectedValueException $e) {
            $this->logger->error('Error for Stripe Webhook.', [
                'type' => $event->type,
                'codeError' => $e->getCode(),
                'messageError' => $e->getMessage(),
            ]);
        }

        switch ($event->type) {
            case 'customer.subscription.created':
                $paymentIntent = $event->data->object;
                $this->subscriptionPayManager->userSaveSubscriptionPay($paymentIntent);

                break;

            case 'customer.subscription.deleted':
                $paymentIntent = $event->data->object;
                $this->subscriptionPayManager->deleteSubscriptionPay($paymentIntent);

                break;

            case 'customer.subscription.updated':
                $paymentIntent = $event->data->object;
                $this->subscriptionPayManager->updateSubscriptionPay($paymentIntent);

                break;

            default:
                $this->logger->error('Unknown webhook type: ' . $event->type);
        }

        return new Response(null, 200);
    }
}
