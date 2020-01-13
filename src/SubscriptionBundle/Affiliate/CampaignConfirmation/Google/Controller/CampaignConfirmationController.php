<?php


namespace SubscriptionBundle\Affiliate\CampaignConfirmation\Google\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CampaignConfirmationController extends AbstractController
{
    /**
     * @Route("/google_campaign", name="google_campaign")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function googlePageAction()
    {
        return $this->render('@Subscription/CampaignConfirmation/Google/google_campaign.html.twig');
    }
}