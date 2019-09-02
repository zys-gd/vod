<?php

namespace App\CarrierTemplate\Handler;

use IdentificationBundle\BillingFramework\ID;

/**
 * Class VodafoneEgyptTpay
 */
class VodafoneEgyptTpayTemplateHandler implements TemplateHandlerInterface
{
    /**
     * @param int $billingCarrierId
     *
     * @return bool
     */
    public function canHandle(int $billingCarrierId): bool
    {
        return ID::VODAFONE_EGYPT_TPAY === $billingCarrierId;
    }

    /**
     * @param string $templateName
     *
     * @return string
     */
    public function getFullTemplatePath(string $templateName): string
    {
        return '@App/Impl/VodafoneEgyptTpay/' . $templateName . '.html.twig';
    }
}