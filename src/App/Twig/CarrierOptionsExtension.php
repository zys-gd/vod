<?php


namespace App\Twig;


use App\Domain\Repository\CarrierRepository;
use App\Domain\Service\CarrierProvider;
use App\Domain\Service\OneClickFlow\OneClickFlowChecker;
use App\Domain\Service\OneClickFlow\OneClickFlowParameters;
use IdentificationBundle\Identification\Service\PassthroughChecker;
use IdentificationBundle\Identification\Service\Session\IdentificationFlowDataExtractor;
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
     * @var OneClickFlowChecker
     */
    private $oneClickFlowChecker;
    /**
     * @var CarrierProvider
     */
    private $carrierProvider;

    /**
     * CarrierOptionsExtension constructor.
     *
     * @param SessionInterface    $session
     * @param CarrierRepository   $carrierRepository
     * @param PassthroughChecker  $passthroughChecker
     * @param CampaignExtractor   $campaignExtractor
     * @param OneClickFlowChecker $oneClickFlowChecker
     * @param CarrierProvider     $carrierProvider
     */
    public function __construct(
        SessionInterface $session,
        CarrierRepository $carrierRepository,
        PassthroughChecker $passthroughChecker,
        CampaignExtractor $campaignExtractor,
        OneClickFlowChecker $oneClickFlowChecker,
        CarrierProvider $carrierProvider

    )
    {
        $this->session             = $session;
        $this->carrierRepository   = $carrierRepository;
        $this->passthroughChecker  = $passthroughChecker;
        $this->campaignExtractor   = $campaignExtractor;
        $this->oneClickFlowChecker = $oneClickFlowChecker;
        $this->carrierProvider     = $carrierProvider;
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
     */
    public function isConfirmationClick(): bool
    {
        return $this->oneClickFlowTwigResolver(OneClickFlowParameters::CONFIRMATION_CLICK);
    }

    /**
     * @return bool
     */
    public function isConfirmationPopup()
    {
        return $this->oneClickFlowTwigResolver(OneClickFlowParameters::CONFIRMATION_POP_UP);
    }

    private function oneClickFlowTwigResolver(int $oneClickFlowRequestedParameter)
    {

        $billingCarrierId = IdentificationFlowDataExtractor::extractBillingCarrierId($this->session);

        if ($billingCarrierId) {

            $carrier  = $this->carrierProvider->fetchCarrierIfNeeded($billingCarrierId);
            $campaign = $this->campaignExtractor->getCampaignFromSession($this->session);

            $isSupportRequestedFlow = $this->oneClickFlowChecker->check($billingCarrierId, $oneClickFlowRequestedParameter);

            if ($isSupportRequestedFlow) {
                if ($carrier->isOneClickFlow() && $campaign) {
                    return $campaign->isOneClickFlow();
                }
                return $carrier->isOneClickFlow();
            }
        }
        return false;
    }

}