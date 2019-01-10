<?php

namespace SubscriptionBundle\Command;

use App\Domain\Constants\ConstBillingCarrierId;
use App\Domain\Entity\Carrier;
use Doctrine\ORM\EntityManager;
use IdentificationBundle\Entity\CarrierInterface;
use IdentificationBundle\Repository\CarrierRepositoryInterface;
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
    /**
     * @var CarrierRepositoryInterface
     */
    private $carrierRepository;
    /**
     * @var CarrierInterface
     */
    private $carrier;
    /**
     * @var EntityManager
     */
    private $entityManager;
    /**
     * @var CronTaskStatus
     */
    private $cronTaskStatus;


    /**
     * RenewTrialSubscriptionCommand constructor.
     *
     * @param TrialRenewer   $renewService
     * @param CronTaskStatus $cronTaskStatus
     * @param EntityManager  $entityManager
     */
    public function __construct(TrialRenewer $renewService, CronTaskStatus $cronTaskStatus, EntityManager $entityManager)
    {
        $this->renewer         = $renewService;
        $this->cronTaskStatus = $cronTaskStatus;
        $this->entityManager = $entityManager;
        parent::__construct();
    }

    public function configure()
    {
        $this->setName('user:subscription:renew-trial');
        $this->setHelp('This command should renew trial users subscriptions.');
        $this->setHelp('Put this command on CRON (1 time per day) and be happy :)');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|void|null
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        foreach ([ConstBillingCarrierId::GLOBE_PHILIPPINES] as $carrierId) {

            $carrier = $this->entityManager->find('\App\Domain\Entity\Carrier', $carrierId);

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
        switch ($carrier->getBillingCarrierId()) {
            case ConstBillingCarrierId::GLOBE_PHILIPPINES:
                return 'globePhilippinesTrialCronTask';
            default:
                throw new \Exception(sprintf('No CRON tasks for carrier %s', $carrier->getName()));
        }
    }
}