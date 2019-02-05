<?php

namespace SubscriptionBundle\Repository\Affiliate;

use Doctrine\ORM\EntityRepository;
use PDO;
use SubscriptionBundle\Entity\Subscription;

class AffiliateLogRepository extends EntityRepository
{
    /**
     * @param string $campaignToken
     * @param \DateTime $started
     * @param \DateTime $expired
     * @param int $limit
     *
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function findByCampaignAndDateRange(
        string $campaignToken,
        \DateTime $started,
        \DateTime $expired,
        int $limit
    ): array
    {
        $start = $started->format('Y-m-d H:i:s');
        $end = $expired->format('Y-m-d H:i:s');

        $q = "SELECT al.user_msisdn FROM affiliate_log AS al
                INNER JOIN `user` AS u ON u.identifier = al.user_msisdn
                INNER JOIN subscriptions AS sub ON u.uuid = sub.user_id
              WHERE al.added_at BETWEEN '" . $start . "' AND '" . $end . "'
               AND sub.current_stage = '" . Subscription::ACTION_SUBSCRIBE . "'
               AND al.campaign_token= '" . $campaignToken . "'
              LIMIT " . $limit;

        $statement = $this->getEntityManager()->getConnection()->prepare($q);
        $statement->execute();

        $result = $statement->fetchAll(PDO::FETCH_COLUMN);

        return is_array($result) ? $result : [];
    }

    /**
     * @param string $affiliateId
     * @param string $carrierId
     * @param \DateTime $started
     * @param \DateTime $expired
     * @param int $limit
     *
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function findByAffiliateCarrierAndDateRange(
        string $affiliateId,
        string $carrierId,
        \DateTime $started,
        \DateTime $expired,
        int $limit
    ): array
    {
        $start = $started->format('Y-m-d H:i:s');
        $end = $expired->format('Y-m-d H:i:s');

        $q = "SELECT al.user_msisdn FROM affiliates AS aff
                INNER JOIN campaigns AS cmp ON aff.uuid = cmp.affiliate_id 
                INNER JOIN affiliate_log AS al ON al.campaign_token = cmp.campaign_token 
                INNER JOIN `user` AS u ON u.identifier  = al.user_msisdn
                INNER JOIN subscriptions AS sub ON u.uuid = sub.user_id
                INNER JOIN campaign_carrier AS cc on cmp.uuid = cc.campaign_id
              WHERE aff.uuid = '" . $affiliateId . "'
                AND cc.carrier_id = '" . $carrierId . "'
                AND al.added_at BETWEEN '" . $start . "' AND '" . $end . "'
                AND sub.current_stage = '" . Subscription::ACTION_SUBSCRIBE . "'
              LIMIT " . $limit;

        $statement = $this->getEntityManager()->getConnection()->prepare($q);
        $statement->execute();

        $result = $statement->fetchAll(PDO::FETCH_COLUMN);

        return is_array($result) ? $result : [];
    }
}