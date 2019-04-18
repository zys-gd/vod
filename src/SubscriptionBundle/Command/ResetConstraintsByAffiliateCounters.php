<?php

namespace SubscriptionBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use SubscriptionBundle\Entity\Affiliate\ConstraintByAffiliate;
use SubscriptionBundle\Repository\Affiliate\ConstraintByAffiliateRepository;
use SubscriptionBundle\Service\SubscriptionLimiter\DTO\AffiliateLimiterData;
use SubscriptionBundle\Service\SubscriptionLimiter\DTO\CarrierLimiterData;
use SubscriptionBundle\Service\SubscriptionLimiter\Limiter\LimiterDataStorage;
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
     * @var LimiterDataStorage
     */
    private $limiterDataStorage;

    /**
     * ResetConstraintsByAffiliateCounters constructor
     *
     * @param EntityManagerInterface          $entityManager
     * @param ConstraintByAffiliateRepository $constraintByAffiliateRepository
     * @param LimiterDataStorage              $limiterDataStorage
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        ConstraintByAffiliateRepository $constraintByAffiliateRepository,
        LimiterDataStorage $limiterDataStorage
    )
    {
        $this->entityManager                   = $entityManager;
        $this->constraintByAffiliateRepository = $constraintByAffiliateRepository;

        parent::__construct();
        $this->limiterDataStorage = $limiterDataStorage;
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


            $carrier = $constraint->getCarrier();

            // $carrierLimiterData = new CarrierLimiterData($carrier, $carrier->getNumberOfAllowedSubscriptionsByConstraint(), $carrier->getNumberOfAllowedSubscriptionsByConstraint());

            $affiliateLimiterData = new AffiliateLimiterData($constraint->getAffiliate(), $constraint, $carrier->getBillingCarrierId(), $constraint->getNumberOfActions(), $constraint->getNumberOfActions());

            // $this->limiterPerformer->saveCarrierConstraint($carrierLimiterData);
            $this->limiterDataStorage->saveCarrierAffiliateConstraint($affiliateLimiterData);

            $constraint
                ->setIsCapAlertDispatch(false)
                ->setFlushDate(new \DateTime('now'));

            $this->entityManager->persist($constraint);
        }

        $this->entityManager->flush();

        $output->writeln('Constraint by affiliate counters successfully reset');
    }
}