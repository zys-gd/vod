<?php


namespace SubscriptionBundle\CampaignConfirmation\Handler;


use Symfony\Component\HttpFoundation\Request;

interface CustomPage
{
    public function proceedCustomPage(Request $request);
}