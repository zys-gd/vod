<?php

namespace SubscriptionBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use IdentificationBundle\Entity\CarrierInterface;
use IdentificationBundle\Repository\CarrierRepositoryInterface;
use SubscriptionBundle\Service\CapConstraint\ConstraintCounterRedis;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ResetConstraintsByCarrierCounters
 */
class ResetConstraintsByCarrierCounters extends Command
{
    /**
     * @var ConstraintCounterRedis
     */
    private $constraintCounterRedis;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var CarrierRepositoryInterface
     */
    private $carrierRepository;

    /**
     * ResetConstraintsByCarrierCounters constructor
     *
     * @param ConstraintCounterRedis $constraintCounterRedis
     * @param EntityManagerInterface $entityManager
     * @param CarrierRepositoryInterface $carrierRepository
     */
    public function __construct(
        ConstraintCounterRedis $constraintCounterRedis,
        EntityManagerInterface $entityManager,
        CarrierRepositoryInterface $carrierRepository
    ) {
        $this->constraintCounterRedis = $constraintCounterRedis;
        $this->entityManager = $entityManager;
        $this->carrierRepository = $carrierRepository;

        parent::__construct();
    }

    public function configure()
    {
        $this->setName('constraint-by-carrier:reset');
        $this->setHelp('Reset from redis all counters for constraints by affiliate');
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

            $this->constraintCounterRedis->resetCounter($carrier->getUuid());

            $carrier
                ->setIsCapAlertDispatch(false)
                ->setFlushDate(new \DateTime('now'));

            $this->entityManager->persist($carrier);
        }

        $this->entityManager->flush();

        $output->writeln('Constraint by carrier counters successfully reset');
    }
}