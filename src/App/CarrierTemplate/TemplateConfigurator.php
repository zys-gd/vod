<?php
/**
 * Created by PhpStorm.
 * User: Yurii Z
 * Date: 21-01-19
 * Time: 11:48
 */

namespace App\CarrierTemplate;

use App\CarrierTemplate\Handler\TemplateHandlerInterface;
use Twig\Environment;

class TemplateConfigurator
{
    private $templateHandlers = [];
    private $commonHandler;
    /**
     * @var Environment
     */
    private $templating;

    /**
     * TemplateConfigurator constructor.
     *
     * @param Environment              $templating
     * @param TemplateHandlerInterface $commonHandler
     */
    public function __construct(Environment $templating, TemplateHandlerInterface $commonHandler)
    {
        $this->templating = $templating;
        $this->commonHandler = $commonHandler;
    }

    public function addHandler(TemplateHandlerInterface $handler)
    {
        $this->templateHandlers[] = $handler;
    }

    /**
     * @param int $billingCarrierId
     *
     * @return TemplateHandlerInterface
     */
    private function getTemplateHandler(int $billingCarrierId): TemplateHandlerInterface
    {
        /** @var TemplateHandlerInterface $templateHandler */
        foreach ($this->templateHandlers as $templateHandler) {
            if ($templateHandler->canHandle($billingCarrierId)) {
                return $templateHandler;
            }
        }

        return $this->commonHandler;
    }

    /**
     * @param string $template
     * @param int    $billingCarrierId
     *
     * @return string
     */
    public function getTemplate(string $template, int $billingCarrierId = 0)
    {
        $implTemplate = $this->getTemplateHandler($billingCarrierId)->getFullTemplatePath($template);
        $isImplTemplateExist = $this->templating->getLoader()->exists($implTemplate);

        if ($isImplTemplateExist) {
            return $implTemplate;
        }

        return "@App/Common/{$template}.html.twig";
    }

    public function getLPTemplate(string $template, int $billingCarrierId = 0, bool $isWifiFlow = false)
    {
        $implTemplate = $this->getTemplateHandler($billingCarrierId)->getFullTemplatePath($template);
        $isImplTemplateExist = $this->templating->getLoader()->exists($implTemplate);
        //
        if ($isImplTemplateExist) {
            return $implTemplate;
        }

        if($isWifiFlow) {
            return "@App/Common/landing/wifi/{$template}.html.twig";
        }
        return "@App/Common/landing/3g/{$template}.html.twig";
    }
}