<?php


namespace App\CarrierTemplate\Handler;


use App\Domain\Constants\ConstBillingCarrierId;
use IdentificationBundle\Entity\CarrierInterface;

class TelenorPKDotHandler implements TemplateHandlerInterface
{

    public function canHandle(CarrierInterface $carrier): bool
    {
        return $carrier->getBillingCarrierId() == ConstBillingCarrierId::TELENOR_PAKISTAN_DOT;
    }

    public function getFullTemplatePath(string $templateName): string
    {
        return '@App/TelenorPKDot/' . $templateName;
    }
}