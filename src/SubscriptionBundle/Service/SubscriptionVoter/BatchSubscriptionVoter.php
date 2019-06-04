<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 08.05.19
 * Time: 17:34
 */

namespace SubscriptionBundle\Service\SubscriptionVoter;


use IdentificationBundle\Identification\DTO\IdentificationData;
use IdentificationBundle\Identification\DTO\ISPData;
use Symfony\Component\HttpFoundation\Request;

class BatchSubscriptionVoter implements SubscriptionVoterInterface
{
    /**
     * @var SubscriptionVoterInterface[]
     */
    private $voters;


    public function checkIfSubscriptionAllowed(Request $request, IdentificationData $identificationData, ISPData $ISPData): bool
    {
        foreach ($this->voters as $voter) {
            if (!$voter->checkIfSubscriptionAllowed($request, $identificationData, $ISPData)) {
                return false;
            }
        }

        return true;
    }

    public function addVoter(SubscriptionVoterInterface $subscriptionVoter)
    {
        $this->voters[] = $subscriptionVoter;
    }
}