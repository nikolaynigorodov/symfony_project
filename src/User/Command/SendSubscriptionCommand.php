<?php

declare(strict_types=1);

namespace Future\Blog\User\Command;

use Doctrine\ORM\EntityManagerInterface;
use Future\Blog\Post\Entity\Post;
use Future\Blog\Post\Repository\PostRepository;
use Future\Blog\User\Entity\Subscription;
use Future\Blog\User\Repository\SubscriptionRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\MailerInterface;

class SendSubscriptionCommand extends Command
{
    private SubscriptionRepository $subscriptionRepository;

    private PostRepository $postRepository;

    private MailerInterface $mailer;

    private EntityManagerInterface $em;

    public function __construct(
        PostRepository $postRepository,
        SubscriptionRepository $subscriptionRepository,
        MailerInterface $mailer,
        EntityManagerInterface $em
    ) {
        parent::__construct();
        $this->subscriptionRepository = $subscriptionRepository;
        $this->postRepository = $postRepository;
        $this->mailer = $mailer;
        $this->em = $em;
    }

    public function updateSubscribeTime(Subscription $subscription): void
    {
        $time = new \DateTime();
        $subscription->setUpdatedAt($time);
        $this->em->flush();
    }

    // the name of the command (the part after "bin/console")
    // protected static $defaultName = 'app:send-subscription';

    protected function configure(): void
    {
        $this
            ->setName('user:send-subscription')
            ->setDescription('Some Description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $allSubscription = $this->subscriptionRepository->findAll();

        if ($allSubscription) {
            foreach ($allSubscription as $subscription) {
                $posts = $this->postRepository->findBySubscription($subscription, Post::POST_STATUS_PUBLISHED);
                if ($posts) {
                    $email = (new TemplatedEmail())
                        ->from('future-blog@email.com')
                        ->to($subscription->getOwner()->getEmail())
                        ->subject('New Post')
                        ->text('New posts on your subscription')
                        ->htmlTemplate('user/subscription/subscription_email.html.twig')

                        // pass variables (name => value) to the template
                        ->context([
                            'data' => $posts,
                        ])
                    ;

                    $this->mailer->send($email);
                    $this->updateSubscribeTime($subscription);
                }
            }
        }

        $output->writeln('Email Send From Subscription');

        return Command::SUCCESS;
    }
}
