<?php


namespace App\Twig;


use App\Domain\Entity\Carrier;
use App\Domain\Repository\CarrierRepository;
use IdentificationBundle\Identification\Service\PassthroughChecker;
use IdentificationBundle\Identification\Service\Session\IdentificationFlowDataExtractor;
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
     * CarrierOptionsExtension constructor.
     *
     * @param SessionInterface   $session
     * @param CarrierRepository  $carrierRepository
     * @param PassthroughChecker $passthroughChecker
     */
    public function __construct(SessionInterface $session,
        CarrierRepository $carrierRepository,
        PassthroughChecker $passthroughChecker)
    {
        $this->session            = $session;
        $this->carrierRepository  = $carrierRepository;
        $this->passthroughChecker = $passthroughChecker;
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('isConfirmationClick', [$this, 'isConfirmationClick']),

            new TwigFunction('isConfirmationPopup', [$this, 'isConfirmationPopup']),

            new TwigFunction('isCarrierPassthrough', [$this, 'isCarrierPassthrough']),
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

            return $carrier->isConfirmationPopup();
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isCarrierPassthrough(): bool
    {
        $ispDetectionData = IdentificationFlowDataExtractor::extractIspDetectionData($this->session);
        try {
            $billingCarrierId = (int)$ispDetectionData['carrier_id'];
            $carrier          = $this->carrierRepository->findOneByBillingId($billingCarrierId);
            return $this->passthroughChecker->isCarrierPassthrough($carrier);
        } catch (\Throwable $e) {
            return false;
        }
    }
}