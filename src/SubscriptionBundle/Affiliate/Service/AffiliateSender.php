<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 14.01.19
 * Time: 16:24
 */

namespace SubscriptionBundle\Affiliate\Service;


use App\Domain\Entity\Affiliate;
use App\Domain\Entity\Campaign;
use Doctrine\ORM\EntityManagerInterface;
use IdentificationBundle\Entity\CarrierInterface;
use SubscriptionBundle\Affiliate\DTO\UserInfo;
use SubscriptionBundle\Entity\Affiliate\AffiliateLog;
use SubscriptionBundle\Entity\Affiliate\CampaignInterface;
use SubscriptionBundle\Entity\Subscription;
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
     * AffiliateSender constructor.
     * @param CampaignRepositoryInterface $campaignRepository
     * @param EntityManagerInterface      $entityManager
     * @param GuzzleClientFactory         $clientFactory
     * @param AffiliateLogFactory         $affiliateLogFactory
     */
    public function __construct(
        CampaignRepositoryInterface $campaignRepository,
        EntityManagerInterface $entityManager,
        GuzzleClientFactory $clientFactory,
        AffiliateLogFactory $affiliateLogFactory
    )
    {
        $this->campaignRepository  = $campaignRepository;
        $this->entityManager       = $entityManager;
        $this->clientFactory       = $clientFactory;
        $this->affiliateLogFactory = $affiliateLogFactory;
    }

    public function checkAffiliateEligibilityAndSendEvent(
        Subscription $subscription,
        UserInfo $userInfo,
        array $campaignParams = null,
        string $campaignToken = null
    ): void
    {

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


        $data = ['query' => $this->getPostBackParameters($affiliate, $campaign, $campaignParams)]; // TODO implement _formAffiliateData
        $fullUrl = $affiliate->getPostbackUrl() . '?' . http_build_query($data['query']);


        try {
            $client = $this->clientFactory->getClient();
            $client->request('GET', $affiliate->getPostbackUrl(), $data);
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

    private function getPostBackParameters(Affiliate $affiliate, CampaignInterface $campaign, array $campaignParams): array
    {
        $query = [];

        /*if ($affiliate->isUniqueFlow()) {
            $query[] = $this->jumpIntoUniqueFlow($affiliate, $campaignParams);
        } else {*/
        $paramsList    = $affiliate->getParamsList();
        $constantsList = $affiliate->getConstantsList();
        $query         = $this->jumpIntoStandartFlow($paramsList, $constantsList, $campaignParams, $query);
        /*};*/

        if ($subPriceName = $affiliate->getSubPriceName()) {
            $query = array_merge(
                $query,
                [$subPriceName => $campaign->getSub() ?? null]
            );
        }

        return [
            'headers' => [
                'User-Agent' => self::USER_AGENT
            ],
            'query'   => $query,
            'debug'   => false
        ];
    }

    /**
     * @param Affiliate $affiliate
     * @param array     $campaignParams
     *
     * @return array|string
     */
    private function jumpIntoUniqueFlow(Affiliate $affiliate, array $campaignParams)
    {
        if (!empty($campaignParams) && array_key_exists($affiliate->getUniqueParameter(), $campaignParams)) {
            $uniqueParameterValue = $campaignParams[$affiliate->getUniqueParameter()];
            $url                  = $affiliate->getPostBackUrlUniqueFlow($uniqueParameterValue);

            return $url;
        }

        return [];
    }

    /**
     * @param array $paramsList
     * @param array $constantsList
     * @param array $campaignParams
     * @param array $query
     *
     * @return array
     */
    private function jumpIntoStandartFlow(array $paramsList, array $constantsList, array $campaignParams, array $query)
    {
        if (!empty($paramsList)) {
            foreach ($paramsList as $output => $input) {
                $query[$output] = $campaignParams[$input];
            }
        }
        if (!empty($constantsList)) {
            $query = array_merge($constantsList, $query);
        }

        return $query;


    }


    public function areParametersEqual(Affiliate $affiliate, array $campaignParams): bool
    {
        $paramsList = $affiliate->getParamsList();
        if (!array_diff_key(array_flip($paramsList), $campaignParams)) {
            return true;
        }

        return false;
    }
}