<?php
/**
 * Created by PhpStorm.
 * User: Yurii Z
 * Date: 21-01-19
 * Time: 13:09
 */

namespace App\CarrierTemplate\Handler;

use IdentificationBundle\Entity\CarrierInterface;

class CommonHandler implements TemplateHandlerInterface
{
    public function canHandle(CarrierInterface $carrier): bool
    {
        return true;
    }

    public function getFullTemplatePath(string $templateName): string
    {
        return '@App/Common/' . $templateName;
    }
}