<?php

namespace App\Domain\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use IdentificationBundle\Entity\CarrierInterface;
use Playwing\DiffToolBundle\Entity\Interfaces\HasUuid;
use SubscriptionBundle\Entity\Affiliate\AffiliateInterface;
use SubscriptionBundle\Entity\Affiliate\ConstraintByAffiliate;

/**
 * Affiliate
 */
class Affiliate implements HasUuid, AffiliateInterface
{
    /**
     * Affiliate types
     */
    const CPC_TYPE = 1;
    const CPA_TYPE = 2;

    /**
     * @var string
     */
    private $uuid;

    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $type;

    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $country;

    /**
     * @var string
     */
    private $commercialContact;

    /**
     * @var string
     */
    private $technicalContact;

    /**
     * @var string
     */
    private $skypeId;

    /**
     * @var bool
     */
    private $enabled;

    /**
     * @var string
     */
    private $postbackUrl;

    /**
     * @var Collection
     */
    protected $parameters;

    /**
     * @var Collection
     */
    private $constants;

    /**
     * @var string
     */
    private $subPriceName;

    /**
     * @var Collection
     */
    private $campaigns;

    /**
     * @var Collection
     */
    private $carriers;

    /**
     * @var Collection
     */
    private $constraints;

    /**
     * @var bool
     */
    private $isLpOff = false;

