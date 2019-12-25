<?php


namespace SubscriptionBundle\Affiliate\CampaignConfirmation\Handler\Common;


use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use SubscriptionBundle\Affiliate\CampaignConfirmation\Handler\CampaignConfirmationInterface;
use SubscriptionBundle\Affiliate\CampaignConfirmation\Handler\HasInstantConfirmation;
use SubscriptionBundle\Affiliate\CampaignConfirmation\Result\AbstractResult;
use SubscriptionBundle\Affiliate\CampaignConfirmation\Result\Failure;
use SubscriptionBundle\Affiliate\CampaignConfirmation\Result\Success;
use SubscriptionBundle\Affiliate\DTO\UserInfo;
use SubscriptionBundle\Affiliate\Exception\WrongIncomingParameters;
use SubscriptionBundle\Affiliate\Service\AffiliateLogFactory;
use SubscriptionBundle\Affiliate\Service\GuzzleClientFactory;
use SubscriptionBundle\Entity\Affiliate\AffiliateInterface;
use SubscriptionBundle\Entity\Affiliate\AffiliateLog;
use SubscriptionBundle\Entity\Affiliate\CampaignInterface;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Entity\SubscriptionPack;

class DefaultHandler implements CampaignConfirmationInterface, HasInstantConfirmation
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var GuzzleClientFactory
     */
    private $guzzleClientFactory;
    /**
     * @var AffiliateLogFactory
     */
    private $affiliateLogFactory;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * DefaultHandler constructor.
     * @param LoggerInterface        $logger
     * @param GuzzleClientFactory    $guzzleClientFactory
     * @param AffiliateLogFactory    $affiliateLogFactory
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        LoggerInterface $logger,
        GuzzleClientFactory $guzzleClientFactory,
        AffiliateLogFactory $affiliateLogFactory,
        EntityManagerInterface $entityManager
    )
    {
        $this->logger              = $logger;
        $this->guzzleClientFactory = $guzzleClientFactory;
        $this->affiliateLogFactory = $affiliateLogFactory;
        $this->entityManager       = $entityManager;
    }


    /**
     * @param string $affiliateUuid
     *
     * @return bool
     */
    public function isAffiliateSupported(string $affiliateUuid): bool
    {
        return true;
    }

    public function doConfirm(
        AffiliateInterface $affiliate,
        CampaignInterface $campaign,
        array $rawCampaignData,
        Subscription $subscription,
        UserInfo $userInfo
    ): AbstractResult
    {

        try {
            $data = [
                'query' => $this->getPostBackParameters(
                    $affiliate,
                    $campaign,
                    $rawCampaignData,
                    $subscription->getSubscriptionPack()
                )
            ];
        } catch (WrongIncomingParameters $e) {
            $this->logger->debug('ending with error AffiliateSender::checkAffiliateEligibilityAndSendEvent(): not full data in request');
            return new Failure();
        }

        $fullUrl = $affiliate->getPostbackUrl() . '?' . http_build_query($data['query']);

        $this->logger->debug('check content', [
            'userInfo' => $data,
            'fullUrl'  => $fullUrl,
        ]);

        try {
            $client = $this->guzzleClientFactory->getClient();
            $client->request('GET', $affiliate->getPostbackUrl(), array_merge(['timeout' => 5.0], $data));
            $entity = $this->affiliateLogFactory->create(
                AffiliateLog::STATUS_SUCCESS,
                $fullUrl,
                $userInfo,
                $campaign,
                $subscription,
                $rawCampaignData
            );
            $result = new Success();
        } catch (\Throwable $ex) {
            $entity = $this->affiliateLogFactory->create(
                AffiliateLog::STATUS_FAILURE,
                $fullUrl,
                $userInfo,
                $campaign,
                $subscription,
                $rawCampaignData,
                $ex->getMessage()
            );
            $result = new Failure();
        }

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $result;
    }

    /**
     * @param AffiliateInterface $affiliate
     * @param CampaignInterface  $campaign
     * @param array              $campaignData
     * @param SubscriptionPack   $subscriptionPack
     *
     * @return array
     * @throws WrongIncomingParameters
     */
    private function getPostBackParameters(
        AffiliateInterface $affiliate,
        CampaignInterface $campaign,
        array $campaignData,
        SubscriptionPack $subscriptionPack
    ): array
    {
        $query = [];

        if ($affiliate->isUniqueFlow()) {
            $query = $this->jumpIntoUniqueFlow($affiliate, $campaignData);
        } else {
            $paramsList    = $affiliate->getParamsList();
            $constantsList = $affiliate->getConstantsList();
            $query         = $this->jumpIntoStandartFlow($paramsList, $constantsList, $campaignData, $query);
            $this->logger->debug('check content in getPostBackParameters()', [
                'query' => $query
            ]);
        };

        if (!$affiliate->isUniqueFlow() && $subPriceName = $affiliate->getSubPriceName()) {
            $query = array_merge(
                $query,
                [$subPriceName => $this->calculateAffiliatePriceParameter($campaign, $subscriptionPack)]
            );
        }

        $this->logger->debug('check content in getPostBackParameters()', [
            'query' => $query
        ]);

        return $query;
    }

    private function calculateAffiliatePriceParameter(
        CampaignInterface $campaign,
        SubscriptionPack $subscriptionPack
    ): float
    {
        if ($campaign->isZeroCreditSubAvailable()) {
            return $campaign->getZeroEurPrice();
        }

        if ($campaign->isFreeTrialSubscription() || $subscriptionPack->isFirstSubscriptionPeriodIsFree()) {
            return $campaign->getFreeTrialPrice();
        }

        return $campaign->getGeneralPrice();
    }

    /**
     * @param array $paramsList
     * @param array $constantsList
     * @param array $campaignParams
     * @param array $query
     *
     * @return array
     * @throws WrongIncomingParameters
     */
    private function jumpIntoStandartFlow(array $paramsList, array $constantsList, array $campaignParams, array $query)
    {
        $this->logger->debug('debug AffiliateSender::jumpIntoStandartFlow()', [
            'paramsList (from DB)'      => $paramsList,
            'campaignParams (from url)' => $campaignParams,
            'constantsList'             => $constantsList,
        ]);

        if (!empty($paramsList)) {
            foreach ($paramsList as $output => $input) {
                try {
                    $query[$output] = $campaignParams[$input]; // !isset($campaignParams[$input])
                } catch (\Error $e) {
                    throw new WrongIncomingParameters();
                }
            }
        }
        if (!empty($constantsList)) {
            $query = array_merge($constantsList, $query);
        }

        return $query;


    }

    /**
     * @param AffiliateInterface $affiliate
     * @param array              $campaignParams
     *
     * @return array|string
     */
    private function jumpIntoUniqueFlow(AffiliateInterface $affiliate, array $campaignParams)
    {
        if (!empty($campaignParams) && array_key_exists($affiliate->getUniqueParameter(), $campaignParams)) {
            $query = [
                $affiliate->getUniqueParameter() => $campaignParams[$affiliate->getUniqueParameter()]
            ];

            return $query;
        }

        return [];
    }

    public function getHandlerId(): string
    {
        return 'default';
    }
}