<?php


namespace App\CarrierTemplate\Handler;


use IdentificationBundle\BillingFramework\ID;

class OrangeTNMMTemplateHandler implements TemplateHandlerInterface
{
    public function canHandle(int $billingCarrierId): bool
    {
        return ID::ORANGE_TUNISIA_MM === $billingCarrierId;
    }

    public function getFullTemplatePath(string $templateName): string
    {
        return '@App/Impl/OrangeTNMM/' . $templateName . '.html.twig';
    }
}