<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 12.11.18
 * Time: 14:00
 */

namespace SubscriptionBundle\Subscription\Notification\Common;


use IdentificationBundle\Entity\User;
use IdentificationBundle\Repository\IdentificationRequestRepository;

class ProcessIdExtractor
{
    public function extractProcessId(User $User): int
    {
        $identificationRequest = $User->getIdentificationProcessId();

        $processId = $identificationRequest->getProcessId();

        return $processId;
    }
}