<?php

namespace Carriers\Controller;

use CommonDataBundle\Service\TemplateConfigurator\Exception\TemplateNotFoundException;
use CommonDataBundle\Service\TemplateConfigurator\TemplateConfigurator;
use IdentificationBundle\Controller\ControllerWithISPDetection;
use IdentificationBundle\Identification\DTO\ISPData;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DimocoController
 */
class DimocoController extends AbstractController implements ControllerWithISPDetection
{
    /**
     * @var TemplateConfigurator
     */
    private $templateConfigurator;

    /**
     * TMobilePolandDimocoController constructor
     *
     * @param TemplateConfigurator $templateConfigurator
     */
    public function __construct(TemplateConfigurator $templateConfigurator)
    {
        $this->templateConfigurator = $templateConfigurator;
    }

    /**
     * @Route("/dimoco-prepayment",name="dimoco_prepayment")
     *
     * @param ISPData $data
     *
     * @return Response
     *
     * @throws TemplateNotFoundException
     */
    public function prepaymentAction(ISPData $data)
    {
        $template = $this->templateConfigurator->getTemplate('prepayment', $data->getCarrierId());

        return $this->render($template);
    }

    /**
     * @Route("/dimoco-payment",name="dimoco_payment")
     *
     * @param ISPData $data
     *
     * @return Response
     *
     * @throws TemplateNotFoundException
     */
    public function paymentAction(ISPData $data)
    {
        $template = $this->templateConfigurator->getTemplate('payment', $data->getCarrierId());

        return $this->render($template);
    }
}