<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 11.01.19
 * Time: 12:10
 */

namespace IdentificationBundle\Service\Action\Identification\Common;


use Symfony\Component\Routing\RouterInterface;

class WifiRouteGenerator
{
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var string
     */
    private $routeName;


    /**
     * WifiRouteGenerator constructor.
     * @param RouterInterface $router
     * @param string          $routeName
     */
    public function __construct(RouterInterface $router, string $routeName)
    {
        $this->router    = $router;
        $this->routeName = $routeName;
    }

    public function generate(): string
    {
        return $this->router->generate($this->routeName);
    }
}