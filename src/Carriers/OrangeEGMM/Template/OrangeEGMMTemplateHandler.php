<?php


namespace Carriers\OrangeEGMM\Template;


use CommonDataBundle\Service\TemplateConfigurator\Handler\TemplateHandlerInterface;
use IdentificationBundle\BillingFramework\ID;

class OrangeEGMMTemplateHandler implements TemplateHandlerInterface
{

    /**
     * @param int $billingCarrierId
     *
     * @return bool
     */
    public function canHandle(int $billingCarrierId): bool
    {
        return $billingCarrierId === ID::ORANGE_EG_MM;
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
        return "$rootTwigPathAlias/Impl/OrangeTNMM/$templatePath/$templateName.html.twig";
    }
}