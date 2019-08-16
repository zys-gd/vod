<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 30.04.18
 * Time: 11:48
 */

namespace SubscriptionBundle\Controller\Actions;


use IdentificationBundle\Identification\DTO\ISPData;
use IdentificationBundle\Repository\CarrierRepositoryInterface;
use SubscriptionBundle\Service\Action\SubscribeBack\Common\CommonFlowHandler;
use SubscriptionBundle\Service\Action\SubscribeBack\SubscribeBackHandlerProvider;
use Symfony\Component\HttpFoundation\Request;

class SubscribeBackAction
{
    /**
     * @var SubscribeBackHandlerProvider
     */
    private $subscribeBackHandlerProvider;
    /**
     * @var CarrierRepositoryInterface
     */
    private $carrierRepository;
    /**
     * @var CommonFlowHandler
     */
    private $commonFlowHandler;

    /**
     * SubscribeBackAction constructor.
     *
     * @param SubscribeBackHandlerProvider $subscribeBackHandlerProvider
     * @param CarrierRepositoryInterface   $carrierRepository
     * @param CommonFlowHandler            $commonFlowHandler
     */
    public function __construct(
        SubscribeBackHandlerProvider $subscribeBackHandlerProvider,
        CarrierRepositoryInterface $carrierRepository,
        CommonFlowHandler $commonFlowHandler)
    {
        $this->subscribeBackHandlerProvider = $subscribeBackHandlerProvider;
        $this->carrierRepository            = $carrierRepository;
        $this->commonFlowHandler            = $commonFlowHandler;
    }

    /**
     * @param Request $request
     * @param ISPData $ispData
     *
     * @return mixed
     */
    public function __invoke(Request $request, ISPData $ispData)
    {
        $carrierId = $ispData->getCarrierId();
        $carrier   = $this->carrierRepository->findOneByBillingId($carrierId);
        $handler   = $this->subscribeBackHandlerProvider->getHandler($carrier);

        return $this->commonFlowHandler->process($request, $carrier, $handler);
    }


}