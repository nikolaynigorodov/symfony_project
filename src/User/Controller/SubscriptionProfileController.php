<?php

declare(strict_types=1);

namespace Future\Blog\User\Controller;

use Future\Blog\User\Dto\SubscriptionDto;
use Future\Blog\User\Form\SubscriptionType;
use Future\Blog\User\Repository\SubscriptionRepository;
use Future\Blog\User\UserManager\SubscriptionManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class SubscriptionProfileController extends AbstractController
{
    private TranslatorInterface $translator;

    private SubscriptionRepository $subscriptionRepository;

    private SubscriptionManager $subscriptionManager;

    public function __construct(
        TranslatorInterface $translator,
        SubscriptionRepository $subscriptionRepository,
        SubscriptionManager $subscriptionManager
    ) {
        $this->translator = $translator;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->subscriptionManager = $subscriptionManager;
    }

    public function __invoke(Request $request): Response
    {
        $this->denyAccessUnlessGranted('subscription');

        $subscription_for_owner = $this->subscriptionRepository->findByOwner($this->getUser());
        if (!empty($subscription_for_owner)) {
            $this->subscriptionManager->deleteSubscription($subscription_for_owner);
            $this->addFlash('success', $this->translator->trans('subscribe.delete.success'));

            return $this->redirectToRoute('user_user_information');
        }
        $subscriptionDto = new SubscriptionDto();
        $form = $this->createForm(SubscriptionType::class, $subscriptionDto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->subscriptionManager->saveSubscription($subscriptionDto);

            $this->addFlash('success', $this->translator->trans('subscribe.save.success'));

            return $this->redirectToRoute('user_user_information');
        }

        return $this->render('user/subscription/subscription.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
