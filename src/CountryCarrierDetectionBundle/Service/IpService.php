<?php
namespace CountryCarrierDetectionBundle\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Service for fetching the IP address of the current request.
 *
 * Class IpService
 * @package CountryCarrierDetectionBundle\Service
 */
class IpService
{

    const SERVER_IP = '127.0.0.1';

    protected $force_ip = null;
    /**
     * The Request Symfony service layer.
     *
     * @var RequestStack
     */
    private $requestStack = null;

    /**
     * IpService constructor.
     *
     * @param RequestStack $container
     */
    public function __construct(RequestStack $container)
    {
        $this->requestStack = $container;
    }

    /**
     * Return the current request IP address.
     *
     * @return string
     */
    public function getIp()
    {
        $session = new Session();
        $this->force_ip = $session->get('user_ip');
        if (!!$this->force_ip){
            return $this->force_ip;
        }

        // if there is "x-real-ip" header
        $request = $this->requestStack->getCurrentRequest() ?? new Request();
        $xRealIp = $request->headers->get('x-real-ip', null);

        if ($xRealIp) {
            return $xRealIp;
        }

        // if there is "x-forwarded-for" header
        $xForwardedfor = $request->headers->get('x-forwarded-for', null);
        if ($xForwardedfor) {
            $xForwardedforList = explode(',', $xForwardedfor);
            return trim($xForwardedforList[0]);
        }

        return $request->getClientIp() ?? self::SERVER_IP;
    }

    /**
     * Seta current IP address.
     */
    public function setIp($ip)
    {
        $session = new Session();
        $session->set('user_ip', $ip);
    }
}