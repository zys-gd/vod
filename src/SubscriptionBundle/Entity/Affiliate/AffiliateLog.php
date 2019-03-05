<?php

namespace SubscriptionBundle\Entity\Affiliate;

use Playwing\DiffToolBundle\Entity\Interfaces\HasUuid;

/**
 * AffiliateLog
 */
class AffiliateLog implements HasUuid
{
    const EVENT_VISIT = 1;
    /*modified in OPTI Tracking Fixes 2 to be a new value*/
    const EVENT_SUBSCRIBE = 12;
    const EVENT_RENEW = 3;
    const EVENT_UNSUBSCRIBE = 4;
    const EVENT_BUY = 5;
    const STATUS_SUCCESS = 1;
    const STATUS_FAILURE = 2;

    /**
     * @var string
     */
    private $uuid;

    /**
     * @var string
     */
    private $campaignToken;

    /**
     * @var string
     */
    private $subscriptionId;

    /**
     * @var string
     */
    private $userMsisdn;

    /**
     * @var string
     */
    private $userIp;

    /**
     * @var string
     */
    private $deviceModel;

    /**
     * @var string
     */
    private $deviceManufacturer;

    /**
     * @var string
     */
    private $deviceMarketingName;

    /**
     * @var string
     */
    private $deviceAtlasId;

    /**
     * @var string
     */
    private $connectionType;

    /**
     * @var string
     */
    private $country;

    /**
     * @var int
     */
    private $event;
    /**
     * @var int
     */
    private $status;

    /**
     * @var string
     */
    private $log;

    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $campaignParams;

    /**
     * Date when the request is added
     * @var \DateTime
     */
    private $addedAt;

    /**
     * AffiliateLog constructor
     *
     * @param string $uuid
     * @throws \Exception
     */
    public function __construct(string $uuid)
    {
        $this->uuid = $uuid;
        $this->addedAt = new \DateTime();
    }

    /**
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * @param string $uuid
     */
    public function setUuid(string $uuid): void
    {
        $this->uuid = $uuid;
    }

    /**
     * Set campaignToken
     *
     * @param string $campaignToken
     */
    public function setCampaignToken($campaignToken)
    {
        $this->campaignToken = $campaignToken;
    }

    /**
     * Get campaignToken
     *
     * @return string
     */
    public function getCampaignToken()
    {
        return $this->campaignToken;
    }

    /**
     * Set userMsisdn
     *
     * @param $userMsisdn
     */
    public function setUserMsisdn($userMsisdn)
    {
        $this->userMsisdn = $userMsisdn;
    }

    /**
     * Get userMsisdn
     *
     * @return string
     */
    public function getUserMsisdn()
    {
        return $this->userMsisdn;
    }

    /**
     * Set userIp
     *
     * @param string $userIp
     */
    public function setUserIp($userIp)
    {
        $this->userIp = $userIp;
    }

    /**
     * Get userIp
     *
     * @return string
     */
    public function getUserIp()
    {
        return $this->userIp;
    }

    /**
     * Get deviceModel
     *
     * @return string
     */
    public function getDeviceModel()
    {
        return $this->deviceModel;
    }

    /**
     * Set deviceModel
     *
     * @param string $deviceModel
     */
    public function setDeviceModel($deviceModel)
    {
        $this->deviceModel = $deviceModel;
    }

    /**
     * Get deviceManufacturer
     *
     * @return string
     */
    public function getDeviceManufacturer()
    {
        return $this->deviceManufacturer;
    }

    /**
     * Set deviceManufacturer
     *
     * @param string $deviceManufacturer
     */
    public function setDeviceManufacturer($deviceManufacturer)
    {
        $this->deviceManufacturer = $deviceManufacturer;
    }

    /**
     * Get deviceMarketingName
     *
     * @return string
     */
    public function getDeviceMarketingName()
    {
        return $this->deviceMarketingName;
    }

    /**
     * Set deviceMarketingName
     *
     * @param string $deviceMarketingName
     */
    public function setDeviceMarketingName($deviceMarketingName)
    {
        $this->deviceMarketingName = $deviceMarketingName;
    }

    /**
     * Get deviceAtlasId
     *
     * @return string
     */
    public function getDeviceAtlasId()
    {
        return $this->deviceAtlasId;
    }

    /**
     * Set deviceAtlasId
     *
     * @param string $deviceAtlasId
     */
    public function setDeviceAtlasId($deviceAtlasId)
    {
        $this->deviceAtlasId = $deviceAtlasId;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set country
     *
     * @param string $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     * Get connectionType
     *
     * @return string
     */
    public function getConnectionType()
    {
        return $this->connectionType;
    }

    /**
     * Set connectionType
     *
     * @param string $connectionType
     */
    public function setConnectionType($connectionType)
    {
        $this->connectionType = $connectionType;
    }

    /**
     * Set event
     *
     * @param integer $event
     */
    public function setEvent($event)
    {
        $this->event = $event;
    }

    /**
     * Get isSubscribe
     *
     * @return int
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Set status
     *
     * @param integer $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * Get status
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set log
     *
     * @param string $log
     */
    public function setLog($log)
    {
        $this->log = $log;
    }

    /**
     * Get log
     *
     * @return string
     */
    public function getLog()
    {
        return $this->log;
    }

    /**
     * Set url
     *
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set campaignParams
     *
     * @param array $campaignParams
     */
    public function setCampaignParams($campaignParams)
    {
        $this->campaignParams = json_encode($campaignParams);
    }

    /**
     * Get campaignParams
     *
     * @return array
     */
    public function getCampaignParams()
    {
        return json_decode($this->campaignParams, true);
    }

    /**
     * @param string $subscription_id
     */
    public function setSubscriptionId($subscription_id)
    {
        $this->subscriptionId = $subscription_id;
    }

    /**
     * @return string
     */
    public function getSubscriptionId()
    {
        return $this->subscriptionId;
    }

    /**
     * @return \DateTime
     */
    public function getAddedAt(): \DateTime
    {
        return $this->addedAt;
    }

    /**
     * @param \DateTime $addedAt
     * @return AffiliateLog
     */
    public function setAddedAt(\DateTime $addedAt): AffiliateLog
    {
        $this->addedAt = $addedAt;
        return $this;
    }

    /**
     * @return string
     */
    public function getFullDeviceInfo(): string
    {
        return
            $this->getDeviceModel() . ' ' .
            $this->getDeviceManufacturer() . ' ' .
            $this->getDeviceAtlasId() . ' ' .
            $this->getDeviceMarketingName();
    }
}