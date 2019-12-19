<?php

namespace SubscriptionBundle\Subscription\Reminder\Command;

use IdentificationBundle\Repository\CarrierRepositoryInterface;
use SubscriptionBundle\Repository\SubscriptionRepository;
use SubscriptionBundle\Subscription\Renew\Cron\CronTaskStatus;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ReminderCommand
 */
class ReminderCommand extends Command
{
    /**
     * @var CarrierRepositoryInterface
     */
    private $carrierRepository;

    /**
     * @var CronTaskStatus
     */
    private $cronTaskStatus;

    /**
     * @var SubscriptionRepository
     */
    private $subscriptionRepository;

    public function __construct(
        CarrierRepositoryInterface $carrierRepository,
        CronTaskStatus $cronTaskStatus,
        SubscriptionRepository $subscriptionRepository
    )
    {
        $this->carrierRepository = $carrierRepository;
        $this->cronTaskStatus = $cronTaskStatus;
        $this->subscriptionRepository = $subscriptionRepository;

        parent::__construct();
    }

    public function configure()
    {
        $this->setName('subscription:reminder');
        $this->addArgument('carrier_id', InputArgument::REQUIRED, 'Carrier ID');
        $this->setHelp("Command to renew expired subscriptions in batches.");
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $carrierId = (int) $input->getArgument('carrier_id');

        if (!$carrierId) {
            throw new \InvalidArgumentException('Wrong carrier Id');
        }
    }
}