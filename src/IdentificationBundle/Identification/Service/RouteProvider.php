<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 11.01.19
 * Time: 12:10
 */

namespace IdentificationBundle\Identification\Service;


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
    private $wifiPageRoute;
    /**
     * @var string
     */
    private $homepageRoute;


    /**
     * RouteProvider constructor.
     * @param RouterInterface $router
     * @param string          $wifiPageRoute
     * @param string          $homepageRoute
     */
    public function __construct(RouterInterface $router, string $wifiPageRoute, string $homepageRoute)
    {
        $this->router        = $router;
        $this->wifiPageRoute = $wifiPageRoute;
        $this->homepageRoute = $homepageRoute;
    }

    public function getLinkToWifiFlowPage(): string
    {
        return $this->router->generate($this->wifiPageRoute);
    }

    public function getLinkToHomepage(): string
    {
        return $this->router->generate($this->homepageRoute, [], RouterInterface::ABSOLUTE_URL);
    }

    public function getAbsoluteUrlToHomepage(): string
    {
        return $this->router->generate($this->homepageRoute, [], RouterInterface::ABSOLUTE_URL);
    }
}