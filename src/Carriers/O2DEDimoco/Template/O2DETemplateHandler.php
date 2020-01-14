<?php

namespace Carriers\O2DEDimoco\Template;

use CommonDataBundle\Service\TemplateConfigurator\Handler\TemplateHandlerInterface;
use IdentificationBundle\BillingFramework\ID;

/**
 * Class O2DETemplateHandler
 */
class O2DETemplateHandler implements TemplateHandlerInterface
{
    /**
     * @param int $billingCarrierId
     *
     * @return bool
     */
    public function canHandle(int $billingCarrierId): bool
    {
        return $billingCarrierId === ID::O2_DE_DIMOCO;
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
        return "$rootTwigPathAlias/Impl/O2DEDimoco/$templatePath/$templateName.html.twig";
    }
}