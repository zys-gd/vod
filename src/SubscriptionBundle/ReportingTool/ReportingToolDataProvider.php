<?php

namespace SubscriptionBundle\ReportingTool;

use IdentificationBundle\Entity\User;

/**
 * Class ReportingToolDataProvider
 */
class ReportingToolDataProvider
{
    const USER_STATS_PATH = '/stats/userstats/';
    const USER_STATS_WITH_CHARGES_PATH = '/stats/userstats_withcharges';

    /**
     * @var ReportingToolRequestSender
     */
    private $sender;

    /**
     * ReportingToolDataProvider constructor
     *
     * @param ReportingToolRequestSender $sender
     */
    public function __construct(ReportingToolRequestSender $sender)
    {
        $this->sender = $sender;
    }

    /**
     * @param User $user
     *
     * @return array
     */
    public function getUserStats(User $user): array
    {
        return $this->sender->sendRequest($user, self::USER_STATS_PATH);
    }

    /**
     * @param User $user
     *
     * @return array
     */
    public function getUsersStatsWithCharges(User $user): array
    {
        return $this->sender->sendRequest($user, self::USER_STATS_WITH_CHARGES_PATH);
    }


}