<?php

namespace App\CarrierTemplate\Handler;

use IdentificationBundle\BillingFramework\ID;

/**
 * Class OrangeEgyptTpay
 */
class OrangeEgyptTpay implements TemplateHandlerInterface
{
    /**
     * @param int $billingCarrierId
     *
     * @return bool
     */
    public function canHandle(int $billingCarrierId): bool
    {
        return ID::ORANGE_EGYPT_TPAY === $billingCarrierId;
    }

    /**
     * @param string $templateName
     *
     * @return string
     */
    public function getFullTemplatePath(string $templateName): string
    {
        return '@App/Impl/OrangeEgyptTpay/' . $templateName . '.html.twig';
    }
}