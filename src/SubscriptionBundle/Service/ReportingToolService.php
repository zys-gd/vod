<?php

namespace SubscriptionBundle\Service;

use IdentificationBundle\Entity\User;

/**
 * Class ReportingToolService
 */
class ReportingToolService
{
    const USER_STATS_PATH = '/stats/userstats/';
    const USER_STATS_WITH_CHARGES_PATH = '/stats/userstats_withcharges';

    /**
     * @var string
     */
    private $reportApiHost;

    /**
     * ReportingToolService constructor
     *
     * @param string $reportApiHost
     */
    public function __construct(string $reportApiHost)
    {
        $this->reportApiHost = $reportApiHost;
    }

    /**
     * @param User $user
     *
     * @return array
     */
    public function getUserStats(User $user): array
    {
        return $this->sendRequest($user, self::USER_STATS_PATH);
    }

    /**
     * @param User $user
     *
     * @return array
     */
    public function getUsersStatsWithCharges(User $user): array
    {
        return $this->sendRequest($user, self::USER_STATS_WITH_CHARGES_PATH);
    }

    /**
     * @param User $user
     * @param string $statsPath
     *
     * @return array
     */
    private function sendRequest(User $user, string $statsPath): array
    {
        $url = $this->reportApiHost. $statsPath . $user->getBillingCarrierId();
        $key = sha1(date("Y") . $user->getIdentifier() . date("d"));

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url,
            CURLOPT_POSTFIELDS => ['msisdn' => $user->getIdentifier()]
        ]);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'X-REVERSE-KEY: ' . $key,
        ]);

        $response = json_decode(curl_exec($curl), true);

        curl_close($curl);

        return $response;
    }
}