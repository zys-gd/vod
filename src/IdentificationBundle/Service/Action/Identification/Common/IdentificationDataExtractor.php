<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 10.01.19
 * Time: 11:03
 */

namespace IdentificationBundle\Service\Action\Identification\Common;


use Symfony\Component\HttpFoundation\Session\SessionInterface;

class IdentificationDataExtractor
{
    public static function extractFromSession(SessionInterface $session): ?array
    {
        return $session->get('identification_data');
    }
}