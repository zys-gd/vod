<?php

namespace IdentificationBundle\Carriers\OrangeEGTpay;

use App\Domain\Constants\ConstBillingCarrierId;
use ExtrasBundle\Utils\LocalExtractor;
use IdentificationBundle\Entity\CarrierInterface;
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
        return $carrier->getBillingCarrierId() === ConstBillingCarrierId::ORANGE_EGYPT_TPAY;
    }

    /**
     * @param Request $request
     * @param CarrierInterface $carrier
     *
     * @return array
     */
    public function getAdditionalIdentificationParams(Request $request, CarrierInterface $carrier): array
    {
        $defaultLang = $carrier->getDefaultLanguage();

        $lang = empty($defaultLang) ? $this->localExtractor->getLocal() : $defaultLang->getCode();

        return ['lang' => $lang];
    }
}