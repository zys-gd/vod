<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 14.01.19
 * Time: 16:24
 */

namespace SubscriptionBundle\Affiliate\Service;


use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Exception\RequestException;
use IdentificationBundle\Entity\CarrierInterface;
use SubscriptionBundle\Affiliate\DTO\UserInfo;
use SubscriptionBundle\Entity\Affiliate\AffiliateLog;
use SubscriptionBundle\Entity\Affiliate\CampaignInterface;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Repository\Affiliate\CampaignRepositoryInterface;

class AffiliateSender
{
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

    public function checkAffiliateEligibilityAndSendEvent(Subscription $subscription, UserInfo $userInfo, string $campaignToken = null): void
    {
        return;

        if (!$campaignToken) {
            return;
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

        $data = ['query' => '']; // TODO implement _formAffiliateData

        $fullUrl = $affiliate->getPostbackUrl() . '?' . http_build_query($data['query']);


        try {
            $client = $this->clientFactory->getClient();
            $client->request('GET', $affiliate->getPostbackUrl(), $data);
            $entity = $this->affiliateLogFactory->create(AffiliateLog::EVENT_SUBSCRIBE, true, $fullUrl, null, $userInfo);
        } catch (RequestException $ex) {
            $entity = $this->affiliateLogFactory->create(AffiliateLog::EVENT_SUBSCRIBE, false, $fullUrl, $ex->getMessage(), $userInfo);
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

    private function sendAffiliatePostback(\App\Domain\Entity\Affiliate $affiliate, UserInfo $userInfo)
    {


    }
}