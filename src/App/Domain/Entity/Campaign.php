<?php

namespace App\Domain\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Playwing\DiffToolBundle\Entity\Interfaces\HasUuid;
use SubscriptionBundle\Entity\Affiliate\AffiliateInterface;
use SubscriptionBundle\Entity\Affiliate\CampaignInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints\Date;

/**
 * Campaign
 */
class Campaign implements CampaignInterface, HasUuid
{
    /**
     * Path for saving campaign banner
     */
    const RESOURCE_IMAGE = 'uploads/images/campaign_banner';

    /**
     * @var string
     */
    private $uuid;

    /**
     * @var Affiliate
     */
    private $affiliate;

    /**
     * @var MainCategory
     */
    private $mainCategory;

    /**
     * @var string
     */
    private $campaignToken;

    /**
     * @var Collection
     */
    private $carriers;

    /**
     * @var string
     */
    private $bgColor = '#000000';

    /**
     * @var string
     */
    private $textColor = '#000000';

    /**
     * @var string
     */
    private $imageName;

    /**
     * @var File
     */
    private $imageFile;

    /**
     * @var Boolean
     */
    private $isPause = false;

    /**
     * @var string
     */
    private $testUrl;

    /**
     * @var integer
     */
    private $counter = 0;

    /**
     * @var Date
     */
    private $flushDate;

    /**
     * @var string
     */
    private $ppd;

    /**
     * @var string
     */
    private $sub;

    /**
     * @var string
     */
    private $click;

    /**
     * Campaign constructor
     *
     * @param string $uuid
     */
    public function __construct(string $uuid)
    {
        $this->uuid = $uuid;
        $this->campaignToken = uniqid();
        $this->carriers = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        if (!empty($this->campaignToken) && !empty($this->affiliate) && !empty($this->carriers)) {
            return $this->campaignToken
                . ' - '
                . $this->affiliate->getName()
                . ' - '
                . implode(', ', $this->carriers->getValues());
        }

        return '';
    }

    /**
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * @return string
     */
    public function getImageName()
    {
        return $this->imageName;
    }

    /**
     * Get thumbnail path
     *
     * @return string
     */
    public function getImagePath()
    {
        return static::RESOURCE_IMAGE .'/' . $this->getImageName();
    }

    /**
     * @param $imageName
     *
     * @return Campaign
     */
    public function setImageName($imageName)
    {
        $this->imageName = $imageName;

        return $this;
    }

    /**
     * Get thumbnail file
     *
     * @return File
     */
    public function getImageFile()
    {
        return $this->imageFile;
    }

    /**
     * Set thumbnail file
     *
     * @param File $file
     */
    public function setImageFile(File $file)
    {
        $this->imageFile = $file;
    }

    /**
     * @param string $token
     */
    public function setCampaignToken($token) {
        $this->campaignToken = $token;
    }

    /**
     * @return string
     */
    public function getCampaignToken(): string
    {
        return $this->campaignToken;
    }

    /**
     * @param string $testUrl
     */
    public function setTestUrl($testUrl) {
        $this->testUrl = $testUrl;
    }

    /**
     * @return string
     */
    public function getTestUrl() {
        return $this->testUrl;
    }

    /**
     * Set affiliate
     *
     * @param Affiliate $affiliate
     *
     * @return Campaign
     */
    public function setAffiliate(Affiliate $affiliate): Campaign
    {
        $this->affiliate = $affiliate;

        return $this;
    }

    /**
     * Get affiliate
     *
     * @return AffiliateInterface
     */
    public function getAffiliate(): ?AffiliateInterface
    {
        return $this->affiliate;
    }

    /**
     * @return MainCategory
     */
    public function getMainCategory(): ?MainCategory
    {
        return $this->mainCategory;
    }

    /**
     * @param MainCategory $mainCategory
     *
     * @return Campaign
     */
    public function setMainCategory(MainCategory $mainCategory): Campaign
    {
        $this->mainCategory = $mainCategory;

        return $this;
    }

