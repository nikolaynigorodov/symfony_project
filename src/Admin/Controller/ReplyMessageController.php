<?php

declare(strict_types=1);

namespace Future\Blog\Admin\Controller;

use Future\Blog\Admin\Dto\ContactReplyMessageDto;
use Future\Blog\Admin\Form\ContactReplyMessageType;
use Future\Blog\Core\Entity\Contact;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ReplyMessageController extends AbstractController
{
    private TranslatorInterface $translator;

    private MailerInterface $mailer;

    public function __construct(TranslatorInterface $translator, MailerInterface $mailer)
    {
        $this->translator = $translator;
        $this->mailer = $mailer;
    }

    public function __invoke(Contact $contact, Request $request): Response
    {
        $contactReplyMessageDto = new ContactReplyMessageDto();
        $contactReplyMessageDto->setEmail($contact->getEmail());
        $form = $this->createForm(ContactReplyMessageType::class, $contactReplyMessageDto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->sendMessage($contactReplyMessageDto);
            $this->addFlash('success', $this->translator->trans('admin.reply_message_success'));

            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->render('admin/contact_reply_messages.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    private function sendMessage(ContactReplyMessageDto $contactReplyMessageDto): void
    {
        $email = (new TemplatedEmail())
            ->from('future-blog@email.com')
            ->to($contactReplyMessageDto->getEmail())
            ->subject('Reply to the message')
            ->text('Reply message')
            ->htmlTemplate('admin/contact/reply_message_email.html.twig')

            ->context([
                'data' => $contactReplyMessageDto->getMessage(),
            ])
        ;

        $this->mailer->send($email);
    }
}
