<?php

namespace SubscriptionBundle\Entity;

use IdentificationBundle\Entity\User;
use Playwing\DiffToolBundle\Entity\Interfaces\HasUuid;

class Subscription implements HasUuid
{
    // status
    const IS_INACTIVE = 0;
    const IS_ACTIVE = 1;
    const IS_PENDING = 2;
    const IS_ERROR = 3;
    const IS_ON_HOLD = 4;

    // stage
    const ACTION_SUBSCRIBE = 1;
    const ACTION_UNSUBSCRIBE = 2;
    const ACTION_RENEW = 3;

    const CURRENT_STAGE_MAP = array(
        1 => 'subscription',
        2 => 'un-subscription',
        3 => 'renewal'
    );

    /**
     * @var int
     *
     */
    private $uuid;

    /**
     * @var int
     *
     */
    private $credits = 0;

    /**
     * @var \DateTime $created
     *
     *
     */
    private $created;

    /**
     * @var \DateTime $updated
     *
     *
     */
    private $updated;

    /**
     * @var integer
     *
     */
    private $status;

    /**
     * @var User
     */
    private $user;

    /**
     * @var \datetime
     *
     */
    private $renewDate;


    /** @var SubscriptionPack */
    private $subscriptionPack;

    /**
     * @var integer
     *
     */
    private $currentStage;

    /**
     * @var string
     *
     */
    private $redirectUrl;

    /**
     * @var string
     *
     */
    private $affiliateToken;


    /**
     * @var null| integer
     */
    private $promotionTierId = null;

    /**
     * @var number
     */
    private $previousStage;

    /**
     * @var number
     */
    private $previousStatus;

    /**
     * Request status
     * @var string
     */
    protected $error = null;

    private $lastRenewAlertDate;

    /**
     * Subscription constructor.
     * @param string $uuid
     */
    public function __construct(string $uuid)
    {
        $this->uuid    = $uuid;
        $this->credits = 0;
        $this->created = new \DateTimeImmutable();
        $this->updated = new \DateTimeImmutable();

    }

    /**
     * @return int
     */
    public function getCredits()
    {
        return $this->getSubscriptionPack()->isUnlimited() ? 1000 : $this->credits;
    }

