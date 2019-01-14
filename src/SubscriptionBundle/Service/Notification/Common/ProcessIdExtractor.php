<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 12.11.18
 * Time: 14:00
 */

namespace SubscriptionBundle\Service\Notification\Common;


use IdentificationBundle\Repository\IdentificationRequestRepository;
use IdentificationBundle\Entity\User;

class ProcessIdExtractor
{
    public function extractProcessId(User $User): int
    {
        $identificationRequest = $User->getIdentificationProcessId();

        $processId = $identificationRequest->getProcessId();

        return $processId;
    }
}