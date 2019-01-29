<?php

namespace SubscriptionBundle\BillingFramework\Process;

use Doctrine\ORM\EntityManager;

/**
 * Class RefundProcess
 */
class RefundProcess
{
    const PROCESS_IDENTIFIER = 'refund';

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var array
     */
    private $tableHeaders = [
        'Msisdn',
        'Status',
        'Error',
        'Billing charge process',
        'Billing refund process',
        'Refund credits'
    ];

    /**
     * RefundService constructor
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param array|string $msisdn
     */
    public function refund($msisdn)
    {
    }
}