<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 06.08.19
 * Time: 13:53
 */

namespace SubscriptionBundle\Piwik\Formatter;


use ExtrasBundle\Utils\TimestampGenerator;
use SubscriptionBundle\Piwik\DTO\ConversionEvent;

class LegacyPiwikFormatter implements FormatterInterface
{

    public function prepareFormattedData(ConversionEvent $event)
    {
        $userInformation  = $event->getUserInformation();
        $orderInformation = $event->getOrderInformation();

        $legacyPiwikVariables = [
            'idsite'     => 2,
            'rec'        => 1,
            'apiv'       => 1,
            'r'          => 443213,
            'uid'        => '76703109',
            'token_auth' => 'blah',
            '_idts'      => 1,
            '_idvc'      => 1
        ];
        $customVariables      = [
            'ec_id'    => $orderInformation->getOrderId(),
            'revenue'  => $orderInformation->getPrice(),
            'country'  => $userInformation->getCountry(),
            'cip'      => $userInformation->getIp(),
            '_cvar'    => json_encode([
                '1'  => ['msisdn', $userInformation->getMsisdn()],
                '2'  => ['connection', $userInformation->getConnection()],
                '3'  => ['conversion_mode', false],
                '4'  => ['currency', $orderInformation->getCurrency()],
                '5'  => ['provider', $userInformation->getProvider()],
                '6'  => ['operator', $userInformation->getOperator()],
                '7'  => ['affiliate', $userInformation->getAffiliate()],
                /*'9'  => 'aff_publisher',*/
                '11' => ['device_screen_height', $userInformation->getDeviceHeight()],
                '12' => ['device_screen_width', $userInformation->getDeviceWidth()]
            ]),
            'ec_items' => json_encode([
                $orderInformation->getAlias(),
                $orderInformation->getAlias(),
                $orderInformation->getAction(),
                $orderInformation->getPrice(),
                1
            ]),
        ];

        $queryString = http_build_query(array_merge($legacyPiwikVariables, $customVariables));


        return [
            'piwikData' => [
                sprintf('http://piwik.playwing.com/piwik.php?%s', $queryString),
                TimestampGenerator::generateMicrotime()
            ]
        ];
    }
}