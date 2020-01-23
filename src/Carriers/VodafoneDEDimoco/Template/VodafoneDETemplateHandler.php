<?php

namespace Carriers\VodafoneDEDimoco\Template;

use CommonDataBundle\Service\TemplateConfigurator\Handler\TemplateHandlerInterface;
use IdentificationBundle\BillingFramework\ID;

/**
 * Class VodafoneDETemplateHandler
 */
class VodafoneDETemplateHandler implements TemplateHandlerInterface
{
    /**
     * @param int $billingCarrierId
     *
     * @return bool
     */
    public function canHandle(int $billingCarrierId): bool
    {
        return $billingCarrierId === ID::VODAFONE_DE_DIMOCO;
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
        return "$rootTwigPathAlias/Impl/VodafoneDEDimoco/$templatePath/$templateName.html.twig";
    }
}