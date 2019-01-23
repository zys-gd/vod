<?php
/**
 * Date: 9/14/2016
 * Time: 18:01
 * @copyright (c) Zitec COM
 * @author George Calcea <george.calcea@zitec.com>
 */

namespace IdentificationBundle\Identification\Exception;


/**
 * Exception is used when is sent a valid token but the token has not a request associated
 * Class RequestNotFoundException
 * @package IdentificationBundle\Exception
 */
class RedirectRequiredException extends \Exception
{

    /**
     * Default exception code
     */
    const EXCEPTION_CODE = 777;

    /**
     * Default exception message
     */
    const EXCEPTION_MESSAGE = "You need to be redirected";
    
    protected $redirectionUrl = '/';

    /**
     * RequestNotFoundException constructor.
     * @param string $message
     * @param int $code
     * @param \Exception|null $previous
     */
    public function __construct($redirectionUrl, $message = null, $code = 0, \Exception $previous = null)
    {
        $this->redirectionUrl = $redirectionUrl;
        $code = empty($code) ? self::EXCEPTION_CODE : $code;
        $message = empty($message) ? self::EXCEPTION_MESSAGE : $message;

        parent::__construct($message, $code, $previous);
    }

    public function getRedirectUrl()
    {
        return $this->redirectionUrl;
    }

}