    /**
     * Affiliate constructor.
     * @param string $uuid
     */
    public function __construct(string $uuid)
    {
        $this->uuid       = $uuid;
        $this->constants  = new ArrayCollection();
        $this->parameters = new ArrayCollection();
        $this->campaigns = new ArrayCollection();
        $this->constraints = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name ?? '';
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
     * Set name
     *
     * @param string $name
     *
     * @return Affiliate
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set type
     *
     * @param integer $type
     *
     * @return Affiliate
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set url
     *
     * @param string $url
     *
     * @return Affiliate
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
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
     * Set country
     *
     * @param string $country
     *
     * @return Affiliate
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
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
     * @return string|null
     */
    public function getPostbackUrl(): ?string
    {
        return $this->postbackUrl;
    }

    /**
     * Set name
     *
     * @param string|null $postbackUrl
     *
     * @return Affiliate
     */
    public function setPostbackUrl($postbackUrl): ?string
    {
        $this->postbackUrl = $postbackUrl;

        return $this;
    }

    /**
     * Set commercialContact
     *
     * @param string $commercialContact
     *
     * @return Affiliate
     */
    public function setCommercialContact($commercialContact)
    {
        $this->commercialContact = $commercialContact;

        return $this;
    }

    /**
     * Get commercialContact
     *
     * @return string
     */
    public function getCommercialContact()
    {
        return $this->commercialContact;
    }

    /**
     * Set technicalContact
     *
     * @param string $technicalContact
     *
     * @return Affiliate
     */
    public function setTechnicalContact($technicalContact)
    {
        $this->technicalContact = $technicalContact;

        return $this;
    }

    /**
     * Get technicalContact
     *
     * @return string
     */
    public function getTechnicalContact()
    {
        return $this->technicalContact;
    }

    /**
     * Set skypeId
     *
     * @param string $skypeId
     *
     * @return Affiliate
     */
    public function setSkypeId($skypeId)
    {
        $this->skypeId = $skypeId;

        return $this;
    }

    /**
     * Get skypeId
     *
     * @return string
     */
    public function getSkypeId()
    {
        return $this->skypeId;
    }

    /**
     * Set enabled
     *
     * @param bool $enabled
     *
     * @return Affiliate
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return bool
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set subPriceName
     *
     * @param bool $subPriceName
     *
     * @return Affiliate
     */
    public function setSubPriceName($subPriceName)
    {
        $this->subPriceName = $subPriceName;

        return $this;
    }

    /**
     * Get subPriceName
     *
     * @return string
     */
    public function getSubPriceName(): ?string
    {
        return $this->subPriceName;
    }

    /**
     * @return array
     */
    public function getParamsList(): array
    {
        $list = [];

        if (isset($this->parameters) && !empty($this->parameters)) {
            foreach ($this->parameters as $parameter) {
                $list[$parameter->getOutputName()] = $parameter->getInputName();
            }
        }

        return $list;
    }

    /**
     * @return array
     */
    public function getInputParamsList(): array
    {
        $paramsList  = $this->getParamsList();
        $inputParams = [];

        foreach ($paramsList as $value) {
            $inputParams[] = $value;
        }

        return $inputParams;
    }

    /**
     * @return array
     */
    public function getConstantsList(): array
    {
        $list = [];

        if (isset($this->constants) && !empty($this->constants)) {
            foreach ($this->constants as $parameter) {
                $list[$parameter->getName()] = $parameter->getValue();
            }
        }

        return $list;
    }

    /**
     * @param Collection $affiliateConstants
     *
     * @return Affiliate
     */
    public function setConstants(Collection $affiliateConstants): self
    {
        /** @var AffiliateConstant $affiliateConstant */
        foreach ($affiliateConstants->getIterator() as $affiliateConstant) {
            $this->addConstant($affiliateConstant);
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getConstants()
    {
        return $this->constants;
    }

    /**
     * @param AffiliateConstant $affiliateConstant
     *
     * @return Affiliate
     */
    public function addConstant(AffiliateConstant $affiliateConstant): self
    {
        if (!$this->constants->contains($affiliateConstant)) {
            $affiliateConstant->setAffiliate($this);
            $this->constants->add($affiliateConstant);
        }

        return $this;
    }

    /**
     * @param AffiliateConstant $affiliateConstant
     */
    public function removeConstant(AffiliateConstant $affiliateConstant)
    {
        if ($this->constants->contains($affiliateConstant)) {
            $this->constants->removeElement($affiliateConstant);

            if ($affiliateConstant->getAffiliate() === $this) {
                $affiliateConstant->setAffiliate($this);
            }
        }
    }

    /**
     * @param Collection $affiliateParameters
     *
     * @return Affiliate
     */
    public function setParameters(Collection $affiliateParameters): self
    {
        /** @var AffiliateParameter $affiliateParameter */
        foreach ($affiliateParameters->getIterator() as $affiliateParameter) {
            $this->addParameter($affiliateParameter);
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getParameters(): Collection
    {
        return $this->parameters;
    }

    /**
     * @param AffiliateParameter $affiliateParameter
     *
     * @return Affiliate
     */
    public function addParameter(AffiliateParameter $affiliateParameter): self
    {
        if (!$this->parameters->contains($affiliateParameter)) {
            $affiliateParameter->setAffiliate($this);
            $this->parameters->add($affiliateParameter);
        }

        return $this;
    }

    /**
     * @param AffiliateParameter $affiliateParameter
     *
     * @return Affiliate
     */
    public function removeParameter(AffiliateParameter $affiliateParameter): self
    {
        if ($this->parameters->contains($affiliateParameter)) {
            $this->parameters->removeElement($affiliateParameter);

            if ($affiliateParameter->getAffiliate() === $this) {
                $affiliateParameter->setAffiliate(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getCampaigns(): Collection
    {
        return $this->campaigns;
    }

    /**
     * @param Collection $campaigns
     *
     * @return Affiliate
     */
    public function setCampaigns(Collection $campaigns): self
    {
        /** @var Campaign $campaign */
        foreach ($campaigns->getIterator() as $campaign) {
            $this->addCampaign($campaign);
        }

        return $this;
    }

    /**
     * @param Campaign $campaign
     *
     * @return Affiliate
     */
    public function addCampaign(Campaign $campaign): self
    {
        if (!$this->campaigns->contains($campaign)) {
            $campaign->setAffiliate($this);
            $this->campaigns->add($campaign);
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getConstraints(): Collection
    {
        return $this->constraints;
    }

    /**
     * @param Collection $constraints
     *
     * @return Affiliate
     */
    public function setConstraints(Collection $constraints):self
    {
        $this->constraints = $constraints;

        return $this;
    }

    /**
     * @param string $capType
     * @param int    $billingCarrierId
     *
     * @return ConstraintByAffiliate|null
     */
    public function getConstraint(string $capType, int $billingCarrierId): ?ConstraintByAffiliate
    {
        $filteredByType = $this->constraints->filter(function (ConstraintByAffiliate $constraint) use ($capType, $billingCarrierId) {
            return $constraint->getCapType() === $capType && $constraint->getCarrier()->getBillingCarrierId() === $billingCarrierId;
        });

        return $filteredByType->isEmpty() ? null : $filteredByType->first();
    }

    /**
     * @return bool
     */
    public function isLpOff(): bool
    {
        return $this->isLpOff;
    }

    /**
     * @param bool $isLpOff
     */
    public function setIsLpOff(bool $isLpOff): void
    {
        $this->isLpOff = $isLpOff;
    }

    /**
     * @return Collection
     */
    public function getCarriers(): Collection
    {
        return $this->carriers;
    }

    /**
     * @param Collection $carriers
     */
    public function setCarriers(Collection $carriers): void
    {
        $this->carriers = $carriers;
    }

    public function hasCarrier(Carrier $carrier): bool
    {
        return $this->carriers->contains($carrier);
    }
}