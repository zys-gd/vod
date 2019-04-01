<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 30.04.18
 * Time: 10:37
 */

namespace SubscriptionBundle\Controller\Actions;


use ExtrasBundle\API\Controller\APIControllerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use SubscriptionBundle\Controller\Traits\ResponseTrait;
use SubscriptionBundle\Service\Callback\Common\CommonFlowHandler;
use SubscriptionBundle\Service\Callback\Impl\CarrierCallbackHandlerProvider;
use SubscriptionBundle\Service\Callback\Impl\HasCustomFlow;

class ListenAction extends Controller implements APIControllerInterface
{
    use ResponseTrait;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var CommonFlowHandler
     */
    private $commonFlowHandler;
    /**
     * @var CarrierCallbackHandlerProvider
     */
    private $carrierCallbackHandlerProvider;


    /**
     * ListenAction constructor.
     *
     * @param CommonFlowHandler              $commonFlowHandler
     * @param LoggerInterface                $logger
     * @param CarrierCallbackHandlerProvider $carrierCallbackHandlerProvider
     */
    public function __construct(
        CommonFlowHandler $commonFlowHandler,
        LoggerInterface $logger,
        CarrierCallbackHandlerProvider $carrierCallbackHandlerProvider
    )
    {
        $this->logger                         = $logger;
        $this->commonFlowHandler              = $commonFlowHandler;
        $this->carrierCallbackHandlerProvider = $carrierCallbackHandlerProvider;
    }

    public function __invoke(Request $request)
    {

        if (!$type = $request->get('type', null)) {
            throw new BadRequestHttpException('`type` parameter is required');
        }

        if (!$carrierId = $request->get('carrier')) {
            throw new BadRequestHttpException('`carrier` parameter should be present in callback response');
        }

        // TODO make security measures to prevent fake requests from billing framework, or request tampering
        // TODO i.e. signature, carrier checking etc. now its unsecure.
        // TODO check for subscription status inside of callback processor
        // TODO check for fully charged.

        try {

            $customCallbackHandler = $this->carrierCallbackHandlerProvider->getHandler($carrierId, $request, $type);

            if ($customCallbackHandler instanceof HasCustomFlow) {
                $result = $customCallbackHandler->process($request, $type);
            } else {
                $result = $this->commonFlowHandler->process($request, $carrierId, $type);
            }


            $response = $this->getSimpleJsonResponse('Updated Subscription ', 200, [
                'identification' => true,
                $type            => true,
                'user'           => true,
                'subscription'   => $result
            ]);

        } catch (\Exception $e) {
            $this->logger->error("Error while handling callback for {$type}", ["exception" => $e]);
            $response = $this->getSimpleJsonResponse($e->getMessage(), 400, [
                'identification' => true,
                'user'           => true,
                'subscription'   => false,
            ]);
        }
        return $response;
    }


}