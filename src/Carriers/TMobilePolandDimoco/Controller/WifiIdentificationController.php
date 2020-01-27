<?php

namespace Carriers\TMobilePolandDimoco\Controller;

use CommonDataBundle\Service\TemplateConfigurator\Exception\TemplateNotFoundException;
use CommonDataBundle\Service\TemplateConfigurator\TemplateConfigurator;
use IdentificationBundle\Controller\ControllerWithISPDetection;
use IdentificationBundle\Identification\DTO\ISPData;
use IdentificationBundle\Identification\Service\Session\IdentificationFlowDataExtractor;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class WifiIdentificationController
 */
class WifiIdentificationController extends AbstractController implements ControllerWithISPDetection
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
     * @Route("/pin-confirmation", name="dimoco_pin_confirmation_page", methods={"GET"})
     * @param Request $request
     *
     * @return Response
     * @throws TemplateNotFoundException
     */
    public function pinConfirmationPageWifiLP(Request $request)
    {
        $billingCarrierId = IdentificationFlowDataExtractor::extractBillingCarrierId($request->getSession());

        $template = $this->templateConfigurator->getTemplate('pin_confirmation_page', $billingCarrierId);

        return $this->render($template, ['phoneNumber' => (string)$request->get('mobile_number', '')]);
    }
}