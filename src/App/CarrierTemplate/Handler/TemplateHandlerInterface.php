<?php
/**
 * Created by PhpStorm.
 * User: Yurii Z
 * Date: 21-01-19
 * Time: 12:27
 */

namespace App\CarrierTemplate\Handler;

use IdentificationBundle\Entity\CarrierInterface;

interface TemplateHandlerInterface
{
    public function canHandle(CarrierInterface $carrier): bool;

    public function getFullTemplatePath(string $templateName): string;
}