<?php
/**
 * Created by PhpStorm.
 * User: Yurii Z
 * Date: 21-01-19
 * Time: 12:23
 */

namespace App\CarrierTemplate\Handler;


use App\Domain\Constants\ConstBillingCarrierId;

class EtisalatEGTemplateHandler implements TemplateHandlerInterface
{
    public function canHandle(int $billingCarrierId): bool
    {
        return ConstBillingCarrierId::ETISALAT_EGYPT === $billingCarrierId;
    }

    public function getFullTemplatePath(string $templateName): string
    {
        return '@App/Impl/EtisalatEgypt/' . $templateName . '.html.twig';
    }
}