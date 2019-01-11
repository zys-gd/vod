<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 10.01.19
 * Time: 11:03
 */

namespace IdentificationBundle\Service\Action\Identification\Common;


use Symfony\Component\HttpFoundation\Session\SessionInterface;

class IdentificationFlowDataExtractor
{
    public static function extractIdentificationData(SessionInterface $session): ?array
    {
        return $session->get('identification_data');
    }

    public static function extractIspDetectionData(SessionInterface $session): ?array
    {
        return $session->get('isp_detection_data');
    }
}