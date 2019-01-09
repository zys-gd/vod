<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 02.05.18
 * Time: 10:17
 */

namespace IdentificationBundle\BillingFramework\Process\API;



use IdentificationBundle\Utils\UrlParamAppender;

class LinkCreator
{

    private $billingFrameworkEndpoint;
    /**
     * @var UrlParamAppender
     */
    private $urlParamAppender;

    /**
     * BillingFrameworkLinkCreator constructor.
     * @param                  $billingFrameworkEndpoint
     * @param UrlParamAppender $urlParamAppender
     */
    public function __construct(string $billingFrameworkEndpoint, UrlParamAppender $urlParamAppender)
    {
        $this->billingFrameworkEndpoint = $billingFrameworkEndpoint;
        $this->urlParamAppender         = $urlParamAppender;
    }

    public function createProcessLink(string $method, array $params = []): string
    {
        $url = sprintf('%s/process/%s', $this->billingFrameworkEndpoint, $method);

        return $this->urlParamAppender->appendUrl($url, $params);
    }

    public function createDataLink(string $method): string
    {
        return sprintf('%s/data/%s', $this->billingFrameworkEndpoint, $method);
    }

    public function createCustomLink(string $path): string
    {
        return sprintf('%s/%s', $this->billingFrameworkEndpoint, $path);
    }

}