<?php
/**
 * Created by PhpStorm.
 * User: Yurii Z
 * Date: 21-01-19
 * Time: 11:48
 */

namespace App\CarrierTemplate;

use App\CarrierTemplate\Handler\CommonHandler;
use App\CarrierTemplate\Handler\TemplateHandlerInterface;
use IdentificationBundle\Entity\CarrierInterface;

class TemplateConfigurator
{
    private $templateHandlers = [];
    private $commonHandler;

    /**
     * TemplateConfigurator constructor.
     */
    public function __construct(TemplateHandlerInterface $commonHandler)
    {
        $this->commonHandler = $commonHandler;
    }

    public function addHandler(TemplateHandlerInterface $handler)
    {
        $this->templateHandlers[] = $handler;
    }

    /**
     * @param CarrierInterface $carrier
     *
     * @return TemplateHandlerInterface
     */
    public function getTemplateHandler(CarrierInterface $carrier): TemplateHandlerInterface
    {
        foreach ($this->templateHandlers as $templateHandler) {
            if ($templateHandler->canHandle($carrier)) {
                return $templateHandler;
            }
        }

        return $this->commonHandler;
    }
}