<?php

namespace IdentificationBundle\Carriers\OrangeEGTpay;

use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use ExtrasBundle\Utils\LocalExtractor;
use IdentificationBundle\BillingFramework\ID;
use IdentificationBundle\Identification\Handler\ConsentPageFlow\HasCommonConsentPageFlow;
use IdentificationBundle\Identification\Handler\ConsentPageFlow\HasConsentPageFlow;
use IdentificationBundle\Identification\Handler\IdentificationHandlerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class OrangeEGIdentificationHandler
 */
class OrangeEGIdentificationHandler implements IdentificationHandlerInterface, HasConsentPageFlow, HasCommonConsentPageFlow
{
    /**
     * @var LocalExtractor
     */
    private $localExtractor;

    /**
     * VodafoneEGIdentificationHandler constructor
     *
     * @param LocalExtractor $localExtractor
     */
    public function __construct(LocalExtractor $localExtractor)
    {
        $this->localExtractor = $localExtractor;
    }

    /**
     * @param CarrierInterface $carrier
     *
     * @return bool
     */
    public function canHandle(CarrierInterface $carrier): bool
    {
        return $carrier->getBillingCarrierId() === ID::ORANGE_EGYPT_TPAY;
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    public function getAdditionalIdentificationParams(Request $request): array
    {
        return ['lang' => $this->localExtractor->getLocal()];
    }
}