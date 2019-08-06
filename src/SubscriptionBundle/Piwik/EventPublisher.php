<?php

namespace PiwikBundle\Service;

use PiwikBundle\Api\ClientAbstract;
use PiwikBundle\Service\DTO\OrderInformation;
use SubscriptionBundle\Piwik\DTO\ConversionEvent;
use SubscriptionBundle\Piwik\DTO\UserInformation;


class EventPublisher
{
    const TRACK_SUBSCRIBE = 'trackSubscribe';
    const TRACK_RESUBSCRIBE = 'trackResubscribe';
    const TRACK_UNSUBSCRIBE = 'trackUnsubscribe';

    /**
     * @var ClientAbstract
     */
    private $piwikClient;

    /**
     * EventPublisher constructor.
     *
     * @param ClientAbstract $piwikClient
     */
    public function __construct(ClientAbstract $piwikClient)
    {
        $this->piwikClient = $piwikClient;
    }


    /**
     * @param OrderInformation $orderInformation
     *
     * @return bool
     * @throws \Exception
     */
    public function sendEcommerceEvent(ConversionEvent $conversionEvent)
    {

        $legacyPiwikVariables = [
            'idsite'     => 1,
            'rec'        => 1,
            'apiv'       => 1,
            'r'          => 443213,
            'cip'        => '127.0.0.1',
            'uid'        => '76703109',
            'token_auth' => 'blah',
            '_idts'      => 1,
            '_idvc'      => 1
        ];

        $ecommerceVariables = ['ec_items' => [
            $orderInformation->getProdSku(),
            $orderInformation->getProdSku(),
            $orderInformation->getProdCat(),
            $orderInformation->getOrderValue(),
            1
        ]];


        $this->piwikClient->addEcommerceItem(
            $orderInformation->getProdSku(),
            $orderInformation->getProdSku(),
            $orderInformation->getProdCat(),
            $orderInformation->getOrderValue(),
            1
        );

        $ret = (bool)$this->piwikClient->doTrackEcommerceOrder(
            $orderInformation->getOrderId(),
            $orderInformation->getOrderValue()
        );
        return $ret;
    }
}