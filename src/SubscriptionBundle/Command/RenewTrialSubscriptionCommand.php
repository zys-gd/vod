<?php

namespace SubscriptionBundle\Command;

use AppBundle\Entity\Carrier;
use AppBundle\Service\Domain\Carrier\CarrierProvider;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use SubscriptionBundle\Service\Cron\CronTaskStatus;
use SubscriptionBundle\Service\Cron\TrialRenewer;

class RenewTrialSubscriptionCommand extends ContainerAwareCommand
{
    /**
     * @var \SubscriptionBundle\Service\Cron\TrialRenewer
     */
    private $renewer;
    private $cronTaskStatus;
    /**
     * @var CarrierProvider
     */
    private $carrierProvider;


    /**
     * RenewTrialSubscriptionCommand constructor.
     * @param TrialRenewer    $renewService
     * @param CronTaskStatus  $cronTaskStatus
     * @param CarrierProvider $carrierProvider
     */
    public function __construct(TrialRenewer $renewService, CronTaskStatus $cronTaskStatus, CarrierProvider $carrierProvider)
    {
        $this->renewer         = $renewService;
        $this->cronTaskStatus  = $cronTaskStatus;
        $this->carrierProvider = $carrierProvider;
        parent::__construct();
    }

    public function configure()
    {
        $this->setName('user:subscription:renew-trial');
        $this->setHelp('This command should renew trial users subscriptions.');
        $this->setHelp('Put this command on CRON (1 time per day) and be happy :)');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        foreach ([\AppBundle\Constant\Carrier::GLOBE_PHILIPPINES] as $carrierId) {

            $carrier = $this->carrierProvider->getCarrierEntity($carrierId);

            $this->cronTaskStatus->getCronTaskByName($this->getCronTaskName($carrier));

            try {
                $this->cronTaskStatus->start();
            } catch (\Exception $e) {
                $output->writeln($e->getMessage());
            }

            $result = $this->renewer->renew($carrier);
            $output->write($result);
        }
    }


    /**
     * @param Carrier $carrier
     * @return string
     * @throws \Exception
     */
    private function getCronTaskName(Carrier $carrier)
    {
        switch ($carrier->getIdCarrier()) {
            case \AppBundle\Constant\Carrier::GLOBE_PHILIPPINES:
                return 'globePhilippinesTrialCronTask';
            default:
                throw new \Exception(sprintf('No CRON tasks for carrier %s', $carrier->getName()));
        }
    }
}