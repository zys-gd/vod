<?php
/**
 * Created by PhpStorm.
 * User: Yurii Z
 * Date: 21-01-19
 * Time: 13:09
 */

namespace App\CarrierTemplate\Handler;

class CommonTemplateHandler implements TemplateHandlerInterface
{
    public function canHandle(int $billingCarrierId): bool
    {
        return true;
    }

    public function getFullTemplatePath(string $templateName): string
    {
        return '@App/Common/' . $templateName . '.html.twig';
    }
}