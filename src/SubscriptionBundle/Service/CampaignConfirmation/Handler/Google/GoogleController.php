<?php


namespace SubscriptionBundle\Service\CampaignConfirmation\Handler\Google;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class GoogleController extends AbstractController
{
    /**
     * @Route('/google_campaign', name='google_campaign')
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function googlePageAction()
    {
        return $this->render('google_campaign.html.twig');
    }
}