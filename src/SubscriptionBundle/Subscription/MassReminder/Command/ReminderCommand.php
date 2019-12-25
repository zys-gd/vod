<?php

namespace SubscriptionBundle\Subscription\MassReminder\Command;

use IdentificationBundle\BillingFramework\ID;
use IdentificationBundle\Repository\CarrierRepositoryInterface;
use SubscriptionBundle\Subscription\MassReminder\Reminder;
use SubscriptionBundle\Subscription\Reminder\ReminderHandlerProvider;
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
    const CRON_TASK_NAME_MAP = [
        ID::BEELINE_KAZAKHSTAN_DOT => 'beelineKZDotMassRemindCronTask'
    ];

    /**
     * @var CarrierRepositoryInterface
     */
    private $carrierRepository;

    /**
     * @var CronTaskStatus
     */
    private $cronTaskStatus;

    /**
     * @var ReminderHandlerProvider
     */
    private $handlerProvider;

    /**
     * @var Reminder
     */
    private $reminder;

    /**
     * ReminderCommand constructor
     *
     * @param CarrierRepositoryInterface $carrierRepository
     * @param CronTaskStatus             $cronTaskStatus
     * @param ReminderHandlerProvider    $handlerProvider
     * @param Reminder                   $reminder
     */
    public function __construct(
        CarrierRepositoryInterface $carrierRepository,
        CronTaskStatus $cronTaskStatus,
        ReminderHandlerProvider $handlerProvider,
        Reminder $reminder
    ) {
        $this->carrierRepository = $carrierRepository;
        $this->cronTaskStatus    = $cronTaskStatus;
        $this->handlerProvider   = $handlerProvider;
        $this->reminder          = $reminder;

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
     *
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $carrierId = (int) $input->getArgument('carrier_id');

        if (!$carrierId) {
            throw new \InvalidArgumentException('Wrong carrier Id');
        }

        $carrier = $this->carrierRepository->findOneByBillingId($carrierId);

        if (!$carrier) {
            throw new \InvalidArgumentException('Wrong carrier Id');
        }

        $taskName = self::CRON_TASK_NAME_MAP[$carrierId] ?? null;

        if (!$taskName) {
            throw new \InvalidArgumentException('No cron tasks for selected carrier');
        }

        $this->cronTaskStatus->initializeCronTaskByName($taskName);

        try {
            $this->cronTaskStatus->start();
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());

            return;
        }

        $handler = $this->handlerProvider->getHandler($carrierId);

        if (!$handler) {
            throw new \InvalidArgumentException('No handler for selected carrier');
        }

        $result = $this->reminder->doRemind($carrier, $handler->getRemind());

        $output->write(implode("\n", [
            sprintf('Processed: %s', $result->getProcessed()),
            sprintf('Succeeded: %s', count($result->getSucceededSubscriptions())),
            sprintf('Failed: %s', count($result->getFailedSubscriptions())),
            sprintf('Error: %s', $result->getError() ?? 'No errors'),
            ''
        ]));
    }
}