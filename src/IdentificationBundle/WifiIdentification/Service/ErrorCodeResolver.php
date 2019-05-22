<?php

namespace IdentificationBundle\WifiIdentification\Service;

use App\Domain\Service\Translator\Translator;
use ExtrasBundle\Utils\LocalExtractor;

/**
 * Class ErrorCodeResolver
 */
class ErrorCodeResolver
{
    /**
     * @var Translator
     */
    private $translator;

    /**
     * @var LocalExtractor
     */
    private $localExtractor;

    /**
     * ErrorCodeResolver constructor
     *
     * @param Translator $translator
     * @param LocalExtractor $localExtractor
     */
    public function __construct(Translator $translator, LocalExtractor $localExtractor)
    {
        $this->translator = $translator;
        $this->localExtractor = $localExtractor;
    }

    /**
     * @param int $billingResponseCode
     * @param int|null $carrierId
     *
     * @return string
     */
    public function resolveMessage(int $billingResponseCode, int $carrierId = null): string
    {
        switch ($billingResponseCode) {
            case 101:
                return $this->translator->translate('messages.error.already_subscribed', $carrierId, $this->localExtractor->getLocal());
                break;
            case 103:
                return 'Too many requests - please wait a bit';
                break;
            case 666:
                return 'You entered the wrong phone number. Please enter the correct phone number with international calling code';
                break;
            default:
                return 'Internal Error';
                break;
        }

    }

}