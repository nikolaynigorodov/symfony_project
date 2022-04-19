<?php

declare(strict_types=1);

namespace Future\Blog\Core\Controller;

use Future\Blog\Core\Dto\ContactDto;
use Future\Blog\Core\Form\ContactCreateForUserType;
use Future\Blog\Core\Form\ContactCreateType;
use Future\Blog\Core\Manager\ContactManager;
use Future\Blog\Core\SpamChecker\SpamChecker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class ContactCreateController extends AbstractController
{
    private TranslatorInterface $translator;

    private ContactManager $contactManager;

    private SpamChecker $spamChecker;

    private string $akismetKey;

    public function __construct(
        TranslatorInterface $translator,
        ContactManager $contactManager,
        SpamChecker $spamChecker
    ) {
        $this->translator = $translator;
        $this->contactManager = $contactManager;
        $this->spamChecker = $spamChecker;
        $this->akismetKey = $_ENV['SPAM_CHECKER_VALUE_IS_TEST'];
    }

    public function __invoke(Request $request): Response
    {
        $contact = new ContactDto();
        if ($this->getUser()) {
            $contact->setEmail($this->getUser()->getEmail());
            $contact->setName($this->getUser()->getFullName());
            $form = $this->createForm(ContactCreateForUserType::class, $contact);
        } else {
            $form = $this->createForm(ContactCreateType::class, $contact);
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $context = [
                'user_ip' => $request->getClientIp(),
                'blog' => $request->getHost(),
                'is_test' => $this->akismetKey,
            ];
            $spamChecker = $this->spamChecker->getSpamScore($contact, $context);
            if ($spamChecker === $this->spamChecker::SPAM || $spamChecker === $this->spamChecker::BLATANT_SPAM) {
                $this->addFlash('success', $this->translator->trans('contact.create.spam.message_error'));

                return $this->redirectToRoute('core_contact_create');
            }
            $this->contactManager->saveContact($contact);
            $this->addFlash('success', $this->translator->trans('contact.create.success'));

            return $this->redirectToRoute('post_post_all_show');
        }

        return $this->render('core/contact/contact_create.html.twig', [
            'contact_create' => $form->createView(),
        ]);
    }
}
