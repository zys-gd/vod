<?php

namespace SubscriptionBundle\Command;

use Carbon\Carbon;
use Doctrine\ORM\EntityManager;
use Enqueue\Client\ProducerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Repository\SubscriptionRepository;

class RenewCommand extends ContainerAwareCommand
{
    const SUBSCRIPTION_RENEW_TOPIC = "subscription_renew";

    private $format = 'Y-m-d H:i:s T';
    /**
     * @var EntityManager
     */
    private $entityManager;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var SubscriptionRepository
     */
    private $subscriptionRepository;
    /**
     * @var ProducerInterface
     */
    private $producer;

    public function __construct(
        EntityManager $entityManager,
        LoggerInterface $logger,
        SubscriptionRepository $subscriptionRepository,
        ProducerInterface $producer
    )
    {
        $this->entityManager          = $entityManager;
        $this->logger                 = $logger;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->producer               = $producer;
        parent::__construct();
    }


    protected function configure()
    {
        $this
            ->setName('user:subscription:renew:v2')
            ->setDescription('Renew subscriptions')
            ->addArgument(
                'start-date',
                InputOption::VALUE_REQUIRED,
                "Renews those subscriptions which have renew date larger than provided date.
                If not provided no start-date filter will be added. Please use '{$this->format}' format"
            )
            ->addArgument(
                'end-date',
                InputOption::VALUE_REQUIRED,
                "Renews those subscriptions which have renew date less than provided date. 
                    If not provided current date will be considered.
                 Please use '{$this->format}' format",
                Carbon::now()->format($this->format)
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $startDate = $input->getArgument('start-date');
        if ($startDate) {
            $startDate = Carbon::createFromFormat($this->format, $startDate);
        }
        $endDate = $input->getArgument('end-date');
        $endDate = Carbon::createFromFormat($this->format, $endDate);

        // DISABLEING THE SQL LOGGER TO GIAN IN PERFORMANCE
        $subscriptionsIterator = $this->subscriptionRepository->findRenewableSubscription($startDate, $endDate);

        $messagesQueued        = 0;
        foreach ($subscriptionsIterator as $subscriptionRow) {
            /** @var Subscription $subscription */
            $subscription = $subscriptionRow[0];
            $message      = $subscription->getUuid();

            $this->producer->sendEvent(self::SUBSCRIPTION_RENEW_TOPIC, $message);
            $messagesQueued++;
            $this->entityManager->detach($subscriptionRow[0]);
            $subscription = null;
            if ($messagesQueued % 100 == 0) {
                gc_enable();
                gc_collect_cycles();
            }
        }

        $message = "Queued {$messagesQueued} subscriptions for renewal whose renewal was due since {$startDate}";
        $this->logger->info($message);
        $output->writeln($message);
    }


}