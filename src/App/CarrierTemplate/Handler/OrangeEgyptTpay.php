<?php

namespace App\CarrierTemplate\Handler;

use App\Domain\Constants\ConstBillingCarrierId;

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
        return ConstBillingCarrierId::ORANGE_EGYPT_TPAY === $billingCarrierId;
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