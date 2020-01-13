<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 25.12.19
 * Time: 15:51
 */

namespace SubscriptionBundle\Affiliate\CampaignConfirmation\Command;


use App\Domain\Entity\Affiliate;
use App\Domain\Entity\Campaign;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use SubscriptionBundle\Affiliate\CampaignConfirmation\Handler\CampaignConfirmationHandlerProvider;
use SubscriptionBundle\Affiliate\CampaignConfirmation\Handler\HasDelayedConfirmation;
use SubscriptionBundle\Entity\Affiliate\AffiliateLog;
use SubscriptionBundle\Repository\Affiliate\AffiliateLogRepository;
use SubscriptionBundle\Repository\Affiliate\CampaignRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Stopwatch\Stopwatch;

class ProcessDelayedConfirmationsCommand extends Command
{
    /**
     * @var AffiliateLogRepository
     */
    private $affiliateLogRepository;
    /**
     * @var CampaignConfirmationHandlerProvider
     */
    private $handlerProvider;
    /**
     * @var CampaignRepositoryInterface
     */
    private $campaignRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var Stopwatch
     */
    private $stopwatch;


    /**
     * ProcessDelayedConfirmationsCommand constructor.
     * @param AffiliateLogRepository              $affiliateLogRepository
     * @param CampaignConfirmationHandlerProvider $handlerProvider
     * @param CampaignRepositoryInterface         $campaignRepository
     * @param EntityManagerInterface              $entityManager
     * @param LoggerInterface                     $logger
     * @param Stopwatch                           $stopwatch
     */
    public function __construct(
        AffiliateLogRepository $affiliateLogRepository,
        CampaignConfirmationHandlerProvider $handlerProvider,
        CampaignRepositoryInterface $campaignRepository,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        Stopwatch $stopwatch
    )
    {
        $this->affiliateLogRepository = $affiliateLogRepository;
        $this->handlerProvider        = $handlerProvider;
        $this->campaignRepository     = $campaignRepository;
        $this->entityManager          = $entityManager;
        $this->logger                 = $logger;
        $this->stopwatch              = $stopwatch;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('subscription:campaign-confirmation:process-delayed-confirmations');

        $this->addArgument('type', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $type = $input->getArgument('type');

        try {
            $handler = $this->handlerProvider->getHandler($type);
        } catch (\InvalidArgumentException $exception) {
            $message = sprintf('Error has been occurred during initialization: `%s`', $exception->getMessage());
            $this->logger->error($message);
            $output->writeln($message);
            return;
        }

        if (!$handler instanceof HasDelayedConfirmation) {
            $message = sprintf(
                '`%s` should implement `%s` to be used via this command',
                get_class($handler),
                HasDelayedConfirmation::class
            );
            $this->logger->error($message);
            $output->writeln($message);
            return;
        }

        $timer        = $this->stopwatch->start('command');
        $messageParts = [
            "Sending conversions to GoogleAds ($type)"
        ];

        $batch   = $handler->getBatchOfLogs();
        $results = [];

        foreach ($batch as $log) {

            $campaign = $this->getCampaign($log->getCampaignToken());
            if (!$campaign) {
                continue;
            }

            $result   = $handler->doConfirm($log);
            $resultId = $result->getResultId();

            if (!isset($results[$resultId])) {
                $results[$resultId] = 1;
            } else {
                $results[$resultId]++;
            }


            $this->entityManager->flush();
            $this->entityManager->clear();

        }

        $timer->stop();

        $messageParts[] = sprintf("%s records processed", count($batch));
        $messageParts[] = sprintf("%s trackings succeeded", $results['success'] ?? 0);
        $messageParts[] = sprintf("%s trackings failed", $results['failure'] ?? 0);
        $messageParts[] = sprintf("%s to be retried", $results['retry'] ?? 0);
        $messageParts[] = sprintf("took %0.3f seconds", $timer->getDuration());

        $output->write(implode(PHP_EOL, $messageParts));

    }

    private function getCampaign(string $token): ?Campaign
    {
        $selectedCampaigns = [];

        if (isset($selectedCampaigns[$token])) {

        } else {
            $selectedCampaigns[$token] = $this->campaignRepository->findOneByCampaignToken($token);
        }

        return $selectedCampaigns[$token];


    }
}