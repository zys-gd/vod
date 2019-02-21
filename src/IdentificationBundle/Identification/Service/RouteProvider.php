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
     * @var string
     */
    private $landingRoute;
    /**
     * @var string
     */
    private $myAccountRoute;


    /**
     * RouteProvider constructor.
     * @param RouterInterface $router
     * @param string          $wifiPageRoute
     * @param string          $homepageRoute
     * @param string          $landingRoute
     */
    public function __construct(RouterInterface $router, string $wifiPageRoute, string $homepageRoute, string $landingRoute, string $myAccountRoute)
    {
        $this->router         = $router;
        $this->wifiPageRoute  = $wifiPageRoute;
        $this->homepageRoute  = $homepageRoute;
        $this->landingRoute   = $landingRoute;
        $this->myAccountRoute = $myAccountRoute;
    }

    public function getLinkToWifiFlowPage(): string
    {
        return $this->router->generate($this->wifiPageRoute, [], RouterInterface::ABSOLUTE_URL);
    }

    public function getLinkToHomepage(array $parameters = []): string
    {
        return $this->router->generate($this->homepageRoute, $parameters, RouterInterface::ABSOLUTE_URL);
    }

    public function getLinkToLanding()
    {
        return $this->router->generate($this->landingRoute, [], RouterInterface::ABSOLUTE_URL);
    }

    public function getLinkToMyAccount()
    {
        return $this->router->generate($this->landingRoute, [], RouterInterface::ABSOLUTE_URL);
    }

}