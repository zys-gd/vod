<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 25.07.19
 * Time: 15:01
 */

namespace SubscriptionBundle\Subscription\Common;


use Symfony\Component\Routing\RouterInterface;

class RouteProvider
{
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var string
     */
    private $resubNotAllowedRoute;
    /**
     * @var string
     */
    private $actionIsNotAllowedUrl;
    /**
     * @var string
     */
    private $callbackHost;


    /**
     * RouteProvider constructor.
     * @param RouterInterface $router
     * @param string          $resubNotAllowedRoute
     * @param string          $actionIsNotAllowedUrl
     * @param string          $callbackHost
     */
    public function __construct(RouterInterface $router, string $resubNotAllowedRoute, string $actionIsNotAllowedUrl, string $callbackHost)
    {
        $this->router                = $router;
        $this->resubNotAllowedRoute  = $resubNotAllowedRoute;
        $this->actionIsNotAllowedUrl = $actionIsNotAllowedUrl;
        $this->callbackHost          = $callbackHost;
    }

    public function getResubNotAllowedRoute(): string
    {
        return $this->router->generate($this->resubNotAllowedRoute);
    }

    public function getActionIsNotAllowedUrl(): string
    {
        return $this->actionIsNotAllowedUrl;
    }

    public function getAbsoluteLinkForCallback(string $route): string
    {
        return sprintf('http://%s%s', $this->callbackHost, $this->router->generate($route));
    }

}