<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 14.01.19
 * Time: 16:24
 */

namespace SubscriptionBundle\Affiliate\Service;

use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use SubscriptionBundle\Affiliate\CampaignConfirmation\Handler\CampaignConfirmationHandlerProvider;
use SubscriptionBundle\Affiliate\CampaignConfirmation\Handler\HasInstantConfirmation;
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
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var CampaignConfirmationHandlerProvider
     */
    private $handlerProvider;
    /**
     * @var AffiliateLogFactory
     */
    private $affiliateLogFactory;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * AffiliateSender constructor.
     * @param CampaignRepositoryInterface         $campaignRepository
     * @param LoggerInterface                     $logger
     * @param CampaignConfirmationHandlerProvider $handlerProvider
     * @param AffiliateLogFactory                 $affiliateLogFactory
     * @param EntityManagerInterface              $entityManager
     */
    public function __construct(
        CampaignRepositoryInterface $campaignRepository,
        LoggerInterface $logger,
        CampaignConfirmationHandlerProvider $handlerProvider,
        AffiliateLogFactory $affiliateLogFactory,
        EntityManagerInterface $entityManager
    )
    {
        $this->campaignRepository  = $campaignRepository;
        $this->logger              = $logger;
        $this->handlerProvider     = $handlerProvider;
        $this->affiliateLogFactory = $affiliateLogFactory;
        $this->entityManager       = $entityManager;
    }


    public function checkAffiliateEligibilityAndSendEvent(
        Subscription $subscription, UserInfo $userInfo, string $campaignToken = null, array $userCampaignData = []
    ): void
    {
        $this->logger->debug('start AffiliateSender::checkAffiliateEligibilityAndSendEvent()', [
            'userInfo'      => $userInfo,
            'campaignData'  => $userCampaignData,
            'campaignToken' => $campaignToken
        ]);

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


        $affiliate           = $campaign->getAffiliate();
        $confirmationHandler = $this->handlerProvider->getHandlerForAffiliateId($affiliate->getUuid());

        if ($confirmationHandler instanceof HasInstantConfirmation) {
            $confirmationHandler->doConfirm(
                $affiliate,
                $campaign,
                $userCampaignData,
                $subscription,
                $userInfo
            );
        } else {
            $entity = $this->affiliateLogFactory->create(
                AffiliateLog::STATUS_WAITING,
                '',
                $userInfo,
                $campaign,
                $subscription,
                $userCampaignData
            );
            $this->entityManager->persist($entity);
            $this->entityManager->flush();
        }

        $this->logger->debug('end AffiliateSender::checkAffiliateEligibilityAndSendEvent(): success');

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


}