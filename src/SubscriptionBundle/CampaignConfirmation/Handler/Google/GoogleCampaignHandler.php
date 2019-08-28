<?php


namespace SubscriptionBundle\CampaignConfirmation\Handler\Google;


use SubscriptionBundle\CampaignConfirmation\Handler\CampaignConfirmationInterface;
use SubscriptionBundle\CampaignConfirmation\Handler\CustomPage;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

class GoogleCampaignHandler implements CampaignConfirmationInterface, CustomPage
{
    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @param string $affiliateUuid
     *
     * @return bool
     */
    public function canHandle(string $affiliateUuid): bool
    {
        return $affiliateUuid == "514fe478-ebd4-11e8-95c4-02bb250f0f22";
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function proceedCustomPage(Request $request)
    {
        if(is_null($request->getSession()->get('GoogleCampaignHandler'))) {
            $request->getSession()->set('GoogleCampaignHandler', true);
            return new RedirectResponse($this->router->generate('google_campaign'));
        }
        return null;
    }
}