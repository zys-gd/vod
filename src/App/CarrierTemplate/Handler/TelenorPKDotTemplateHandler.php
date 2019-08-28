<?php


namespace App\CarrierTemplate\Handler;


use IdentificationBundle\BillingFramework\ID;

class TelenorPKDotTemplateHandler implements TemplateHandlerInterface
{

    public function canHandle(int $billingCarrierId): bool
    {
        return $billingCarrierId === ID::TELENOR_PAKISTAN_DOT;
    }

    public function getFullTemplatePath(string $templateName): string
    {
        return '@App/Impl/TelenorPKDot/' . $templateName . '.html.twig';
    }
}