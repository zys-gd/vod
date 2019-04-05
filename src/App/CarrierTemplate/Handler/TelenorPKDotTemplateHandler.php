<?php


namespace App\CarrierTemplate\Handler;


use App\Domain\Constants\ConstBillingCarrierId;

class TelenorPKDotTemplateHandler implements TemplateHandlerInterface
{

    public function canHandle(int $billingCarrierId): bool
    {
        return $billingCarrierId === ConstBillingCarrierId::TELENOR_PAKISTAN_DOT;
    }

    public function getFullTemplatePath(string $templateName): string
    {
        return '@App/Impl/TelenorPKDot/' . $templateName . '.html.twig';
    }
}