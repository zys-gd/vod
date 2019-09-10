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
     * @var string
     */
    private $wrongCarrierRoute;
    /**
     * @var string
     */
    private $contactUsRoute;

    /**
     * RouteProvider constructor.
     *
     * @param RouterInterface $router
     * @param string          $wifiPageRoute
     * @param string          $homepageRoute
     * @param string          $landingRoute
     * @param string          $myAccountRoute
     * @param string          $wrongCarrierRoute
     * @param string          $contactUsRoute
     */
    public function __construct(
        RouterInterface $router,
        string $wifiPageRoute,
        string $homepageRoute,
        string $landingRoute,
        string $myAccountRoute,
        string $wrongCarrierRoute,
        string $contactUsRoute
    ) {
        $this->router            = $router;
        $this->wifiPageRoute     = $wifiPageRoute;
        $this->homepageRoute     = $homepageRoute;
        $this->landingRoute      = $landingRoute;
        $this->myAccountRoute    = $myAccountRoute;
        $this->wrongCarrierRoute = $wrongCarrierRoute;
        $this->contactUsRoute    = $contactUsRoute;
    }

    public function getLinkToWifiFlowPage(): string
    {
        return $this->router->generate($this->wifiPageRoute, [], RouterInterface::ABSOLUTE_URL);
    }

    public function getLinkToHomepage(array $parameters = []): string
    {
        return $this->router->generate($this->homepageRoute, $parameters, RouterInterface::ABSOLUTE_URL);
    }

    public function getShortLinkToHomepage(array $parameters = []): string
    {
        return $this->router->generate($this->homepageRoute, $parameters, RouterInterface::NETWORK_PATH);
    }

    public function getLinkToLanding()
    {
        return $this->router->generate($this->landingRoute, [], RouterInterface::ABSOLUTE_URL);
    }

    public function getLinkToMyAccount()
    {
        return $this->router->generate($this->myAccountRoute, [], RouterInterface::ABSOLUTE_URL);
    }

    public function getLinkToWrongCarrierPage()
    {
        return $this->router->generate($this->wrongCarrierRoute, [], RouterInterface::ABSOLUTE_URL);
    }

    public function getContactUsLink()
    {
        return $this->router->generate($this->contactUsRoute, [], RouterInterface::ABSOLUTE_URL);
    }


}