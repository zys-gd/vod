<?php
/**
 * Created by PhpStorm.
 * User: Yurii Z
 * Date: 21-01-19
 * Time: 12:23
 */

namespace App\CarrierTemplate\Handler;


use App\CarrierTemplate\Handler\TemplateHandlerInterface;
use App\Domain\Constants\ConstBillingCarrierId;
use IdentificationBundle\Entity\CarrierInterface;

class EtisalatEGTemplateHandler implements TemplateHandlerInterface
{
    public function canHandle(CarrierInterface $carrier): bool
    {
        return ConstBillingCarrierId::ETISALAT_EGYPT === $carrier->getBillingCarrierId();
    }

    public function getFullTemplatePath(string $templateName): string
    {
        return '@App/EtisalatEgypt/' . $templateName;
    }
}