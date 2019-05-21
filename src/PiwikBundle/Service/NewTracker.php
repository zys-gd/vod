<?php

namespace PiwikBundle\Service;

use App\Domain\Constants\ConstBillingCarrierId;
use App\Domain\Entity\Affiliate;
use App\Domain\Entity\Campaign;
use App\Domain\Entity\Carrier;
use App\Domain\Entity\Game;
use App\Domain\Entity\UploadedVideo;
use App\Domain\Repository\CampaignRepository;
use DeviceDetectionBundle\Service\Device;
use IdentificationBundle\Entity\User;
use LegacyBundle\Service\Exchanger;
use PiwikBundle\Api\ClientAbstract;
use PiwikBundle\Api\JsClient;
use PiwikBundle\Api\PhpClient;
use SubscriptionBundle\Affiliate\Service\AffiliateVisitSaver;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Service\SubscriptionPackProvider;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class NewTracker
{
    const TRACK_SUBSCRIBE     = 'trackSubscribe';
    const TRACK_RESUBSCRIBE   = 'trackResubscribe';
    const TRACK_RENEW         = 'trackRenew';
    const TRACK_UNSUBSCRIBE   = 'trackUnsubscribe';
    const TRACK_DOWNLOAD      = 'trackDownload';
    const TRACK_PLAYING_VIDEO = 'trackPlayingVideo';

    protected $exchangeService;
    protected $container;
    protected $user;
    protected $operator;
    protected $affiliate;
    protected $campaign;
    protected $aff_publisher;
    protected $msisdn;
    protected $connection;
    protected $country;
    protected $ip;
    protected $customVars = [
        'operator' => [
            'id' => 6,
            'name' => 'operator',
        ],
        'affiliate' => [
            'id' => 7,
            'name' => 'affiliate',
        ],
        'publisher' => [
            'id' => 8,
            'name' => 'publisher',
        ],
        'subscription-type' => [
            'id' => 10,
            'name' => 'subscription-type',
        ],
        'aff_publisher' => [
            'id' => 9,
            'name' => 'aff_publisher',
        ],

        'msisdn' => [
            'id' => 1,
            'name' => 'msisdn',
        ],
        'connection' => [
            'id' => 2,
            'name' => 'connection',
        ],
        'conversion_mode' => [
            'id' => 3,
            'name' => 'conversion_mode',
        ],
        'currency' => [
            'id' => 4,
            'name' => 'currency',
        ],
        'provider' => [
            'id' => 5,
            'name' => 'provider',
        ],
        'device_screen_height' => [
            'id' => 11,
            'name' => 'device_screen_height',
        ],
        'device_screen_width' => [
            'id' => 12,
            'name' => 'device_screen_width',
        ],
        'game_name' => [
            'id' => 13,
            'name' => 'game_name'
        ],
        'game_uuid' => [
            'id' => 14,
            'name' => 'game_uuid'
        ],
        'video_name' => [
            'id' => 15,
            'name' => 'video_name'
        ],
        'video_uuid' => [
            'id' => 16,
            'name' => 'video_uuid'
        ]
    ];
    /**
     * @var SubscriptionPackProvider
     */
    private $subscriptionPackProvider;
    /**
     * @var SessionInterface
     */
    private $session;
    /**
     * @var CampaignRepository
     */
    private $campaignRepository;
    /**
     * @var Device
     */
    private $device;
    /**
     * @var ClientAbstract
     */
    private $piwikClient;
    /**
     * @var PiwikDataMapper
     */
    private $piwikDataMapper;


    /**
     * NewTracker constructor.
     *
     * @param PhpClient                        $phpClient
     * @param JsClient                         $jsClient
     * @param                                  $jsEnabled
     * @param Exchanger                        $exchangeService
     * @param SubscriptionPackProvider         $subscriptionPackProvider
     * @param SessionInterface                 $session
     * @param string                           $campaignSessionName
     * @param CampaignRepository               $campaignRepository
     * @param Device                           $device
     */
    public function __construct(
        ClientAbstract $piwikClient,
        PiwikDataMapper $piwikDataMapper
    )
    {
        $this->piwikClient     = $piwikClient;
        $this->piwikDataMapper = $piwikDataMapper;
    }

    /**
     * NewTracker::trackPage()
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
     * NewTracker::addStandardVariables()
     * Adds visit custom variables
     *
     * @param User|null                                    $user
     * @param null                                         $connection
     * @param null                                         $operator
     * @param null                                         $country
     * @param null                                         $ip
     * @param null                                         $msisdn
     * @param null                                         $affiliate
     * @param null                                         $campaign
     * @param null                                         $aff_publisher
     * @param null|\SubscriptionBundle\Entity\Subscription $subscription
     *
     * @return ClientAbstract
     * @throws \Exception
     */
    protected function addStandardVariables(
        User $user = null,

        $subscription = null,

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
        $ret = false;
        /** @var User $user */
        $user = $user ?: $this->user;
        $connection = $connection ?: $this->connection;
        $operator = $operator ?: $this->operator;
        $country = $country ?: $this->country;
        $ip = $ip ?: $this->ip;
        $msisdn = $msisdn ?: $this->msisdn;
        $affiliate = $affiliate ?: $this->affiliate;
        $campaign = $campaign ?: $this->campaign;
        $aff_publisher = $aff_publisher ?: $this->aff_publisher;

        if ($user) {
            $ret = $this->piwikClient->setUserId($user->getUuid());
            $this->user = $user;

            if ($userCountry = $user->getCountry()) {
                $country = $userCountry;
            }
            if ($userIp = $user->getIp()) {
                $ip = $userIp;
            }
            if ($userMsisdn = $user->getIdentifier()) {
                $msisdn = $userMsisdn;
            }

            if ($userOperator = $user->getCarrier()) {
                $operator = $userOperator->getBillingCarrierId();
            }
        }
        if ($connection) {
            $ret = $this->addVariable('connection', $connection);
            $this->connection = $connection;
        }
        if ($operator) {
            $ret = $this->addVariable('operator', $operator);
            $this->operator = $operator;
        }
        if ($country) {
            $this->piwikClient->setCountry($country);
            $this->country = $country;
        }
        if ($ip) {
            $this->piwikClient->setIp($ip);
            $this->ip = $ip;
        }
        if (!$msisdn) {
            $msisdn = $this->session->get('msisdn');
        }
        if ($msisdn) {
            $ret = $this->addVariable('msisdn', $msisdn);
            $this->msisdn = $msisdn;
        }

        if (!$affiliate) {

           $token = AffiliateVisitSaver::extractCampaignToken($this->session);

            if (empty($token) && $subscription) {
//                if ($subscription->isUnsubscribed() || $subscription->isRenew()) {
//                    if ($lastInHistory = $subscription->getLastSubscriptionHistory()) {
//                        $campaignParams = $lastInHistory->getAffiliateToken();
//                    }
//                }
                if (empty($token)) {
                    $token = $subscription->getAffiliateToken();
                }
            }
            if (!empty($token)
                && ($propCampaign = $this->campaignRepository->findOneBy(['campaignToken' => $token]))
            ) {
                $propAffiliate = $propCampaign->getAffiliate();
                if ($propAffiliate) {
                    /** @var Affiliate $affiliate */
                    $affiliate = $propAffiliate;
                    /** @var Campaign $campaign */
                    $campaign = $propCampaign;
                }
            }
        }
        if ($affiliate && $operator && $this->isAppropriateCampaign($campaign, $operator)) {
            $affiliateString = $affiliate->getUuid();
            if ($campaign) {
                $affiliateString .= '@' . $campaign->getUuid();
                $this->campaign = $campaign;
            }
            $ret = $this->addVariable('affiliate', $affiliateString);
            $this->affiliate = $affiliate;
        }

        if ($aff_publisher) {
            $ret = $this->addVariable('aff_publisher', $aff_publisher);
            $this->aff_publisher = $aff_publisher;
        }

        $this->addVariable('device-screen-height', $this->device->getDisplayHeight());

        $this->addVariable('device-screen-width', $this->device->getDisplayWidth());


        return $ret;
    }

    /**
     * NewTracker::addVariable()
     * Adds a pre-defined variable to the Piwik object
     *
     * @param        $key
     * @param        $value
     * @param string $scope
     * Adds a pre-defined variable to the Piwik object
     *
     * @return mixed
     * @throws \Exception
     */
    protected function addVariable($key, $value, $scope = 'visit')
    {
        $ret = false;
        if (isset($this->customVars[$key])) {
            $ret = $this->piwikClient->setCustomVariable($this->customVars[$key]['id'], $this->customVars[$key]['name'], $value, $scope);
        }
        return $ret;
    }

    /**
     * @param Campaign $campaign
     * @param int  $carrierId
     *
     * @return bool
     */
    private function isAppropriateCampaign(Campaign $campaign, int $carrierId)
    {
        $result = true;
        if (!$carrierId) {
            $campaignCarriers = $campaign->getCarriers()->getValues();
            foreach ($campaignCarriers as $campaignCarrier) {
                /** @var Carrier $campaignCarrier */
                if ($campaignCarrier->getIsCampaignsOnPause()) {
                    $result = false;
                }
            }

        }
        else {
            $carriers = $campaign->getCarriers()->filter(function (Carrier $carrier) use ($carrierId) {
                return $carrier->getBillingCarrierId() === $carrierId;
            });

            $result = $carriers->isEmpty() ? false : !$carriers->first()->getIsCampaignsOnPause();
        }

        return $result;
    }

    /**
     * @param User         $user
     * @param Subscription $subscription
     * @param              $bfResponse
     * @param null         $conversionMode
     *
     * @return bool
     * @throws \Exception
     */
    public function trackResubscribe(User $user,
        Subscription $subscription,
        $bfResponse,
        $conversionMode = null
    )
    {
        return $this->trackSubscribe($user, $subscription, $bfResponse, $conversionMode, 'resubscribe');
    }

    /**
     * NewTracker::trackSubscribe()
     * Tracks to Piwik a subscription conversion, whether successful or failed (determined by analyzing the $bfResponse argument)
     *
     * @param User $user
     * @param Subscription $subscription
     * @param ProcessResult $bfResponse
     * @param null|string $conversionMode
     * @param string $type
     *
     * @return bool
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \SubscriptionBundle\Exception\ActiveSubscriptionPackNotFound
     * @throws \Exception
     */
    public function trackSubscribe(User $user,
        Subscription $subscription,
        ProcessResult $bfResponse,
        string $conversionMode = null,
        string $type = 'subscribe'
    ): bool
    {
        $oSubPack = $this->subscriptionPackProvider->getActiveSubscriptionPack($user);

        // Refactor candidate.
        // In v1 this method has been used during renew process
        if (
            $user->getCarrierId() == ConstBillingCarrierId::HUTCH3_INDONESIA &&
            $bfResponse->getStatus() === ProcessResult::STATUS_FAILED &&
            $bfResponse->getError() === ProcessResult::ERROR_CANCELED
        ) {
            return false;
        }

        if (!$oSubPack
            || ($bfResponse->getType() !== $type ? !($bfResponse->getType() == 'subscribe' && $type == 'resubscribe') : false)
            || !in_array($type, ['subscribe', 'renew', 'resubscribe'])
            || !in_array($bfResponse->getStatus(), ['successful', 'failed'])
        ) {
            return false;
        }

        if ($oSubPack->getCarrier()->getBillingCarrierId() == ConstBillingCarrierId::HUTCH3_INDONESIA
            && $bfResponse->getStatus() === ProcessResult::STATUS_FAILED
            && $bfResponse->getError() === ProcessResult::ERROR_CANCELED) {
            return false;
        }

        $bfSuccess = $bfResponse->getStatus() === 'successful';
        $bfId = $bfResponse->getId();
        $bfProvider = $bfResponse->getProvider();

        $subscriptionPackId = abs($oSubPack->getUuid());

        $eurPrice = $this->exchangeService->convert($oSubPack->getTierCurrency(), $oSubPack->getTierPrice());
        $subscriptionPrice = round($oSubPack->getTierPrice(), 2);
        $name = $type . '-' . ($bfSuccess ? 'ok' : 'failed');

        $orderIdPieces = [
            $name,
            $subscription->getUuid(),
            $subscriptionPackId,
            $bfId,
            $subscriptionPrice,
        ];
        $orderId = implode('-', $orderIdPieces);

        $this->piwikClient->clearCustomVariables();
        $this->addStandardVariables($user, $subscription);
        $this->addVariable('currency', $oSubPack->getTierCurrency());
        $this->addVariable('provider', $bfProvider);

        if ($conversionMode) {
            $this->addVariable('conversion_mode', $conversionMode);
        }
        return $this->sendEcommerce($orderId, $eurPrice, $name . '-' . $subscriptionPackId, $type);
    }

    /**
     * NewTracker::sendEcommerce()
     * Common method for the tracking of subscriptions, renewals, unsubscriptions and downloads
     *
     * @param $orderId
     * @param $orderValue
     * @param $prodSku
     * @param $prodCat
     *
     * @return bool
     * @throws \Exception
     */
    protected function sendEcommerce($orderId, $orderValue, $prodSku, $prodCat)
    {
        $this->piwikClient->addEcommerceItem(
            $prodSku,
            $prodSku,
            $prodCat,
            $orderValue,
            1
        );

        $ret = (bool)$this->piwikClient->doTrackEcommerceOrder(
            $orderId,
            $orderValue
        );
        return $ret;
    }

    /**
     * NewTracker::trackRenew()
     * Tracks to Piwik a renewal conversion, whether successful or failed (determined by analyzing the $bfResponse argument)
     *
     * @param User         $user
     * @param Subscription $subscription
     * @param              $bfResponse
     * @param null|string    $conversionMode
     *
     * @return bool
     * @throws \Exception
     */
    public function trackRenew(User $user,
        Subscription $subscription,
        ProcessResult $bfResponse,
        string $conversionMode = null
    ): bool
    {
        return $this->trackSubscribe($user, $subscription, $bfResponse, $conversionMode, 'renew');
    }

    /**
     * NewTracker::trackUnsubscribe()
     * Tracks to Piwik an unsubscription conversion, whether successful or failed (determined by analyzing the $bfResponse argument).
     * The $bfResponse can be lacking when the unsubscription is internal ($conversionMode will be sent as 'internal' if not otherwise specified)
     *
     * @param User         $user
     * @param Subscription $subscription
     * @param              $bfResponse
     * @param null|string    $conversionMode
     *
     * @return bool
     * @throws \Exception
     */
    public function trackUnsubscribe(User $user,
        Subscription $subscription,
        ProcessResult $bfResponse = null,
        string $conversionMode = null
    ): bool
    {
        $bfId = $bfProvider = $oSubPack = false;
        $bfSuccess = true;

        $oSubPack = $this->subscriptionPackProvider->getActiveSubscriptionPack($user);

        $bfWrong = $bfResponse
            && $bfResponse->getError() != ProcessResult::ERROR_BATCH_LIMIT_EXCEEDED
            && $bfResponse->getError() != ProcessResult::ERROR_USER_TIMEOUT
            && !($bfResponse->getError() == ProcessResult::ERROR_CANCELED && $oSubPack->getCarrier()->getBillingCarrierId() == ConstBillingCarrierId::ROBI_BANGLADESH)
            &&
            (
                $bfResponse->getType() !== 'unsubscribe'
                || !in_array($bfResponse->getStatus(), ['successful', 'failed', 'ok'])
            );

        if (!$oSubPack || $bfWrong) {
            return false;
        }

        if ($bfResponse) {
            $bfSuccess = $bfResponse->getError() == ProcessResult::ERROR_BATCH_LIMIT_EXCEEDED ||
            $bfResponse->getError() == ProcessResult::ERROR_USER_TIMEOUT ||
            ($bfResponse->getError() == ProcessResult::ERROR_CANCELED && $oSubPack->getCarrier()->getBillingCarrierId() == ConstBillingCarrierId::ROBI_BANGLADESH)
                ? true : ($bfResponse->getStatus() === 'successful' || $bfResponse->getStatus() === 'ok');
            $bfId = $bfResponse->getId();
            $bfProvider = $bfResponse->getProvider();
        }

        $subscriptionPlanId = abs($oSubPack->getUuid());
        $eurPrice = $this->exchangeService->convert($oSubPack->getTierCurrency(), $oSubPack->getTierPrice());
        $subscriptionPrice = round($oSubPack->getTierPrice(), 2);

        $name = 'unsubscribe-' . ($bfSuccess ? 'ok' : 'failed');
        $orderIdPieces = [
            $name,
            $subscription->getUuid(),
            $subscriptionPlanId,
            $bfId ?: 'N' . rand(1000, 9999),
            $subscriptionPrice,
            mt_rand(0, 9999)
        ];
        $orderId = implode('-', $orderIdPieces);

        $this->piwikClient->clearCustomVariables();
        $this->addStandardVariables($user, $subscription);

        $this->addVariable('currency', $oSubPack->getTierCurrency());

        if ($bfProvider) {
            $this->addVariable('provider', $bfProvider);
        }
        if (!$conversionMode && !$bfResponse) {
            $conversionMode = 'internal';
        }
        if ($conversionMode) {
            $this->addVariable('conversion_mode', $conversionMode);
        }
        return $this->sendEcommerce($orderId, $eurPrice, $name . '-' . $subscriptionPlanId, 'unsubscribe');
    }

    /**
     * NewTracker::trackDownload()
     * Tracks to Piwik a game download, The $subscription argument is optional.
     *
     * @param User              $user
     * @param Game              $game
     * @param Subscription|null $subscription
     * @param null|string         $conversionMode
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

        $type = 'download-ok';
        $prodSku = 'download-' . $game->getUuid();

        $orderIdPieces = [
            $type,
            $subscriptionId,
            $subscriptionPlanId,
            $game->getUuid(),
            'N' . rand(1000, 9999),
        ];
        $orderId = implode('-', $orderIdPieces);
        return $this->sendEcommerce($orderId, 0.01, $prodSku, 'game');
    }


    /**
     * @param User $user
     * @param UploadedVideo $uploadedVideo
     * @param Subscription $subscription
     * @param null|string $conversionMode
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function trackVideoPlaying(
        User $user,
        UploadedVideo $uploadedVideo,
        Subscription $subscription,
        string $conversionMode = null
    ) {
        $this->piwikClient->clearCustomVariables();
        $this->addStandardVariables($user, $subscription);

        $this->addVariable('video_name', $uploadedVideo->getTitle());
        $this->addVariable('video_uuid', $uploadedVideo->getUuid());

        if ($conversionMode) {
            $this->addVariable('conversion_mode', $conversionMode);
        }

        $subscriptionPack = $this->subscriptionPackProvider->getActiveSubscriptionPack($user);

        $type = 'playing-video';
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