<?php

namespace Carriers\TMobilePolandDimoco\Controller;

use CommonDataBundle\Service\TemplateConfigurator\Exception\TemplateNotFoundException;
use CommonDataBundle\Service\TemplateConfigurator\TemplateConfigurator;
use IdentificationBundle\Controller\ControllerWithISPDetection;
use IdentificationBundle\Identification\DTO\ISPData;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PrepaymentController
 */
class PaymentController extends AbstractController implements ControllerWithISPDetection
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
     * @Route("/payment",name="payment")
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

    /**
     * @Route("/payment-confirmation", name="payment_confirmation")
     *
     * @param Request $request
     * @param ISPData $data
     *
     * @return Response
     *
     * @throws TemplateNotFoundException
     */
    public function confirmationAction(Request $request, ISPData $data)
    {
        $result = $request->query->get('result', null);

        if (empty($result) || $result !== 'successful') {
            $reason = $request->query->get('reason', 'subscribe_error');
            // ProcessResult::ERROR_NOT_ENOUGH_CREDIT
            return $this->redirectToRoute('index', ['err_handle' => $reason]);
        }

        $template = $this->templateConfigurator->getTemplate('payment_confirmation', $data->getCarrierId());

        return $this->render($template);
    }
}