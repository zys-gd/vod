<?php
/**
 * Created by PhpStorm.
 * User: Yurii Z
 * Date: 23-01-19
 * Time: 11:20
 */

namespace ExtrasBundle\Utils;


use Symfony\Component\HttpFoundation\RequestStack;

class LocalExtractor
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function getLocal()
    {
        $languageCode = $this->requestStack->getCurrentRequest()->server->get('HTTP_ACCEPT_LANGUAGE', 'en');
        return substr($languageCode, 0, 2);
    }
}