    /**
     * @param int $credits
     */
    public function setCredits($credits)
    {
        $this->credits = $credits;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return \DateTime
     */
    public function getRenewDate()
    {
        return $this->renewDate;
    }

    /**
     * @param \DateTimeInterface $renewDate
     */
    public function setRenewDate($renewDate)
    {
        $this->renewDate = $renewDate;
    }

    /**
     * @return SubscriptionPack
     */
    public function getSubscriptionPack()
    {
        return $this->subscriptionPack;
    }

    /**
     * @param SubscriptionPack $subscriptionPack
     */
    public function setSubscriptionPack($subscriptionPack)
    {
        $this->subscriptionPack = $subscriptionPack;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus($status)
    {
        $this->setPreviousStatus($this->status);
        $this->status = $status;
    }

    /**
     * @return boolean
     */
    public function isOnHold()
    {
        return $this->status === self::IS_ON_HOLD;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->status === self::IS_ACTIVE;
    }

    /**
     * @return bool
     */
    public function isInActive()
    {
        return $this->status === self::IS_INACTIVE;
    }

    /**
     * @return mixed
     */
    public function getCurrentStage()
    {
        return $this->currentStage;
    }

    public function getCurrentStageLabel()
    {
        return self::CURRENT_STAGE_MAP[$this->currentStage];
    }

    /**
     * @param mixed $currentStage
     */
    public function setCurrentStage($currentStage)
    {
        $this->setPreviousStage($this->currentStage);
        $this->currentStage = $currentStage;
    }

    /**
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->redirectUrl;
    }

    /**
     * @param string $redirectUrl
     */
    public function setRedirectUrl($redirectUrl)
    {
        $this->redirectUrl = $redirectUrl;
    }


    public function isPending()
    {
        return $this->status === self::IS_PENDING;
    }

    public function hasError()
    {
        return $this->status === self::IS_ERROR;
    }

    public function isRedirectRequired()
    {
        return !!isset($this->redirectUrl);
    }

    /**
     * @return int|null
     */
    public function getPromotionTierId()
    {
        return $this->promotionTierId;
    }

    /**
     * @param int|null $promotionTierId
     */
    public function setPromotionTierId($promotionTierId)
    {
        $this->promotionTierId = $promotionTierId;
    }


    /**
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param \DateTime $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }


    /**
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @param \DateTime $updated
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    }

    /**
     * Added to support older subscription  API
     * @return int
     */
    public function getAllowedCount()
    {
        return $this->getCredits();
    }

    /**
     * @return number
     */
    public function getPreviousStage()
    {
        return $this->previousStage;
    }

    /**
     * @param number $previousStage
     */
    public function setPreviousStage($previousStage)
    {
        $this->previousStage = $previousStage;
    }

    /**
     * @return number
     */
    public function getPreviousStatus()
    {
        return $this->previousStatus;
    }

    /**
     * @param number $previousStatus
     */
    public function setPreviousStatus($previousStatus)
    {
        $this->previousStatus = $previousStatus;
    }

    /**
     * @param string $affiliateToken
     */
    public function setAffiliateToken($affiliateToken)
    {
        $this->affiliateToken = $affiliateToken;
    }

    /**
     * @return array|string
     */
    public function getAffiliateToken()
    {
        return $this->affiliateToken ? json_decode($this->affiliateToken, true) : null;
    }

    /**
     * @param string $error
     */
    public function setError(string $error)
    {
        $this->error = $error;
    }

    /**
     * @return string
     */
    public function getError(): string
    {
        return $this->error ?? '';
    }

    /**
     * Returns the request stage
     * @return string
     */
    public function getHumanReadableStage()
    {
        switch ($this->currentStage) {
            case self::ACTION_SUBSCRIBE:
                return 'SUBSCRIBE';
                break;
            case self::ACTION_UNSUBSCRIBE:
                return 'UNSUBSCRIBE';
                break;
            case self::ACTION_RENEW:
                return 'RENEW';
                break;
            default:
                return 'UNSUBSCRIBE';
                break;
        }
    }

    /**
     * Returns the request status
     * @return string
     */
    public function getHumanReadableStatus()
    {
        switch ($this->status) {
            case self::IS_ACTIVE:
                return 'SUCCESSFUL';
                break;
            case self::IS_INACTIVE:
                return 'SUCCESSFUL';
                break;
            case self::IS_ON_HOLD:
                return 'ON_HOLD';
                break;
            case self::IS_PENDING:
                return 'PENDING';
                break;
            case self::IS_ERROR:
                return 'FAILED';
                break;
            default:
                return 'FAILED';
                break;
        }
    }

    /**
     * @return bool
     */
    public function isUnsubscriptionAllowed()
    {
        return $this->getCurrentStage() != self::ACTION_UNSUBSCRIBE ? true : false;
    }

    /**
     * @return bool
     */
    public function isSubscribedWithError(): bool
    {
        return ($this->getCurrentStage() != self::IS_INACTIVE && $this->hasError()) ? true : false;
    }

    public function needShowCounter()
    {
        return ($this->getCredits() >= 0); // todo: change to write way
    }

    public function isError(): bool
    {
        return $this->error ? true : false;
    }

    /**
     * Check if subscription is allowed
     * @return boolean
     */
    public function isSubscriptionAllowed()
    {
        if (!$this->isUnsubscribed()) {
            return false;
        }
//        if ($this->isGracePeriod()) {
//            return false;
//        }
        if ($this->isError()) {
            return false;
        }

        return true;
    }

    public function isAvailableToday()
    {
        if ($this->getCurrentStage() == self::ACTION_SUBSCRIBE) {
            return true;
        }
        if ($this->isUnsubscribed() && $this->getUpdated()->format('Y-m-d H:i:s') <= date('Y-m-d H:i:s', strtotime("-1 days"))) {
            return true;
        }

        return false;
    }

    /**
     * @param string $uuid
     */
    public function setUuid(string $uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * @return mixed
     */
    public function getLastRenewAlertDate(): ?\DateTime
    {
        return $this->lastRenewAlertDate;
    }

    /**
     * @param mixed $lastRenewAlertDate
     * @return Subscription
     */
    public function setLastRenewAlertDate(\DateTime $lastRenewAlertDate)
    {
        $this->lastRenewAlertDate = $lastRenewAlertDate;
        return $this;
    }

    /**
     * statuses
     */

    /**
     * 4/1
     * @return bool
     */
    public function isNotFullyPaid()
    {
        return $this->currentStage == self::ACTION_SUBSCRIBE && $this->status == self::IS_ON_HOLD && $this->getError() == 'not_fully_paid';
    }

    /**
     * 4/1
     * @return bool
     */
    public function isNotEnoughCredit()
    {
        return $this->currentStage == self::ACTION_SUBSCRIBE && $this->status == self::IS_ON_HOLD && $this->getError() == 'not_enough_credit';
    }

    /**
     * 0/2
     * @return bool
     */
    public function isUnsubscribed()
    {
        return $this->isInActive() && $this->currentStage == self::ACTION_UNSUBSCRIBE;
    }

    /**
     * 1/1
     * @return bool
     */
    public function isSubscribed(): bool
    {
        return ($this->getCurrentStage() != self::IS_INACTIVE && $this->getStatus() == self::IS_ACTIVE);
    }
    /**
     * /statuses/
     */
}

