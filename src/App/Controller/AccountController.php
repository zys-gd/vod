<?php

namespace App\Controller;

use App\CarrierTemplate\TemplateConfigurator;
use App\Domain\Repository\CarrierRepository;
use IdentificationBundle\Controller\ControllerWithISPDetection;
use IdentificationBundle\Identification\DTO\ISPData;
use SubscriptionBundle\Service\SubscriptionExtractor;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController implements ControllerWithISPDetection, AppControllerInterface
{
    /**
     * @var SubscriptionExtractor
     */
    private $subscriptionExtractor;
    /**
     * @var TemplateConfigurator
     */
    private $templateConfigurator;
    /**
     * @var CarrierRepository
     */
    private $carrierRepository;

    /**
     * AccountController constructor.
     *
     * @param CarrierRepository     $carrierRepository
     * @param SubscriptionExtractor $subscriptionExtractor
     * @param TemplateConfigurator  $templateConfigurator
     */
    public function __construct(
        CarrierRepository $carrierRepository,
        SubscriptionExtractor $subscriptionExtractor,
        TemplateConfigurator $templateConfigurator
    ) {
        $this->subscriptionExtractor = $subscriptionExtractor;
        $this->templateConfigurator  = $templateConfigurator;
        $this->carrierRepository     = $carrierRepository;
    }

    /**
     * @Route("/account",name="account")
     * @param Request $request
     * @param ISPData $data
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function accountAction(Request $request, ISPData $data)
    {
        $subscription = $this->subscriptionExtractor->extractSubscriptionFromSession($request->getSession());

        $templateParams = [];

        if (!is_null($subscription)) {
            $templateParams['subscriptionCreatedDate'] = $subscription->getCreated();
        }

        $template = $this->templateConfigurator->getTemplate('account', $data->getCarrierId());

        return $this->render($template, $templateParams);
    }
}
