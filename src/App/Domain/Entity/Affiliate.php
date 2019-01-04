<?php

namespace App\Domain\Entity;

use Doctrine\Common\Collections\ArrayCollection;
/**
 * Affiliate
 */
class Affiliate
{
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
     * @var ArrayCollection
     */
    protected $parameters;
    /**
     * @var ArrayCollection
     */
    private $constants;

    /**
     * @var string
     */
    private $subPriceName;


    /**
     * Affiliate constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        $this->uuid = \Ramsey\Uuid\Uuid::uuid4()->toString();
        $this->constants = new ArrayCollection();
        $this->parameters = new ArrayCollection();
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
     * @return string
     */
    public function getName()
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
     * Get id
     *
     * @return int
     */
    public function getPostbackUrl()
    {
        return $this->postbackUrl;
    }

    /**
     * Set name
     *
     * @param string $postbackUrl
     *
     * @return Affiliate
     */
    public function setPostbackUrl($postbackUrl)
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
     * @return bool
     */
    public function getSubPriceName()
    {
        return $this->subPriceName;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }


    public function getParameters()
    {
        return $this->parameters;
    }

    public function getParamsList(){
        $list=[];
        if(isset($this->parameters)&&!empty($this->parameters)){
            foreach ($this->parameters as $parameter){
                $list[$parameter->getOutputName()] = $parameter->getInputName();
            }
        }
        return $list;
    }

    public function getInputParamsList(){
        $paramsList=$this->getParamsList();
        $inputParams=[];
        foreach ($paramsList as $value){
            $inputParams[] = $value;
        }
        return $inputParams;
    }

    public function getConstantsList(){
        $list=[];
        if(isset($this->constants)&&!empty($this->constants)){
            foreach ($this->constants as $parameter){
                $list[$parameter->getName()] = $parameter->getValue();
            }
        }

        return $list;
    }


    /**
     * @param AffiliateConstant $affiliateConstant
     */
    public function addConstants(AffiliateConstant $affiliateConstant){
        $this->constants[] = $affiliateConstant;
        $affiliateConstant->setAffiliate($this);
    }


    /**
     * @param AffiliateConstant $affiliateConstant
     */
    public function addParameters(AffiliateParameter $affiliateParameter){
        $this->parameters[] = $affiliateParameter;
        $affiliateParameter->setAffiliate($this);
    }

    /**
     * @return ArrayCollection
     */
    public function getConstants(){
        return $this->constants;
    }


    /**
     * @param $affiliateConstant
     */
    public function setConstants($affiliateConstant){
        $this->constants = $affiliateConstant;
    }


    /**
     * @param $affiliateParameters
     */
    public function setParameters($affiliateParameters){
        $this->parameters = $affiliateParameters;
    }


    /**
     * @param AffiliateConstant $affiliateConstant
     */
    public function removeConstant(AffiliateConstant $affiliateConstant)
    {
        $this->constants->removeElement($affiliateConstant);
    }

    /**
     * @param $affiliateParameters
     */
    public function removeParameter(AffiliateParameter $affiliateParameters){
        $this->parameters -> removeElement($affiliateParameters);
    }


}

