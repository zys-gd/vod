<?php


namespace App\Twig;


use App\Domain\Entity\Campaign;
use App\Domain\Repository\CarrierRepository;
use App\Domain\Service\Carrier\CarrierProvider;
use App\Domain\Service\OneClickFlow\OneClickFlowCarriersProvider;
use App\Domain\Service\OneClickFlow\OneClickFlowParameters;
use App\Domain\Service\OneClickFlow\OneClickFlowScheduler;
use IdentificationBundle\Identification\Service\PassthroughChecker;
use IdentificationBundle\Identification\Service\Session\IdentificationFlowDataExtractor;
use Psr\Log\LoggerInterface;
use SubscriptionBundle\Affiliate\Service\CampaignExtractor;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CarrierOptionsExtension extends AbstractExtension
{
    /**
     * @var CarrierRepository
     */
    private $carrierRepository;
    /**
     * @var PassthroughChecker
     */
    private $passthroughChecker;
    /**
     * @var SessionInterface
     */
    private $session;
    /**
     * @var CampaignExtractor
     */
    private $campaignExtractor;
    /**
     * @var OneClickFlowCarriersProvider
     */
    private $oneClickFlowCarriersProvider;
    /**
     * @var OneClickFlowScheduler
     */
    private $oneClickFlowScheduler;
    /**
     * @var CarrierProvider
     */
    private $carrierProvider;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * CarrierOptionsExtension constructor.
     *
     * @param SessionInterface             $session
     * @param CarrierRepository            $carrierRepository
     * @param PassthroughChecker           $passthroughChecker
     * @param CampaignExtractor            $campaignExtractor
     * @param OneClickFlowCarriersProvider $oneClickFlowCarriersProvider
     * @param CarrierProvider              $carrierProvider
     * @param OneClickFlowScheduler        $oneClickFlowScheduler
     * @param LoggerInterface              $logger
     */
    public function __construct(
        SessionInterface $session,
        CarrierRepository $carrierRepository,
        PassthroughChecker $passthroughChecker,
        CampaignExtractor $campaignExtractor,
        OneClickFlowCarriersProvider $oneClickFlowCarriersProvider,
        CarrierProvider $carrierProvider,
        OneClickFlowScheduler $oneClickFlowScheduler,
        LoggerInterface $logger
    )
    {
        $this->session                      = $session;
        $this->carrierRepository            = $carrierRepository;
        $this->passthroughChecker           = $passthroughChecker;
        $this->campaignExtractor            = $campaignExtractor;
        $this->oneClickFlowCarriersProvider = $oneClickFlowCarriersProvider;
        $this->carrierProvider              = $carrierProvider;
        $this->oneClickFlowScheduler        = $oneClickFlowScheduler;
        $this->logger                       = $logger;
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('isConfirmationClick', [$this, 'isConfirmationClick']),
            new TwigFunction('isConfirmationPopup', [$this, 'isConfirmationPopup']),
        ];
    }

    /**
     * @return bool
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function isConfirmationClick(): bool
    {
        return !$this->oneClickFlowTwigResolver(OneClickFlowParameters::CONFIRMATION_CLICK);
    }

    /**
     * @return bool
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function isConfirmationPopup()
    {
        return !$this->oneClickFlowTwigResolver(OneClickFlowParameters::CONFIRMATION_POP_UP);
    }

    /**
     * @param int $oneClickFlowRequestedParameter
     *
     * @return bool
     * @throws \Psr\Cache\InvalidArgumentException
     */
    private function oneClickFlowTwigResolver(int $oneClickFlowRequestedParameter)
    {

        $billingCarrierId = IdentificationFlowDataExtractor::extractBillingCarrierId($this->session);

        if ($billingCarrierId) {

            $carrier = $this->carrierProvider->fetchCarrierIfNeeded($billingCarrierId);
            /** @var Campaign|null $campaign */
            $campaign = $this->campaignExtractor->getCampaignFromSession($this->session);

            $handler = $this->oneClickFlowCarriersProvider->get($billingCarrierId, $oneClickFlowRequestedParameter);

            if ($handler) {
                if (!$carrier->isOneClickFlow() && $campaign) {
                    $schedule                            = $this->oneClickFlowScheduler->getScheduleAsArray($campaign->getSchedule());
                    $isCampaignScheduleExistAndTriggered = $schedule
                        ? $this->oneClickFlowScheduler->isNowInCampaignSchedule($schedule)
                        : true;
                    $this->logger->debug('Check one click for campaign', [$campaign->isOneClickFlow(), $isCampaignScheduleExistAndTriggered]);
                    return $campaign->isOneClickFlow() && $isCampaignScheduleExistAndTriggered;
                }
                return $carrier->isOneClickFlow();
            }
        }
        return true;
    }

}