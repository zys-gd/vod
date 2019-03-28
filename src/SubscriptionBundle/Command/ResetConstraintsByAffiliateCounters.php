<?php

namespace SubscriptionBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use SubscriptionBundle\Entity\Affiliate\ConstraintByAffiliate;
use SubscriptionBundle\Repository\Affiliate\ConstraintByAffiliateRepository;
use SubscriptionBundle\Service\AffiliateConstraint\ConstraintByAffiliateRedis;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ResetConstraintsByAffiliateCounters
 */
class ResetConstraintsByAffiliateCounters extends Command
{
    /**
     * @var ConstraintByAffiliateRedis
     */
    private $constraintByAffiliateRedis;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var ConstraintByAffiliateRepository
     */
    private $constraintByAffiliateRepository;

    public function __construct(
        ConstraintByAffiliateRedis $constraintByAffiliateRedis,
        EntityManagerInterface $entityManager,
        ConstraintByAffiliateRepository $constraintByAffiliateRepository
    ) {
        $this->constraintByAffiliateRedis = $constraintByAffiliateRedis;
        $this->entityManager = $entityManager;
        $this->constraintByAffiliateRepository = $constraintByAffiliateRepository;

        parent::__construct();
    }

    public function configure()
    {
        $this->setName('constraint-by-affiliate:reset');
        $this->setHelp('Reset from cache all counters for constraints by affiliate');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|void|null
     *
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $constraints = $this->constraintByAffiliateRepository->findAll();

        if (empty($constraints)) {
            $output->writeln('No constraints by affiliates were found');

            return;
        }

        /** @var ConstraintByAffiliate $constraint */
        foreach ($constraints as $constraint) {
            $this->constraintByAffiliateRedis->resetCounter($constraint);

            $constraint
                ->setIsCapAlertDispatch(false)
                ->setFlushDate(new \DateTime('now'));

            $this->entityManager->persist($constraint);
        }

        $this->entityManager->flush();

        $output->writeln('Counters successfully reset');
    }
}