<?php


namespace SubscriptionBundle\CampaignConfirmation\Handler;


use SubscriptionBundle\Affiliate\Service\CampaignExtractor;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CampaignConfirmationHandlerProvider
{
    /** @var CampaignConfirmationHandlerProvider array */
    private $handlers = [];
    /** @var CampaignConfirmationInterface */
    private $defaultHandler;
    /**
     * @var CampaignExtractor
     */
    private $campaignExtractor;

    /**
     * CampaignConfirmationHandlerProvider constructor.
     *
     * @param CampaignConfirmationInterface                           $defaultHandler
     * @param \SubscriptionBundle\Affiliate\Service\CampaignExtractor $campaignExtractor
     */
    public function __construct(CampaignConfirmationInterface $defaultHandler, CampaignExtractor $campaignExtractor)
    {
        $this->defaultHandler = $defaultHandler;
        $this->campaignExtractor = $campaignExtractor;
    }

    /**
     * @param CampaignConfirmationInterface $handler
     */
    public function addHandler(CampaignConfirmationInterface $handler): void
    {
        $this->handlers[] = $handler;
    }

    /**
     * @param string $affiliateUuid
     *
     * @return CampaignConfirmationInterface
     */
    public function getHandler(string $affiliateUuid): CampaignConfirmationInterface
    {
        /** @var CampaignConfirmationInterface $handler */
        foreach ($this->handlers as $handler) {
            if ($handler->canHandle($affiliateUuid)) {
                return $handler;
            }
        }
        return $this->defaultHandler;
    }

    /**
     * @param SessionInterface $session
     *
     * @return CampaignConfirmationInterface
     */
    public function provideHandler(SessionInterface $session): CampaignConfirmationInterface
    {
        try{
            $affiliate = $this->campaignExtractor->extractAffiliateFromCampaign($session);
            return $this->getHandler($affiliate->getUuid());
        } catch (\Throwable $e) {
            return $this->defaultHandler;
        }
    }
}