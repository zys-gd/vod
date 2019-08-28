<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 08.05.19
 * Time: 17:33
 */

namespace SubscriptionBundle\Subscription\Subscribe\Voter;


use IdentificationBundle\Identification\DTO\IdentificationData;
use IdentificationBundle\Identification\DTO\ISPData;
use Symfony\Component\HttpFoundation\Request;

interface SubscriptionVoterInterface
{
    public function checkIfSubscriptionAllowed(Request $request, IdentificationData $identificationData, ISPData $ISPData): bool;
}