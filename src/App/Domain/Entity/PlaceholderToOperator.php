<?php
/**
 * Created by PhpStorm.
 * User: Maxim Nevstruev
 * Date: 14.02.2017
 * Time: 11:43
 */

namespace App\Domain\Entity;


use App\Domain\Entity\Interfaces\HasUuid;

class PlaceholderToOperator implements HasUuid
{

    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $carrier_id;

    /**
     * @var string
     */
    private $placeholder_id;

    /**
     * @var string
     */
    private $specificValue;

    /**
     * @var \App\Domain\Entity\Languages
     */
    private $language;

    /**
     * @var integer
     */
    private $subscription_pack_id;

    /** @var string */
    private $uuid = null;

    /**
     * PlaceholderToOperator constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        $this->uuid = \Ramsey\Uuid\Uuid::uuid4()->toString();
    }

    /**new Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle()
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
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
     * Set carrierId
     *
     * @param integer $carrierId
     *
     * @return PlaceholderToOperator
     */
    public function setCarrierId($carrierId)
    {
        $this->carrier_id = $carrierId;

        return $this;
    }

    /**
     * Get carrierId
     *
     * @return integer
     */
    public function getCarrierId()
    {
        return $this->carrier_id;
    }

    /**
     * Set placeholderId
     *
     * @param string $placeholderId
     *
     * @return PlaceholderToOperator
     */
    public function setPlaceholderId($placeholderId)
    {
        $this->placeholder_id = $placeholderId;

        return $this;
    }

    /**
     * Get placeholderId
     *
     * @return string
     */
    public function getPlaceholderId()
    {
        return $this->placeholder_id;
    }

    /**
     * Set specificValue
     *
     * @param string $specificValue
     *
     * @return PlaceholderToOperator
     */
    public function setSpecificValue($specificValue)
    {
        $this->specificValue = $specificValue;

        return $this;
    }

    /**
     * Get specificValue
     *
     * @return string
     */
    public function getSpecificValue()
    {
        return $this->specificValue;
    }

    /**
     * Set language
     *
     * @param \App\Domain\Entity\Languages $language
     *
     * @return PlaceholderToOperator
     */
    public function setLanguage(\App\Domain\Entity\Languages $language = null)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Get language
     *
     * @return \App\Domain\Entity\Languages
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @return int|null
     */
    public function getSubscriptionPackId()
    {
        return $this->subscription_pack_id;
    }

    /**
     * @param int $subscription_pack_id
     */
    public function setSubscriptionPackId(int $subscription_pack_id)
    {
        $this->subscription_pack_id = $subscription_pack_id;
    }
}
