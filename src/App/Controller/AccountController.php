<?php

namespace App\Controller;

use App\Domain\Repository\CarrierRepository;
use CommonDataBundle\Service\TemplateConfigurator\Exception\TemplateNotFoundException;
use CommonDataBundle\Service\TemplateConfigurator\TemplateConfigurator;
use Doctrine\ORM\NonUniqueResultException;
use IdentificationBundle\Controller\ControllerWithISPDetection;
use IdentificationBundle\Identification\DTO\ISPData;
use SubscriptionBundle\Subscription\Common\SubscriptionExtractor;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
     * @return Response
     *
     * @throws TemplateNotFoundException
     * @throws NonUniqueResultException
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
