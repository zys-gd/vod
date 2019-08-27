<?php
/**
 * Created by PhpStorm.
 * User: Yurii Z
 * Date: 21-01-19
 * Time: 12:23
 */

namespace App\CarrierTemplate\Handler;


use IdentificationBundle\BillingFramework\ID;

class EtisalatEGTemplateHandler implements TemplateHandlerInterface
{
    public function canHandle(int $billingCarrierId): bool
    {
        return ID::ETISALAT_EGYPT === $billingCarrierId;
    }

    public function getFullTemplatePath(string $templateName): string
    {
        return '@App/Impl/EtisalatEgypt/' . $templateName . '.html.twig';
    }
}