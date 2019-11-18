<?php

namespace Carriers\TMobilePolandDimoco\OneClick;

use App\Domain\Service\OneClickFlow\HasCustomOneClickRedirectRules;
use App\Domain\Service\OneClickFlow\OneClickFlowInterface;
use App\Domain\Service\OneClickFlow\OneClickFlowParameters;
use IdentificationBundle\BillingFramework\ID;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class TMobilePLDimocoOneClickHandler
 */
class TMobilePLDimocoOneClickHandler implements OneClickFlowInterface, HasCustomOneClickRedirectRules
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * TMobilePLDimocoOneClickHandler constructor
     *
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @param int $billingCarrierId
     * @param int $flowType
     *
     * @return bool
     */
    public function canHandle(int $billingCarrierId, int $flowType): bool
    {
        return $billingCarrierId === ID::TMOBILE_POLAND_DIMOCO && $flowType === $this->getFlowType();
    }

    /**
     * @return string
     */
    public function getRedirectUrl(): string
    {
        return $this->router->generate('payment');
    }

    /**
     * @return int
     */
    public function getFlowType(): int
    {
        return OneClickFlowParameters::LP_OFF;
    }
}