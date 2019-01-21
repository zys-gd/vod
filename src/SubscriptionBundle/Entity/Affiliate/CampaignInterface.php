<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 17.01.19
 * Time: 14:23
 */

namespace SubscriptionBundle\Entity\Affiliate;


use App\Domain\Entity\Affiliate;
use App\Domain\Entity\Carrier;
use Doctrine\Common\Collections\ArrayCollection;

interface CampaignInterface
{

    public function getCampaignToken(): string ;

    public function getSub(): string;



        /**
     * Get operator
     *
     * @return Carrier[] | ArrayCollection
     */
    public function getCarriers(): ArrayCollection;

    public function getAffiliate(): Affiliate;
}