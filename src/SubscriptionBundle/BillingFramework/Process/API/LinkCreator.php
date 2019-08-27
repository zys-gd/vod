<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 02.05.18
 * Time: 10:17
 */

namespace SubscriptionBundle\BillingFramework\Process\API;


use ExtrasBundle\Utils\UrlParamAppender;
use SubscriptionBundle\BillingFramework\BillingOptionsProvider;

class LinkCreator
{
    /**
     * @var UrlParamAppender
     */
    private $urlParamAppender;
    /**
     * @var BillingOptionsProvider
     */
    private $billingOptionsProvider;

    /**
     * BillingFrameworkLinkCreator constructor.
     * @param BillingOptionsProvider $billingOptionsProvider
     * @param UrlParamAppender       $urlParamAppender
     */
    public function __construct(BillingOptionsProvider $billingOptionsProvider, UrlParamAppender $urlParamAppender)
    {
        $this->urlParamAppender       = $urlParamAppender;
        $this->billingOptionsProvider = $billingOptionsProvider;
    }

    public function createProcessLink(string $method, array $params = []): string
    {
        $url = sprintf('%s/process/%s', $this->billingOptionsProvider->getApiHost(), $method);

        return $this->urlParamAppender->appendUrl($url, $params);
    }

    public function createDataLink(string $method, string $id = null): string
    {
        if ($id) {
            return sprintf('%s/data/%s/%s', $this->billingOptionsProvider->getApiHost(), $method, $id);
        } else {
            return sprintf('%s/data/%s', $this->billingOptionsProvider->getApiHost(), $method);

        }
    }

    public function createCustomLink(string $path): string
    {
        return sprintf('%s/%s', $this->billingOptionsProvider->getApiHost(), $path);
    }

}