<?php

declare(strict_types=1);

namespace Future\Blog\User\Command;

use Doctrine\ORM\EntityManagerInterface;
use Future\Blog\User\Entity\Subscription;
use Future\Blog\User\Repository\TokenConfirmRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ClearTokenConfirmCommand extends Command
{
    private EntityManagerInterface $em;

    private TokenConfirmRepository $tokenConfirmRepository;

    private int $timeToEndConfirm;

    public function __construct(EntityManagerInterface $em, TokenConfirmRepository $tokenConfirmRepository, int $timeToEndConfirm)
    {
        parent::__construct();
        $this->em = $em;
        $this->tokenConfirmRepository = $tokenConfirmRepository;
        $this->timeToEndConfirm = $timeToEndConfirm;
    }

    // the name of the command (the part after "bin/console")
    // protected static $defaultName = 'app:send-subscription';

    protected function configure(): void
    {
        $this
            ->setName('token-confirm:clear')
            ->setDescription('Some Description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $findTokenConfirm = $this->tokenConfirmRepository->findByDateTime(new \DateTime('- ' . $this->timeToEndConfirm . ' hour'));

        if ($findTokenConfirm) {
            foreach ($findTokenConfirm as $tokenConfirm) {
                $this->em->remove($tokenConfirm);
            }
            $this->em->flush();
        }

        $output->writeln('Clear TokenConfirm');

        return Command::SUCCESS;
    }
}
