<?php


namespace App\Twig;


use App\Domain\Service\CarrierProvider;
use CommonDataBundle\Entity\Interfaces\CarrierInterface;
use ExtrasBundle\Utils\LocalExtractor;
use IdentificationBundle\Identification\Service\Session\IdentificationFlowDataExtractor;
use IdentificationBundle\Repository\CarrierRepositoryInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Session\Session;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class LocalizationExtension extends AbstractExtension
{
    /**
     * @var LocalExtractor
     */
    private $localExtractor;
    /**
     * @var Session
     */
    private $session;
    /**
     * @var Filesystem
     */
    private $filesystem;
    /**
     * @var CarrierProvider
     */
    private $carrierProvider;

    /**
     * LocalizationExtension constructor.
     * @param LocalExtractor  $localExtractor
     * @param Session         $session
     * @param Filesystem      $filesystem
     * @param CarrierProvider $carrierProvider
     */
    public function __construct(
        LocalExtractor $localExtractor,
        Session $session,
        Filesystem $filesystem,
        CarrierProvider $carrierProvider
    )
    {
        $this->localExtractor  = $localExtractor;
        $this->session         = $session;
        $this->filesystem      = $filesystem;
        $this->carrierProvider = $carrierProvider;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('getLanguageCode', [$this, 'getLanguageCodeInLowerCase']),
            new TwigFunction('getLocalizationCSSPath', [$this, 'getLocalizationCSSPath']),
        ];
    }


    public function getLanguageCodeInLowerCase()
    {
        $localLanguageCode = $this->localExtractor->getLocal();

        $billingCarrierId  = (int) IdentificationFlowDataExtractor::extractBillingCarrierId($this->session);
        /** @var CarrierInterface $carrier */
        $carrier = $billingCarrierId ? $this->carrierProvider->fetchCarrierIfNeeded($billingCarrierId) : null;

        if ($carrier && $carrier->getDefaultLanguage()) {
            return strtolower($carrier->getDefaultLanguage()->getCode());
        }

        return strtolower($localLanguageCode);
    }

    public function getLocalizationCSSPath()
    {
        $langCode            = $this->getLanguageCodeInLowerCase();
        $localizationCSSPath = "css/localizations/$langCode.css";

        if (!$this->filesystem->exists($localizationCSSPath)) {
            return null;
        }

        return $localizationCSSPath;
    }
}