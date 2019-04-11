<?php
/**
 * Created by PhpStorm.
 * User: Yurii Z
 * Date: 21-01-19
 * Time: 12:27
 */

namespace App\CarrierTemplate\Handler;

interface TemplateHandlerInterface
{
    public function canHandle(int $billingCarrierId): bool;

    public function getFullTemplatePath(string $templateName): string;
}