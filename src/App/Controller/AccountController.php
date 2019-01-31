<?php
/**
 * Created by PhpStorm.
 * User: Iliya Kobus
 * Date: 1/17/2019
 * Time: 2:19 PM
 */

namespace App\Controller;

use App\CarrierTemplate\TemplateConfigurator;
use App\Domain\Repository\CarrierRepository;
use IdentificationBundle\Identification\DTO\ISPData;
use SubscriptionBundle\Service\SubscriptionExtractor;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AccountController extends AbstractController
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
    public function __construct(CarrierRepository $carrierRepository,
        SubscriptionExtractor $subscriptionExtractor,
        TemplateConfigurator $templateConfigurator)
    {
        $this->subscriptionExtractor = $subscriptionExtractor;
        $this->templateConfigurator = $templateConfigurator;
        $this->carrierRepository = $carrierRepository;
    }


    /**
     * @Route("/account",name="account")
     * @param Request $request
     * @param ISPData $data
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function termsAndConditionsAction(Request $request, ISPData $data)
    {
        $carrier = $this->carrierRepository->findOneByBillingId($data->getCarrierId());

        $subscription = $this->subscriptionExtractor->extractSubscriptionFromSession($request->getSession());

        $templateParams = [
            'templateHandler' => $this->templateConfigurator->getTemplateHandler($carrier)
        ];
        !is_null($subscription) && $templateParams['subscriptionCreatedDate'] = $subscription->getCreated();
        return $this->render('@App/Common/account.html.twig', $templateParams);
    }
}
