<?php

namespace PiwikBundle\Service;

use PiwikBundle\Api\ClientAbstract;
use PiwikBundle\Service\DTO\EcommerceDTO;


class PiwikTracker
{
    const TRACK_SUBSCRIBE   = 'trackSubscribe';
    const TRACK_RESUBSCRIBE = 'trackResubscribe';
    const TRACK_RENEW       = 'trackRenew';
    const TRACK_UNSUBSCRIBE = 'trackUnsubscribe';

    /**
     * @var ClientAbstract
     */
    private $piwikClient;

    /**
     * PiwikTracker constructor.
     *
     * @param ClientAbstract $piwikClient
     */
    public function __construct(ClientAbstract $piwikClient)
    {
        $this->piwikClient = $piwikClient;
    }

    /**
     * @return bool
     */
    public function sendPageView()
    {
        $ret = (bool)$this->piwikClient->doTrackPageView('');
        return $ret;
    }

    /**
     * @param EcommerceDTO $ecommerceDTO
     *
     * @return bool
     * @throws \Exception
     */
    public function sendEcommerce(EcommerceDTO $ecommerceDTO)
    {
        $this->piwikClient->addEcommerceItem(
            $ecommerceDTO->getProdSku(),
            $ecommerceDTO->getProdSku(),
            $ecommerceDTO->getProdCat(),
            $ecommerceDTO->getOrderValue(),
            1
        );

        $ret = (bool)$this->piwikClient->doTrackEcommerceOrder(
            $ecommerceDTO->getOrderId(),
            $ecommerceDTO->getOrderValue()
        );
        return $ret;
    }
}