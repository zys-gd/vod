<?php

namespace SubscriptionBundle\Command;


use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use SubscriptionBundle\Entity\CronRunningHistory;
use SubscriptionBundle\Repository\CronRunningHistoryRepository;

class GeneralRenewCommand extends ContainerAwareCommand
{
    /**
     * @var CronRunningHistoryRepository
     */
    private $cronRunningHistoryRepository;
    /**
     * @var EntityManager
     */
    private $entityManager;


    /**
     * GeneralRenewCommand constructor.
     * @param CronRunningHistoryRepository $cronRunningHistoryRepository
     * @param EntityManager                $entityManager
     */
    public function __construct(
        CronRunningHistoryRepository $cronRunningHistoryRepository,
        EntityManager $entityManager
    )
    {
        $this->cronRunningHistoryRepository = $cronRunningHistoryRepository;
        $this->entityManager                = $entityManager;

        parent::__construct();
    }

    public function configure()
    {
        $this->setName('subscription.endless.cron');
        $this->setHelp('This cron should work automatically on the cron instance');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var EntityManager $entityManager */
        $command = $this->getApplication()->find('user:subscription:renew:v2');
        /** @var CronRunningHistory $lastEvent */
        $lastEvent       = $this->cronRunningHistoryRepository->findOneBy([], ['id' => 'DESC']);
        $currentTime     = new \DateTime('now');
        $currentTimeTemp = new \DateTime('now');

        if ($lastEvent && ($currentTime->format('Y-m-d H') == $lastEvent->getLastRunningHour()->format('Y-m-d H'))) {
            return 0;
        } else {
            $startTime  = $lastEvent
                ? $lastEvent->getLastRunningHour()
                : $currentTime;
            $endTime    = $lastEvent
                ? new \DateTime($lastEvent->getLastRunningHour()->format('Y-m-d H:i:s') . '+1 hour -1 second')
                : $currentTimeTemp->modify('+1 hour -1 second');
            $arguments  = [
                'command'    => 'user:subscription:renew:v2',
                'start-date' => $startTime->format('Y-m-d H:i:s T'),
                'end-date'   => $endTime->format('Y-m-d H:i:s T')
            ];
            $greenInput = new ArrayInput($arguments);
            $command->run($greenInput, $output);
            $newEvent = new CronRunningHistory();
            $newEvent->setLastRunningHour($endTime);
            $this->entityManager->persist($newEvent);
            $this->entityManager->flush();
        }
    }
}