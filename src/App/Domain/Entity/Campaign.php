<?php

namespace App\Domain\Entity;

use AppBundle\Validator\Constraints\ContainsConstraints;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Campaign
 */
class Campaign
{

    const RESOURCE_IMAGE = 'images/campaign_banner';
    /**
     * @var string
     */
    private $uuid;

    /**
     * @var Affiliate
     */
    private $affiliate;

    private $campaignToken;

    /**
     * @var Carrier[] | ArrayCollection
     */
    private $carriers;

    /**
     * @var Game
     */
    private $game;

    /**
     * @var string
     */
    private $bgColor = '#000000';

    private $textColor = '#000000';

    /**
     * @var ArrayCollection
     */
    private $campaignPricingDetails;

    /**
     * @var string
     */
    private $image;

    /**
     * @var CategoryCampaignOverride
     */
    private $categoryOverride;

    /**
     * @var File
     */
    private $imageFile;

    /**
     * @var Boolean
     */
    private $isPause = false;

    /**
     * @var ArrayCollection
     * @Assert\All(
     *     @ContainsConstraints()
     * )
     */
    private $campaignConstraints;

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
     * Campaign constructor.
     * @param string $uuid
     */
    public function __construct(string $uuid)
    {
        $this->uuid = $uuid;
        $this->campaignPricingDetails = new ArrayCollection();
        $this->campaignConstraints = new ArrayCollection();
        $this->carriers = new ArrayCollection();
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
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Get thumbnail path
     *
     * @return string
     */
    public function getImagePath()
    {
        return static::RESOURCE_IMAGE .'/' . $this->getImage();
    }

    /**
     * @param $image
     * @return Campaign
     */
    public function setImage($image)
    {
        $this->image = $image;
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
     * @return Game
     */
    public function setImageFile(File $file)
    {
        $this->imageFile = $file;
    }

    public function setCampaignToken($token) {
        $this->campaignToken = $token;
    }

    public function getCampaignToken() {
        return $this->campaignToken;
    }

    public function setTestUrl($testUrl) {
        $this->testUrl = $testUrl;
    }

    public function getTestUrl() {
        return $this->testUrl;
    }


    /**
     * @param CampaignPricing $campaignPricing
     */
    public function addCampaignPricingDetail(CampaignPricing $campaignPricing)
    {
            $this->campaignPricingDetails[] = $campaignPricing;
            $campaignPricing->setCampaign($this);
    }

    /**
     * @param $campaignPricingDetails
     */
    public function setCampaignPricingDetails($campaignPricingDetails)
    {
        $this->campaignPricingDetails = $campaignPricingDetails;
    }

    /**
     * @return ArrayCollection
     */
    public function getCampaignPricingDetails()
    {
        return $this->campaignPricingDetails->getValues();
    }

    /**
     * @param CampaignPricing $campaignPricing
     */
    public function removeCampaignPricingDetail(CampaignPricing $campaignPricing)
    {
         $this->campaignPricingDetails->removeElement($campaignPricing);
    }

    /**
     * Set affiliate
     *
     * @param integer $affiliate
     *
     * @return Campaign
     */
    public function setAffiliate($affiliate)
    {
        $this->affiliate = $affiliate;

        return $this;
    }

    /**
     * Get affiliate
     *
     * @return Affiliate
     */
    public function getAffiliate()
    {
        return $this->affiliate;
    }

    /**
     * Get operator
     *
     * @return Carrier[] | ArrayCollection
     */
    public function getCarriers()
    {
        return $this->carriers;
    }

    /**
     * @param Carrier $carrier
     * @return $this
     */
    public function addCarrier(Carrier $carrier)
    {
        $this->carriers->add($carrier);

        return $this;
    }

    /**
     * @param Carrier $carrier
     * @return $this
     */
    public function removeCarrier(Carrier $carrier)
    {
        $this->carriers->remove($carrier);

        return $this;
    }

    /**
     * Set game
     *
     * @param integer $game
     *
     * @return Campaign
     */
    public function setGame($game)
    {
        $this->game = $game;

        return $this;
    }

    /**
     * Get game
     *
     * @return Game
     */
    public function getGame()
    {
        return $this->game;
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

    public function setTextColor($textColor)
    {
        $this->textColor = $textColor;

        return $this;
    }

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
     * @return CategoryCampaignOverride
     */
    public function getCategoryOverride()
    {
        return $this->categoryOverride;
    }

    /**
     * @param CategoryCampaignOverride $categoryOverride
     */
    public function setCategoryOverride(CategoryCampaignOverride $categoryOverride)
    {
        $this->categoryOverride = $categoryOverride;
    }

    /**
     * @param CampaignConstraints $campaignConstraint
     */
    public function addCampaignConstraint(CampaignConstraints $campaignConstraint)
    {
        $this->campaignConstraints[] = $campaignConstraint;
        $campaignConstraint->setCampaign($this);
    }

    /**
     * @param $campaignConstraints
     */
    public function setCampaignConstraints($campaignConstraints)
    {
        $this->campaignConstraints = $campaignConstraints;
    }

    /**
     * @return ArrayCollection
     */
    public function getCampaignConstraints()
    {
        return $this->campaignConstraints;
    }

    /**
     * @param CampaignConstraints $campaignConstraints
     */
    public function removeCampaignConstraint(CampaignConstraints $campaignConstraints)
    {
        $this->campaignConstraints->removeElement($campaignConstraints);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return 'Campaign #'.$this->getUuid();
    }

    /**
     * Required to load the default value on campaigns, that was previously created
     *
     * @param LifecycleEventArgs $event
     */
    public function onPostLoad(LifecycleEventArgs $event)
    {
        if ($this->categoryOverride instanceof CategoryCampaignOverride) {
            return;
        }

        $this->setCategoryOverride(
            $event->getEntityManager()->getReference(CategoryCampaignOverride::class, 1)
        );
    }


    /**
     * @return string
     * This is how we generate the token to identify traffic intended for aff campaigns.
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
     * @return CampaignConstraints
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
     * @return CampaignConstraints
     */
    public function setFlushDate($flushDate)
    {
        $this->flushDate = $flushDate;

        return $this;
    }

    /**
     * Get flushDate
     *
     * @return \DateTime
     */
    public function getFlushDate()
    {
        if(is_null($this->flushDate)){

            $this->flushDate = new \DateTime('now');

        }
        return $this->flushDate;
    }
}

