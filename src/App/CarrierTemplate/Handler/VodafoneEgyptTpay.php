<?php

namespace App\CarrierTemplate\Handler;

use App\Domain\Constants\ConstBillingCarrierId;

/**
 * Class VodafoneEgyptTpay
 */
class VodafoneEgyptTpay implements TemplateHandlerInterface
{
    /**
     * @param int $billingCarrierId
     *
     * @return bool
     */
    public function canHandle(int $billingCarrierId): bool
    {
        return ConstBillingCarrierId::VODAFONE_EGYPT_TPAY === $billingCarrierId;
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