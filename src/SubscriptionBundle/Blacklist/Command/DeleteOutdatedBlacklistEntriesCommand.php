<?php

namespace SubscriptionBundle\Blacklist\Command;

use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use SubscriptionBundle\Repository\BlackListRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DeleteOutdatedBlacklistEntriesCommand extends Command
{
    /**
     * @var BlackListRepository
     */
    private $blackListRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * DeleteOutdatedBlacklistEntriesCommand constructor.
     */
    public function __construct(BlackListRepository $blackListRepository, EntityManagerInterface $entityManager)
    {
        $this->blackListRepository = $blackListRepository;
        $this->entityManager       = $entityManager;

        parent::__construct();
    }


    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('subscription:blacklist:delete-outdated-blacklist-entries');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $entries = $this->blackListRepository->findOutdatedEntries(
            new DateTimeImmutable('-6 month')
        );

        $successfullyDeleted = 0;
        foreach ($entries as $entry) {
            try {
                $this->entityManager->remove($entry);
                $successfullyDeleted++;

            } catch (\Exception $exception) {
                $errors[] = $entry->getAlias();
            }
        }

        $errorsIds = empty($errors)
            ? 'no errors occurred'
            : implode(', ', $errors);

        $output->writeln("$successfullyDeleted entries was successfully deleted");
        $output->writeln("Errors: $errorsIds");

        $this->entityManager->flush();
        $this->entityManager->clear();

    }
}
