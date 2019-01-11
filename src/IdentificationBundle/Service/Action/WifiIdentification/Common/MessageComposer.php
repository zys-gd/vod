<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 11.01.19
 * Time: 17:03
 */

namespace IdentificationBundle\Service\Action\WifiIdentification\Common;


class MessageComposer
{

    private $messages;

    public function composePinCodeMessage(string $subscriptionInformation, string $lang): string
    {
        $messageTemplate = isset($this->messages['pin_' . $lang])
            ? $this->messages['pin_' . $lang]
            : $this->messages['pin_en'];

        $pinCode = random_int(100000, 999999);
        //$this->pinCodeService->savePinCode($pinCode);

        $pinMessage = str_replace(
            ['_subscription_information_', '_pincode_'],
            [$subscriptionInformation, $pinCode],
            $messageTemplate
        );

        return $pinMessage;
    }
}