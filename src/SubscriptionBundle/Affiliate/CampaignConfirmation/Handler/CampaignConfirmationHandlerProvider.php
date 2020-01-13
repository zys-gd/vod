<?php


namespace SubscriptionBundle\Affiliate\CampaignConfirmation\Handler;


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
        $this->defaultHandler    = $defaultHandler;
        $this->campaignExtractor = $campaignExtractor;
    }

    /**
     * @param CampaignConfirmationInterface $handler
     */
    public function addHandler(CampaignConfirmationInterface $handler): void
    {
        $availableInterfaceString = json_encode([
            HasInstantConfirmation::class,
            HasDelayedConfirmation::class
        ]);

        $handlerClass = get_class($handler);


        if (
            (!$handler instanceof HasInstantConfirmation) &&
            (!$handler instanceof HasDelayedConfirmation)
        ) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Handler `%s` should implement one of following two interfaces `%s`',
                    $handlerClass,
                    $availableInterfaceString
                )
            );
        }

        if ($handler instanceof HasInstantConfirmation && $handler instanceof HasDelayedConfirmation) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Handler `%s` cannot implement both confirmation strategy. Please select one of `%s`',
                    $handlerClass,
                    $availableInterfaceString
                )
            );
        }

        $this->handlers[] = $handler;


    }

    /**
     * @param string $id
     * @return CampaignConfirmationInterface
     */
    public function getHandler(string $id): CampaignConfirmationInterface
    {
        /** @var CampaignConfirmationInterface $handler */
        foreach ($this->handlers as $handler) {
            if ($handler->getHandlerId() === $id) {
                return $handler;
            }
        }
        throw new \InvalidArgumentException(sprintf('Handler `%s` is not found', $id));
    }

    /**
     * @param string $affiliateUuid
     *
     * @return CampaignConfirmationInterface
     */
    public function getHandlerForAffiliateId(string $affiliateUuid): CampaignConfirmationInterface
    {
        /** @var CampaignConfirmationInterface $handler */
        foreach ($this->handlers as $handler) {
            if ($handler->isAffiliateSupported($affiliateUuid)) {
                return $handler;
            }
        }
        return $this->defaultHandler;
    }

    /**
     * @param SessionInterface $session
     *
     * @return CampaignConfirmationInterface
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getHandlerForSession(SessionInterface $session): CampaignConfirmationInterface
    {
        try {
            $affiliate = $this->campaignExtractor->extractAffiliateFromCampaign($session);
            return $this->getHandlerForAffiliateId($affiliate->getUuid());
        } catch (\Throwable $e) {
            return $this->defaultHandler;
        }
    }
}