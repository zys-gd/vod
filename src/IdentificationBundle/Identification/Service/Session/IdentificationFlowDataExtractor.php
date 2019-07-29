<?php

namespace IdentificationBundle\Identification\Service\Session;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class IdentificationFlowDataExtractor
 */
class IdentificationFlowDataExtractor
{
    /**
     * @param SessionInterface $session
     *
     * @return array|null
     */
    public static function extractIdentificationData(SessionInterface $session): ?array
    {
        return $session->get(IdentificationDataStorage::IDENTIFICATION_DATA_KEY);
    }

    /**
     * @param SessionInterface $session
     *
     * @return string|null
     */
    public static function extractIdentificationToken(SessionInterface $session): ?string
    {
        $identificationData = $session->get(IdentificationDataStorage::IDENTIFICATION_DATA_KEY);

        return isset($identificationData['identification_token'])
            ? $identificationData['identification_token']
            : null;
    }

    /**
     * @param SessionInterface $session
     *
     * @return array|null
     */
    public static function extractIspDetectionData(SessionInterface $session): ?array
    {
        return $session->get(IdentificationDataStorage::ISP_DETECTION_DATA_KEY);
    }

    /**
     * @param SessionInterface $session
     *
     * @return int|null
     */
    public static function extractBillingCarrierId(SessionInterface $session): ?int
    {
        $ispData = $session->get(IdentificationDataStorage::ISP_DETECTION_DATA_KEY);

        return isset($ispData['carrier_id']) ? (int) $ispData['carrier_id'] : null;
    }
}