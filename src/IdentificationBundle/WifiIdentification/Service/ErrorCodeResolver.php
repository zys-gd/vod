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
        $lang = $this->localExtractor->getLocal();

        switch ($billingResponseCode) {
            case 100:
                return $this->translator->translate('messages.error.wrong_phone_number', $carrierId, $lang);
                break;
            case 101:
                return $this->translator->translate('messages.error.already_subscribed', $carrierId, $lang);
                break;
            case 102:
            case 103:
                return $this->translator->translate('message.error.pin_request_limit_exceeded', $carrierId, $lang);
                break;
            case 104:
                return $this->translator->translate('message.error.invalid_pin', $carrierId, $lang);
            case 105:
                return $this->translator->translate('messages.info.not_enough_credit', $carrierId, $lang);
            default:
                return 'Internal Error';
                break;
        }

    }

}