<?php

namespace Carriers\DebitelDEDimoco\Template;

use CommonDataBundle\Service\TemplateConfigurator\Handler\TemplateHandlerInterface;
use IdentificationBundle\BillingFramework\ID;

/**
 * Class DebitelDETemplateHandler
 */
class DebitelDETemplateHandler implements TemplateHandlerInterface
{
    /**
     * @param int $billingCarrierId
     *
     * @return bool
     */
    public function canHandle(int $billingCarrierId): bool
    {
        return $billingCarrierId === ID::DEBITEL_DE_DIMOCO;
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