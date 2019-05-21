<?php

namespace PiwikBundle\Service;

use PiwikBundle\Api\PhpClient;

/**
 * Class PiwikDataSender
 */
class PiwikDataSender
{
    /**
     * @var PiwikClientFactory
     */
    private $piwikClientFactory;

    public function __construct(PiwikClientFactory $piwikClientFactory)
    {
        $this->piwikClientFactory = $piwikClientFactory;
    }

    /**
     * @param $orderId
     * @param $orderValue
     * @param $prodSku
     * @param $prodCat
     *
     * @return bool
     * @throws \Exception
     */
    public function sendEcommerce($orderId, $orderValue, $prodSku, $prodCat)
    {
        $this->piwikClientFactory->getPiwikClient()->addEcommerceItem(
            $prodSku,
            $prodSku,
            $prodCat,
            $orderValue,
            1
        );

        $result = (bool)$this->piwikClientFactory->getPiwikClient()->doTrackEcommerceOrder(
            $orderId,
            $orderValue
        );
        return $result;
    }

    public function sendVisitData()
    {
        $this->piwikClientFactory->getPiwikClient()->doTrackPageView('');
    }
}