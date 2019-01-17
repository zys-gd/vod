<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 17.01.19
 * Time: 11:31
 */

namespace SubscriptionBundle\Affiliate\Service;


use Symfony\Component\HttpFoundation\Session\SessionInterface;

class AffiliateVisitSaver
{
    public static function saveCampaignId(string $id, SessionInterface $session): void
    {
        $session->set('campaign_id', $id);
    }

    public static function extractCampaignToken(SessionInterface $session): ?string
    {
        $session->get('campaign_id', null);
    }
}