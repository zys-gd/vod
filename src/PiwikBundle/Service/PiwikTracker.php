<?php

namespace PiwikBundle\Service;

use App\Domain\Entity\Affiliate;
use App\Domain\Entity\Campaign;
use App\Domain\Entity\Carrier;
use App\Domain\Entity\Game;
use App\Domain\Entity\UploadedVideo;
use IdentificationBundle\Entity\User;
use PiwikBundle\Api\ClientAbstract;
use PiwikBundle\Service\DTO\EcommerceDTO;
use SubscriptionBundle\Affiliate\Service\AffiliateVisitSaver;
use SubscriptionBundle\Entity\Subscription;


class PiwikTracker
{
    const TRACK_SUBSCRIBE     = 'trackSubscribe';
    const TRACK_RESUBSCRIBE   = 'trackResubscribe';
    const TRACK_RENEW         = 'trackRenew';
    const TRACK_UNSUBSCRIBE   = 'trackUnsubscribe';
    const TRACK_DOWNLOAD      = 'trackDownload';
    const TRACK_PLAYING_VIDEO = 'trackPlayingVideo';

    /**
     * @var ClientAbstract
     */
    private $piwikClient;


    /**
     * PiwikTracker constructor.
     *
     * @param ClientAbstract $piwikClient
     */
    public function __construct(ClientAbstract $piwikClient)
    {
        $this->piwikClient = $piwikClient;
    }

    /**
     * PiwikTracker::trackPage()
     * Tracks to Piwik a single page visit
     *
     * @param User|null $user
     * @param null      $connection
     * @param null      $operator
     * @param null      $country
     * @param null      $ip
     * @param null      $msisdn
     * @param null      $affiliate
     * @param null      $campaign
     * @param null      $aff_publisher
     *
     * @return bool
     * @throws \Exception
     */
    public function trackPage(
        User $user = null,
        $connection = null,
        $operator = null,
        $country = null,
        $ip = null,
        $msisdn = null,
        $affiliate = null,
        $campaign = null,
        $aff_publisher = null
    )
    {
        $this->piwikClient->clearCustomVariables();
        $this->addStandardVariables(
            $user,
            null,
            $connection,
            $operator,
            $country,
            $ip,
            $msisdn,
            $affiliate,
            $campaign,
            $aff_publisher
        );
        $ret = (bool)$this->piwikClient->doTrackPageView('');
        return $ret;
    }

    /**
     * @param EcommerceDTO $ecommerceDTO
     *
     * @return bool
     * @throws \Exception
     */
    public function sendEcommerce(EcommerceDTO $ecommerceDTO)
    {
        $this->piwikClient->addEcommerceItem(
            $ecommerceDTO->getProdSku(),
            $ecommerceDTO->getProdSku(),
            $ecommerceDTO->getProdCat(),
            $ecommerceDTO->getOrderValue(),
            1
        );

        $ret = (bool)$this->piwikClient->doTrackEcommerceOrder(
            $ecommerceDTO->getOrderId(),
            $ecommerceDTO->getOrderValue()
        );
        return $ret;
    }

    /**
     * PiwikTracker::trackDownload()
     * Tracks to Piwik a game download, The $subscription argument is optional.
     *
     * @param User              $user
     * @param Game              $game
     * @param Subscription|null $subscription
     * @param null|string       $conversionMode
     *
     * @return bool
     * @throws \Exception
     */
    public function trackDownload(User $user,
        Game $game,
        Subscription $subscription = null,
        string $conversionMode = null
    ): bool
    {
        $this->piwikClient->clearCustomVariables();
        $this->addStandardVariables($user, $subscription);

        $this->addVariable('game_name', $game->getName());
        $this->addVariable('game_uuid', $game->getUuid());

        $oSubPack = $this->subscriptionPackProvider->getActiveSubscriptionPack($user);

        $subscriptionId = $subscription ? $subscription->getUuid() : 0;

        $subscriptionPlanId = $subscription ? abs($oSubPack->getUuid()) : 0;
        if ($conversionMode) {
            $this->addVariable('conversion_mode', $conversionMode);
        }

        $type    = 'download-ok';
        $prodSku = 'download-' . $game->getUuid();

        $orderIdPieces = [
            $type,
            $subscriptionId,
            $subscriptionPlanId,
            $game->getUuid(),
            'N' . rand(1000, 9999),
        ];
        $orderId       = implode('-', $orderIdPieces);
        return $this->sendEcommerce($orderId, 0.01, $prodSku, 'game');
    }


    /**
     * @param User          $user
     * @param UploadedVideo $uploadedVideo
     * @param Subscription  $subscription
     * @param null|string   $conversionMode
     *
     * @return bool
     * @throws \Exception
     */
    public function trackVideoPlaying(
        User $user,
        UploadedVideo $uploadedVideo,
        Subscription $subscription,
        string $conversionMode = null
    )
    {
        $this->piwikClient->clearCustomVariables();
        $this->addStandardVariables($user, $subscription);

        $this->addVariable('video_name', $uploadedVideo->getTitle());
        $this->addVariable('video_uuid', $uploadedVideo->getUuid());

        if ($conversionMode) {
            $this->addVariable('conversion_mode', $conversionMode);
        }

        $subscriptionPack = $this->subscriptionPackProvider->getActiveSubscriptionPack($user);

        $type    = 'playing-video';
        $prodSku = 'playing-video-' . $uploadedVideo->getUuid();

        $orderIdPieces = [
            $type,
            $subscription->getUuid(),
            $subscriptionPack->getUuid(),
            $uploadedVideo->getUuid(),
            'N' . rand(1000, 9999)
        ];

        $orderId = implode('-', $orderIdPieces);

        return $this->sendEcommerce($orderId, 0.01, $prodSku, $uploadedVideo->getSubcategory()->getTitle());
    }
}