    /**
     * Get operator
     *
     * @return Collection
     */
    public function getCarriers(): Collection
    {
        return $this->carriers;
    }

    /**
     * @param Carrier $carrier
     *
     * @return $this
     */
    public function addCarrier(Carrier $carrier)
    {
        $this->carriers->add($carrier);

        return $this;
    }

    /**
     * @param Carrier $carrier
     *
     * @return $this
     */
    public function removeCarrier(Carrier $carrier)
    {
        $this->carriers->remove($carrier);

        return $this;
    }

    /**
     * Set bgColor
     *
     * @param string $bgColor
     *
     * @return Campaign
     */
    public function setBgColor($bgColor)
    {
        $this->bgColor = $bgColor;

        return $this;
    }

    /**
     * Get bgColor
     *
     * @return string
     */
    public function getBgColor()
    {
        return $this->bgColor;
    }

    /**
     * @param string $textColor
     *
     * @return $this
     */
    public function setTextColor($textColor)
    {
        $this->textColor = $textColor;

        return $this;
    }

    /**
     * @return string
     */
    public function getTextColor()
    {
        return $this->textColor;
    }

    /**
     * Set isPause
     *
     * @param boolean $isPause
     *
     * @return Campaign
     */
    public function setIsPause ($isPause)
    {
        $this->isPause = $isPause;

        return $this;
    }

    /**
     * Get isPause
     *
     * @return boolean
     */
    public function getIsPause (): bool
    {
        return $this->isPause;
    }

    /**
     * @return string
     * This is how we getLinkToWifiFlowPage the token to identify traffic intended for aff campaigns.
     * /?cmpId=token
     * We don't need to store the computed token anywhere inside the DB to identify a specific campaign,
     * because we get its ID it by decoding the token. This applies only when there is no enforced ID from affiliate.
     * This method is called only by configureListFields() inside App\Admin\CampaignAdmin
     *
     *
     * The parameter names are hardcoded, and should be read from app/config/parameters.yml
     */
    public function getLandingUrl()
    {
        return "http://".$_SERVER['HTTP_HOST'].$this->getTestUrl();
    }

    /**
     * Set counter
     *
     * @param integer $counter
     *
     * @return Campaign
     */
    public function setCounter($counter)
    {
        $this->counter = $counter;

        return $this;
    }

    /**
     * Get counter
     *
     * @return integer
     */
    public function getCounter()
    {
        return $this->counter;
    }

    /**
     * Set flushDate
     *
     * @param \DateTime $flushDate
     *
     * @return Campaign
     */
    public function setFlushDate($flushDate)
    {
        $this->flushDate = $flushDate;

        return $this;
    }

    /**
     * @return \DateTime
     *
     * @throws \Exception
     */
    public function getFlushDate()
    {
        if (is_null($this->flushDate)) {
            $this->flushDate = new \DateTime('now');
        }

        return $this->flushDate;
    }

    /**
     * @return string
     */
    public function getPpd()
    {
        return $this->ppd;
    }

    /**
     * @param $ppd
     *
     * @return Campaign
     */
    public function setPpd($ppd)
    {
        $this->ppd = $ppd;

        return $this;
    }

    public function getSub(): ?string
    {
        return $this->sub;
    }

    /**
     * @param $sub
     *
     * @return Campaign
     */
    public function setSub($sub)
    {
        $this->sub = $sub;

        return $this;
    }

    /**
     * @return string
     */
    public function getClick()
    {
        return $this->click;
    }

    /**
     * @param  $click
     *
     * @return Campaign
     */
    public function setClick($click)
    {
        $this->click = $click;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getPausedCampaigns()
    {
        return $this->carriers->filter(function (Carrier $carrier){
            return $carrier->getIsCampaignsOnPause();
        });
    }

    /**
     * @param string $uuid
     */
    public function setUuid(string $uuid)
    {
        $this->uuid = $uuid;
    }
}