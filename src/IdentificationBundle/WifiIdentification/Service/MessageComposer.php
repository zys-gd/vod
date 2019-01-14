<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 11.01.19
 * Time: 17:03
 */

namespace IdentificationBundle\WifiIdentification\Service;


class MessageComposer
{

    private $messages;

    public function composePinCodeMessage(string $subscriptionInformation, string $lang, string $pinCode): string
    {
        $messageTemplate = isset($this->messages['pin_' . $lang])
            ? $this->messages['pin_' . $lang]
            : $this->messages['pin_en'];


        $pinMessage = str_replace(
            ['_subscription_information_', '_pincode_'],
            [$subscriptionInformation, $pinCode],
            $messageTemplate
        );

        return $pinMessage;
    }
}