<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 07.03.19
 * Time: 12:04
 */

namespace SubscriptionBundle\Command;


use IdentificationBundle\Repository\CarrierRepositoryInterface;
use SubscriptionBundle\Service\Cron\RenewAlerter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class IncomingRenewNotificationCommand extends Command
{
    /**
     * @var CarrierRepositoryInterface
     */
    private $carrierRepository;
    /**
     * @var RenewAlerter
     */
    private $renewAlerter;


    /**
     * IncomingRenewNotificationCommand constructor.
     * @param CarrierRepositoryInterface $carrierRepository
     * @param RenewAlerter               $renewAlerter
     */
    public function __construct(CarrierRepositoryInterface $carrierRepository, RenewAlerter $renewAlerter)
    {
        $this->carrierRepository = $carrierRepository;
        $this->renewAlerter      = $renewAlerter;
        parent::__construct();
    }

    public function configure()
    {
        $this->setName('user:subscription:renew-alert');
        $this->addArgument('carrier_id', InputArgument::REQUIRED, 'Carrier ID');
        $this->setHelp('This command should send SMS about renew to user.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $carrierId = (int)$input->getArgument('carrier_id');
        if (!$carrierId) {
            throw new \InvalidArgumentException('Wrong carrier Id');
        }
        $carrier = $this->carrierRepository->findOneByBillingId($carrierId);

        $this->renewAlerter->sendRenewAlerts($carrier);
    }
}