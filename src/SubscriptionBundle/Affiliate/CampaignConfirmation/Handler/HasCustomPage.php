<?php


namespace SubscriptionBundle\Affiliate\CampaignConfirmation\Handler;


use Symfony\Component\HttpFoundation\Request;

interface HasCustomPage
{
    public function proceedCustomPage(Request $request);
}