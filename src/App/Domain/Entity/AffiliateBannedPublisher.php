<?php


namespace App\Domain\Entity;


class AffiliateBannedPublisher
{
    /** @var Affiliate */
    private $affiliate;

    /** @var string */
    private $publisherId;

    /**
     * @return string
     */
    public function getAffiliate(): string
    {
        return $this->affiliate;
    }

    /**
     * @return string
     */
    public function getPublisherId(): string
    {
        return $this->publisherId;
    }

}