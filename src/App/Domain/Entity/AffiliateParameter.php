<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 4/4/2018
 * Time: 5:09 PM
 */

namespace App\Domain\Entity;


use App\Domain\Entity\Interfaces\HasUuid;

class AffiliateParameter implements HasUuid
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $input_name;

    /**
     * @var string
     */
    private $output_name;

    /**
     * @var Affiliate
     */
    private $affiliate;

    /**
     * @var string
     */
    private $uuid = null;

    /**
     * AffiliateParameter constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        $this->uuid = \Ramsey\Uuid\Uuid::uuid4()->toString();
    }

    /**
     * Get id
     *
     * @return int
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
     * Set input_name
     *
     * @param string $name
     *
     * @return AffiliateParameter
     */
    public function setInputName($name)
    {
        $this->input_name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getInputName()
    {
        return $this->input_name;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return AffiliateParameter
     */
    public function setOutputName($name)
    {
        $this->output_name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getOutputName()
    {
        return $this->output_name;
    }
    /**
     * Set affiliate
     *
     * @param integer $affiliate
     *
     * @return AffiliateParameter
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

}