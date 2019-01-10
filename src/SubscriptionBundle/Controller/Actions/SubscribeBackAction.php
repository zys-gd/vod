<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 30.04.18
 * Time: 11:48
 */

namespace SubscriptionBundle\Controller\Actions;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use SubscriptionBundle\Service\Action\SubscribeBack\SubscribeBackHandlerProvider;

class SubscribeBackAction
{
    /**
     * @var SubscribeBackHandlerProvider
     */
    private $subscribeBackHandlerProvider;
    /**
     * @var RouterInterface
     */
    private $router;


    /**
     * SubscribeBackAction constructor.
     * @param SubscribeBackHandlerProvider $subscribeBackHandlerProvider
     */
    public function __construct(SubscribeBackHandlerProvider $subscribeBackHandlerProvider, RouterInterface $router)
    {
        $this->subscribeBackHandlerProvider = $subscribeBackHandlerProvider;
        $this->router                       = $router;
    }

    /**
     * Unusual action, when we are receiving user already subscribed on provider side.
     * @param Request $request
     * @return Response
     */
    public function __invoke(Request $request)
    {
        $handler = $this->subscribeBackHandlerProvider->getHandler($request);

        return $handler->handleRequest($request);
    }


}