<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 12.11.18
 * Time: 14:00
 */

namespace SubscriptionBundle\Service\Notification\Common;


use IdentificationBundle\Repository\IdentificationRequestRepository;
use UserBundle\Entity\BillableUser;

class ProcessIdExtractor
{
    /**
     * @var IdentificationRequestRepository
     */
    private $identificationRequestRepository;


    /**
     * ProcessIdExtractor constructor.
     * @param IdentificationRequestRepository $identificationRequestRepository
     */
    public function __construct(IdentificationRequestRepository $identificationRequestRepository)
    {
        $this->identificationRequestRepository = $identificationRequestRepository;
    }

    public function extractProcessId(BillableUser $billableUser): int
    {
        $identificationRequest = $this
            ->identificationRequestRepository
            ->findOneBy(
                ['userIdentifier' => $billableUser->getIdentifier()],
                ['id' => 'DESC']
            );

        $processId = $identificationRequest->getProcessId();

        return $processId;
    }
}