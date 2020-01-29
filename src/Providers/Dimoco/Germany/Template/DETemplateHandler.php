<?php

namespace Providers\Dimoco\Germany\Template;

use CommonDataBundle\Service\TemplateConfigurator\Handler\TemplateHandlerInterface;
use IdentificationBundle\BillingFramework\ID;

/**
 * Class DETemplateHandler
 * @package Providers\Dimoco\Germany\Template
 */
class DETemplateHandler implements TemplateHandlerInterface
{
    /**
     * @param int $billingCarrierId
     *
     * @return bool
     */
    public function canHandle(int $billingCarrierId): bool
    {
        return in_array($billingCarrierId, ID::DE_DIMOCO_CARRIERS);
    }

    /**
     * @param string $rootTwigPathAlias
     * @param string $templatePath
     * @param string $templateName
     *
     * @return string
     */
    public function getFullTemplatePath(string $rootTwigPathAlias, string $templatePath, string $templateName): string
    {
        return "$rootTwigPathAlias/Impl/DebitelDEDimoco/$templatePath/$templateName.html.twig";
    }
}