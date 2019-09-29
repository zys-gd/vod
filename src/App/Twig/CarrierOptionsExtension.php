<?php


namespace App\Twig;


use App\Domain\Entity\Carrier;
use App\Domain\Repository\CarrierRepository;
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
     * CarrierOptionsExtension constructor.
     *
     * @param SessionInterface   $session
     * @param CarrierRepository  $carrierRepository
     * @param PassthroughChecker $passthroughChecker
     * @param CampaignExtractor  $campaignExtractor
     */
    public function __construct(
        SessionInterface $session,
        CarrierRepository $carrierRepository,
        PassthroughChecker $passthroughChecker,
        CampaignExtractor $campaignExtractor
    ) {
        $this->session            = $session;
        $this->carrierRepository  = $carrierRepository;
        $this->passthroughChecker = $passthroughChecker;
        $this->campaignExtractor  = $campaignExtractor;
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
        $billingCarrierId = IdentificationFlowDataExtractor::extractBillingCarrierId($this->session);

        if ($billingCarrierId) {
            /** @var Carrier $carrier */
            $carrier = $this->carrierRepository->findOneByBillingId($billingCarrierId);
            $campaign = $this->campaignExtractor->getCampaignFromSession($this->session);

            if ($carrier->isConfirmationClick() && $campaign) {
                return $campaign->isConfirmationClick();
            }

            return $carrier->isConfirmationClick();
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isConfirmationPopup(): bool
    {
        $billingCarrierId = IdentificationFlowDataExtractor::extractBillingCarrierId($this->session);

        if ($billingCarrierId) {
            /** @var Carrier $carrier */
            $carrier = $this->carrierRepository->findOneByBillingId($billingCarrierId);
            $campaign = $this->campaignExtractor->getCampaignFromSession($this->session);

            if ($carrier->isConfirmationPopup() && $campaign) {
                return $campaign->isConfirmationPopup();
            }

            return $carrier->isConfirmationPopup();
        }

        return false;
    }
}