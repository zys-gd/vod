<?php

namespace SubscriptionBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use IdentificationBundle\Entity\CarrierInterface;
use IdentificationBundle\Repository\CarrierRepositoryInterface;
use SubscriptionBundle\Service\SubscriptionLimiter\DTO\CarrierLimiterData;
use SubscriptionBundle\Service\SubscriptionLimiter\Limiter\LimiterStorage;
use SubscriptionBundle\Service\SubscriptionLimiter\Limiter\StorageKeyGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ResetConstraintsByCarrierCounters
 */
class ResetConstraintsByCarrierCounters extends Command
{

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var CarrierRepositoryInterface
     */
    private $carrierRepository;
    /**
     * @var LimiterStorage
     */
    private $limiterDataStorage;
    /**
     * @var StorageKeyGenerator
     */
    private $storageKeyGenerator;

    /**
     * ResetConstraintsByCarrierCounters constructor
     *
     * @param EntityManagerInterface     $entityManager
     * @param CarrierRepositoryInterface $carrierRepository
     * @param LimiterStorage             $limiterDataStorage
     * @param StorageKeyGenerator        $storageKeyGenerator
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        CarrierRepositoryInterface $carrierRepository,
        LimiterStorage $limiterDataStorage,
        StorageKeyGenerator $storageKeyGenerator
    )
    {
        $this->entityManager       = $entityManager;
        $this->carrierRepository   = $carrierRepository;
        $this->limiterDataStorage  = $limiterDataStorage;
        $this->storageKeyGenerator = $storageKeyGenerator;

        parent::__construct();
    }

    public function configure()
    {
        $this->setName('constraint-by-carrier:reset');
        $this->setHelp('Reset from redis all counters for constraints by affiliate');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|void|null
     *
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $carriers = $this->carrierRepository->findAll();

        if (empty($carriers)) {
            $output->writeln('No carriers were found');

            return;
        }

        /** @var CarrierInterface $carrier */
        foreach ($carriers as $carrier) {
            $allowedSubscriptions = $carrier->getNumberOfAllowedSubscriptionsByConstraint();

            if (empty($allowedSubscriptions)) {
                continue;
            }
            $output->writeln($carrier->getName());

            $key = $this->storageKeyGenerator->generateKey($carrier);

            $this->limiterDataStorage->resetPendingCounter($key);
            $this->limiterDataStorage->resetFinishedCounter($key);

            $carrier
                ->setIsCapAlertDispatch(false)
                ->setFlushDate(new \DateTime('now'));

            $this->entityManager->persist($carrier);
        }

        $this->entityManager->flush();

        $output->writeln('Constraint by carrier counters successfully reset');
    }
}