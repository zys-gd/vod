<?php

namespace IdentificationBundle\Identification\Service;

/**
 * Interface TranslatorInterface
 */
interface TranslatorInterface
{
    /**
     * @param string $translationKey
     * @param $billingCarrierId
     * @param string $languageCode
     *
     * @return string|null
     */
    public function translate(string $translationKey, $billingCarrierId, string $languageCode): ?string;
}