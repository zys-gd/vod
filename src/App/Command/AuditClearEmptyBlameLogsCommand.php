<?php

namespace App\Command;

use DataDog\AuditBundle\Entity\AuditLog;
use Doctrine\DBAL\ConnectionException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

class AuditClearEmptyBlameLogsCommand extends Command
{
    /** @var string */
    protected static $defaultName = 'app:audit:clear-empty-blame-logs';

    /** @var EntityManagerInterface */
    private $entityManager;

    /**
     * AuditClearEmptyBlameLogsCommand constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Command that clear log entries with an empty blame_id');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return void
     * @throws ConnectionException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $this->entityManager->getConnection()->beginTransaction();
        try {
            $queryBuilder = $this->entityManager->createQueryBuilder();

            $queryBuilder->delete(AuditLog::class, 'a')
                ->where('a.blame is null')
                ->getQuery()
                ->execute();

            $this->entityManager->getConnection()->commit();

            $io->success('Successfully deleted log entries with an empty blame_id');

        } catch (Throwable $e) {
            $this->entityManager->getConnection()->rollBack();

            $io->error($e->getMessage());
        }
    }
}
