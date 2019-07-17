<?php

namespace IdentificationBundle\Identification\Service;

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
        return $session->get('identification_data');
    }

    /**
     * @param SessionInterface $session
     *
     * @return string|null
     */
    public static function extractIdentificationToken(SessionInterface $session): ?string
    {
        $identificationData = $session->get('identification_data');

        return isset($identificationData['identification_token']) ? $identificationData['identification_token'] : null;
    }

    /**
     * @param SessionInterface $session
     *
     * @return array|null
     */
    public static function extractIspDetectionData(SessionInterface $session): ?array
    {
        return $session->get('isp_detection_data');
    }

    /**
     * @param SessionInterface $session
     *
     * @return int|null
     */
    public static function extractBillingCarrierId(SessionInterface $session): ?int
    {
        $ispData = $session->get('isp_detection_data');

        return isset($ispData['carrier_id']) ? (int) $ispData['carrier_id'] : null;
    }
}