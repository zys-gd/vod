<?php

namespace App\Domain\Entity;
use App\Domain\Entity\Interfaces\HasUuid;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Event\LifecycleEventArgs;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\DateTime;
use AppBundle\Validator\Constraints as Constraints;

/**
 * CampaignConstraints
 * @Constraints\ContainsConstraints
 */
class CampaignConstraints implements HasUuid
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $numberSubscription;

    /**
     * @var string
     */
    private $redirectUrl;

    /**
     * @var Date
     */
    private $startAt;

    /**
     * @var Date
     */
    private $endAt;

    /**
     * @var Boolean
     */
    private $isPause = false;

    /**
     * @var Campaign
     */
    private $campaign;

    /** @var string */
    private $uuid = null;

    /**
     * CampaignConstraints constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        $this->uuid = \Ramsey\Uuid\Uuid::uuid4()->toString();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
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
    public function setUuid(string $uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     * Set numberSubscription
     *
     * @param integer $numberSubscription
     *
     * @return CampaignConstraints
     */
    public function setNumberSubscription($numberSubscription)
    {
        $this->numberSubscription = $numberSubscription;

        return $this;
    }

    /**
     * Get numberSubscription
     *
     * @return integer
     */
    public function getNumberSubscription()
    {
        return $this->numberSubscription;
    }

    /**
     * Set redirectUrl
     *
     * @param string $redirectUrl
     *
     * @return CampaignConstraints
     */
    public function setRedirectUrl($redirectUrl)
    {
        $this->redirectUrl = $redirectUrl;

        return $this;
    }

    /**
     * Get redirectUrl
     *
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->redirectUrl;
    }

    /**
     * Set startAt
     *
     * @param \DateTime $startAt
     *
     * @return CampaignConstraints
     */
    public function setStartAt($startAt)
    {
        $this->startAt = $startAt;

        return $this;
    }

    /**
     * Get startAt
     *
     * @return \DateTime
     */
    public function getStartAt()
    {
        return $this->startAt;
    }

    /**
     * Set endAt
     *
     * @param \DateTime $endAt
     *
     * @return CampaignConstraints
     */
    public function setEndAt($endAt)
    {
        $this->endAt = $endAt;

        return $this;
    }

    /**
     * Get endAt
     *
     * @return \DateTime
     */
    public function getEndAt()
    {
        return $this->endAt;
    }

    /**
     * Set isPause
     *
     * @param boolean $isPause
     *
     * @return CampaignConstraints
     */
    public function setIsPause($isPause)
    {
        $this->isPause = $isPause;

        return $this;
    }

    /**
     * Get isPause
     *
     * @return boolean
     */
    public function getIsPause()
    {
        return $this->isPause;
    }

    /**
     * Set campaign
     *
     * @param Campaign $campaign
     *
     * @return CampaignConstraints
     */
    public function setCampaign($campaign)
    {
        $this->campaign = $campaign;

        return $this;
    }

    /**
     * Get campaign
     *
     * @return Campaign
     */
    public function getCampaign()
    {
        return $this->campaign;
    }
}
