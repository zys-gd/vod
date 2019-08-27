<?php

namespace SubscriptionBundle\Subscription\MassRenew\Command;

use IdentificationBundle\BillingFramework\ID;
use IdentificationBundle\Repository\CarrierRepositoryInterface;
use SubscriptionBundle\Subscription\Renew\Common\CommonFlowHandler;
use SubscriptionBundle\Subscription\Renew\Cron\CronTaskStatus;
use SubscriptionBundle\Subscription\Renew\Handler\HasCommonFlow;
use SubscriptionBundle\Subscription\Renew\Handler\RenewHandlerProvider;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MassRenewCommand extends Command
{

    const CRON_TASK_NAME_MAP = [
        ID::MOBILINK_PAKISTAN    => 'mobilinkPakistanMassRenewCronTask',
        ID::TELENOR_PAKISTAN_DOT => 'telenorPakistanDOTMassRenewCronTask'
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
     * @var RenewHandlerProvider
     */
    private $renewHandlerProvider;
    /**
     * @var CommonFlowHandler
     */
    private $commonFlowHandler;


    /**
     * MassRenewCommand constructor.
     * @param CarrierRepositoryInterface $carrierRepository
     * @param CronTaskStatus             $cronTaskStatus
     * @param RenewHandlerProvider       $renewHandlerProvider
     * @param CommonFlowHandler          $commonFlowHandler
     */
    public function __construct(
        CarrierRepositoryInterface $carrierRepository,
        CronTaskStatus $cronTaskStatus,
        RenewHandlerProvider $renewHandlerProvider,
        CommonFlowHandler $commonFlowHandler
    )
    {
        $this->carrierRepository    = $carrierRepository;
        $this->cronTaskStatus       = $cronTaskStatus;
        $this->renewHandlerProvider = $renewHandlerProvider;
        parent::__construct();
        $this->commonFlowHandler = $commonFlowHandler;
    }

    public function configure()
    {
        $this->setName('user:subscription:mass-renew');
        $this->addArgument('carrier_id', InputArgument::REQUIRED, 'Carrier ID');
        $this->setHelp("Command to renew expired subscriptions in batches.");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $carrierId = (int)$input->getArgument('carrier_id');

        if (!$carrierId) {
            throw new \InvalidArgumentException('Wrong carrier Id');
        }

        $carrier  = $this->carrierRepository->findOneByBillingId($carrierId);
        $taskName = self::CRON_TASK_NAME_MAP[$carrierId] ?? null;

        if (!$taskName) {
            throw new \InvalidArgumentException('No cron tasks for selected carrier');
        }

        $this->cronTaskStatus->getCronTaskByName($taskName);
        try {
            $this->cronTaskStatus->start();
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
            return;
        }

        $handler = $this->renewHandlerProvider->getRenewer($carrier);

        if ($handler instanceof HasCommonFlow) {
            $result = $this->commonFlowHandler->process($carrier);
        } else {
            throw new \RuntimeException('Handlers for renew should have according interfaces');
        }

        $this->cronTaskStatus->stop();

        $output->write(implode("\n", [
            sprintf('Processed: %s', $result->getProcessed()),
            sprintf('Succeeded: %s', count($result->getSucceededSubscriptions())),
            sprintf('Failed: %s', count($result->getFailedSubscriptions())),
            sprintf('Error: %s', $result->getError() ?? 'No errors'),
        ]));
    }


}
