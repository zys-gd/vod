<?php

namespace AppBundle\Service\Translator;

use SubscriptionBundle\Entity\SubscriptionPack;

class TranslatorShortcodeReplacer
{
    /**
     * @param                  $text
     * @param SubscriptionPack $oSubPack
     * @param array            $aTransformOptions
     * @return string
     */
    public function do($text, SubscriptionPack $oSubPack, array $aTransformOptions = [])
    {
        // $price    = $oSubPack->getPriceFromTier();
        // $currency = $oSubPack->getCurrencyFromTier();
        // $credits  = $oSubPack->isUnlimited() ? 'unlimited' : $oSubPack->getCredits();
        //
        // foreach ($aTransformOptions as $transform_options) {
        //     switch ($transform_options) {
        //         case 'format_price_sep':
        //             $price = number_format($price, 2, ",", "."); // use \NumberFormatter ?
        //             break;
        //         case 'format_price_2_decimals':
        //             $price = number_format($price, 2);
        //             break;
        //     }
        // }
        //
        // $shortcodes  = ['%price%', '%currency%', '%credits%'];
        // $replace     = [$price, $currency, $credits];
        // $replacement = vsprintf(str_replace($shortcodes, $replace, $text), $replace);
        //
        // return $replacement;
    }
}