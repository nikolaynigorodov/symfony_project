<?php

declare(strict_types=1);

namespace Future\Blog\Post\Command;

use Doctrine\ORM\EntityManagerInterface;
use Future\Blog\Post\Entity\Post;
use Future\Blog\Post\Repository\PostRepository;
use Future\Blog\User\Entity\Subscription;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PostChangeStatusDelayedCommand extends Command
{
    private EntityManagerInterface $em;

    private PostRepository $postRepository;

    public function __construct(EntityManagerInterface $em, PostRepository $postRepository)
    {
        parent::__construct();
        $this->em = $em;
        $this->postRepository = $postRepository;
    }

    // the name of the command (the part after "bin/console")
    // protected static $defaultName = 'app:send-subscription';

    protected function configure(): void
    {
        $this
            ->setName('post:change-status-delayed')
            ->setDescription('Some Description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $nowDateTime = new \DateTime('now');
        $postStatusDelayed = $this->postRepository->findByStatusDelayed($nowDateTime);
        if ($postStatusDelayed) {
            /**
             * @var Post $post
             */
            foreach ($postStatusDelayed as $post) {
                $post->setStatus(Post::POST_STATUS_PUBLISHED);
                $post->setCreatedAt($post->getPublishingDate());
            }
            $this->em->flush();
        }

        $output->writeln('Post status change Published');

        return Command::SUCCESS;
    }
}
