<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 15.01.19
 * Time: 11:35
 */

namespace IdentificationBundle\WifiIdentification\Service;


class ErrorCodeResolver
{
    public function resolveMessage(int $billingResponseCode): string
    {

        switch ($billingResponseCode) {
            case 101:
                return 'You are already subscribed';
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