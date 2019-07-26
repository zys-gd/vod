<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 14.01.19
 * Time: 16:24
 */

namespace SubscriptionBundle\Affiliate\Service;

use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use SubscriptionBundle\Affiliate\DTO\UserInfo;
use SubscriptionBundle\Entity\Affiliate\AffiliateInterface;
use SubscriptionBundle\Entity\Affiliate\AffiliateLog;
use SubscriptionBundle\Entity\Affiliate\CampaignInterface;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Affiliate\Exception\WrongIncomingParameters;
use SubscriptionBundle\Repository\Affiliate\CampaignRepositoryInterface;

class AffiliateSender
{
    const USER_AGENT = 'Mozilla/4.0 (compatible; MSIE 7.0 Windows NT 5.1; .NET CLR 2.0.50727; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729)';
    /**
     * @var CampaignRepositoryInterface
     */
    private $campaignRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var GuzzleClientFactory
     */
    private $clientFactory;
    /**
     * @var AffiliateLogFactory
     */
    private $affiliateLogFactory;
    /**
     * @var LoggerInterface
     */
    private $logger;


    /**
     * AffiliateSender constructor.
     *
     * @param CampaignRepositoryInterface $campaignRepository
     * @param EntityManagerInterface      $entityManager
     * @param GuzzleClientFactory         $clientFactory
     * @param AffiliateLogFactory         $affiliateLogFactory
     * @param LoggerInterface             $logger
     */
    public function __construct(
        CampaignRepositoryInterface $campaignRepository,
        EntityManagerInterface $entityManager,
        GuzzleClientFactory $clientFactory,
        AffiliateLogFactory $affiliateLogFactory,
        LoggerInterface $logger
    )
    {
        $this->campaignRepository = $campaignRepository;
        $this->entityManager = $entityManager;
        $this->clientFactory = $clientFactory;
        $this->affiliateLogFactory = $affiliateLogFactory;
        $this->logger = $logger;
    }

    public function checkAffiliateEligibilityAndSendEvent(
        Subscription $subscription,
        UserInfo $userInfo,
        array $campaignParams = null,
        string $campaignToken = null
    ): void
    {
        $this->logger->debug('start AffiliateSender::checkAffiliateEligibilityAndSendEvent()', [
            'userInfo' => $userInfo,
            'campaignParams' => $campaignParams,
            '$campaignToken' => $campaignToken
        ]);
        if (!$campaignToken) {
            return;
        }
        if (is_null($campaignParams)) {
            $campaignParams = [];
        }

        /** @var CampaignInterface $campaign */
        if (!$campaign = $this->campaignRepository->findOneByCampaignToken($campaignToken)) {
            return;
        };


        $carrier = $subscription->getUser()->getCarrier();
        if (!$this->isCampaignConnectedToCarrier($campaign, $carrier)) {
            return;
        }
        $affiliate = $campaign->getAffiliate();
        if (!$this->areParametersEqual($affiliate, $campaignParams)) {

        }


        try {
            $data = ['query' => $this->getPostBackParameters($affiliate, $campaign, $campaignParams)];

            $fullUrl = $affiliate->getPostbackUrl() . '?' . http_build_query($data['query']);

            $this->logger->debug('check content', [
                'userInfo' => $data,
                'campaignParams' => $fullUrl,
                '$campaignToken' => $campaignToken
            ]);

            try {
                $client = $this->clientFactory->getClient();
                $client->request('GET', $affiliate->getPostbackUrl(), array_merge(['timeout' => 5.0], $data));
                $entity = $this->affiliateLogFactory->create(
                    AffiliateLog::EVENT_SUBSCRIBE,
                    true,
                    $fullUrl,
                    $userInfo,
                    $campaign,
                    $subscription,
                    ['cid' => $campaignToken]
                );
            } catch (\Exception $ex) {
                $entity = $this->affiliateLogFactory->create(
                    AffiliateLog::EVENT_SUBSCRIBE,
                    false,
                    $fullUrl,
                    $userInfo,
                    $campaign,
                    $subscription,
                    ['cid' => $campaignToken],
                    $ex->getMessage()
                );
            }

            $this->entityManager->persist($entity);
            $this->entityManager->flush();

            $this->logger->debug('end AffiliateSender::checkAffiliateEligibilityAndSendEvent(): success');
        } catch (WrongIncomingParameters $e) {
            $this->logger->debug('ending with error AffiliateSender::checkAffiliateEligibilityAndSendEvent(): not full data in request');
        }
    }

    private function isCampaignConnectedToCarrier(CampaignInterface $campaign, CarrierInterface $carrier): bool
    {
        $ids = array_map(
            function (CarrierInterface $carrier) {
                return $carrier->getBillingCarrierId();
            },
            $campaign->getCarriers()->getValues()
        );

        return in_array($carrier->getBillingCarrierId(), $ids);
    }

    public function areParametersEqual(AffiliateInterface $affiliate, array $campaignParams): bool
    {
        $paramsList = $affiliate->getParamsList();
        if (!array_diff_key(array_flip($paramsList), $campaignParams)) {
            return true;
        }

        return false;
    }

    /**
     * @param AffiliateInterface $affiliate
     * @param CampaignInterface  $campaign
     * @param array              $campaignParams
     *
     * @return array
     * @throws WrongIncomingParameters
     */
    private function getPostBackParameters(AffiliateInterface $affiliate,
                                           CampaignInterface $campaign,
                                           array $campaignParams): array
    {
        $query = [];

        if ($affiliate->isUniqueFlow()) {
            $query = $this->jumpIntoUniqueFlow($affiliate, $campaignParams);
        } else {
        $paramsList = $affiliate->getParamsList();
        $constantsList = $affiliate->getConstantsList();
        $query = $this->jumpIntoStandartFlow($paramsList, $constantsList, $campaignParams, $query);
        $this->logger->debug('check content in getPostBackParameters()', [
            'query' => $query
        ]);
        };

        if (!$affiliate->isUniqueFlow() && $subPriceName = $affiliate->getSubPriceName()) {
            $query = array_merge(
                $query,
                [$subPriceName => $campaign->getSub() ?? null]
            );
        }

        $this->logger->debug('check content in getPostBackParameters()', [
            'query' => $query
        ]);

        return $query;
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
            'paramsList (from DB)' => $paramsList,
            'campaignParams (from url)' => $campaignParams,
            'constantsList' => $constantsList,
        ]);

        if (!empty($paramsList)) {
            foreach ($paramsList as $output => $input) {
                try{
                    $query[$output] = $campaignParams[$input]; // !isset($campaignParams[$input])
                } catch (\ErrorException $e) {
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
     * @param array     $campaignParams
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
}