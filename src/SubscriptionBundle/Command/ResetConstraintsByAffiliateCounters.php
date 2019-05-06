<?php

namespace SubscriptionBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use SubscriptionBundle\Entity\Affiliate\ConstraintByAffiliate;
use SubscriptionBundle\Repository\Affiliate\ConstraintByAffiliateRepository;
use SubscriptionBundle\Service\SubscriptionLimiter\DTO\AffiliateLimiterData;
use SubscriptionBundle\Service\SubscriptionLimiter\DTO\CarrierLimiterData;
use SubscriptionBundle\Service\SubscriptionLimiter\Limiter\LimiterStorage;
use SubscriptionBundle\Service\SubscriptionLimiter\Limiter\StorageKeyGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ResetConstraintsByAffiliateCounters
 */
class ResetConstraintsByAffiliateCounters extends Command
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var ConstraintByAffiliateRepository
     */
    private $constraintByAffiliateRepository;
    /**
     * @var LimiterStorage
     */
    private $limiterDataStorage;
    /**
     * @var StorageKeyGenerator
     */
    private $storageKeyGenerator;

    /**
     * ResetConstraintsByAffiliateCounters constructor
     *
     * @param EntityManagerInterface          $entityManager
     * @param ConstraintByAffiliateRepository $constraintByAffiliateRepository
     * @param LimiterStorage                  $limiterDataStorage
     * @param StorageKeyGenerator             $storageKeyGenerator
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        ConstraintByAffiliateRepository $constraintByAffiliateRepository,
        LimiterStorage $limiterDataStorage,
        StorageKeyGenerator $storageKeyGenerator
    )
    {
        $this->entityManager                   = $entityManager;
        $this->constraintByAffiliateRepository = $constraintByAffiliateRepository;
        $this->limiterDataStorage              = $limiterDataStorage;
        $this->storageKeyGenerator             = $storageKeyGenerator;
        parent::__construct();
    }

    public function configure()
    {
        $this->setName('constraint-by-affiliate:reset');
        $this->setHelp('Reset from redis all counters for constraints by affiliate');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|void|null
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $constraints = $this->constraintByAffiliateRepository->getSubscriptionConstraints();

        if (empty($constraints)) {
            $output->writeln('No constraints by affiliates were found');

            return;
        }

        /** @var ConstraintByAffiliate $constraint */
        foreach ($constraints as $constraint) {

            $key = $this->storageKeyGenerator->generateAffiliateConstraintKey($constraint);

            $this->limiterDataStorage->resetFinishedCounter($key);
            $this->limiterDataStorage->resetPendingCounter($key);

            $constraint
                ->setIsCapAlertDispatch(false)
                ->setFlushDate(new \DateTime('now'));

            $this->entityManager->persist($constraint);
        }

        $this->entityManager->flush();

        $output->writeln('Constraint by affiliate counters successfully reset');
    }
}