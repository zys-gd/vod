<?php

namespace Carriers\ZainKSA\Template;

use CommonDataBundle\Service\TemplateConfigurator\Handler\TemplateHandlerInterface;
use IdentificationBundle\BillingFramework\ID;

/**
 * Class ZainKSATemplateHandler
 */
class ZainKSATemplateHandler implements TemplateHandlerInterface
{
    /**
     * @param int $billingCarrierId
     *
     * @return bool
     */
    public function canHandle(int $billingCarrierId): bool
    {
        return $billingCarrierId === ID::ZAIN_SAUDI_ARABIA;
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
        return "$rootTwigPathAlias/Impl/ZainKSA/$templatePath/$templateName.html.twig";
    }
}