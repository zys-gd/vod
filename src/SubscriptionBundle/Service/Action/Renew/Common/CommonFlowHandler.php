<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 06.03.19
 * Time: 11:54
 */

namespace SubscriptionBundle\Service\Action\Renew\Common;


use IdentificationBundle\Entity\CarrierInterface;
use SubscriptionBundle\Repository\SubscriptionRepository;
use SubscriptionBundle\Service\Action\MassRenew\MassRenewer;
use SubscriptionBundle\Service\Action\Renew\DTO\MassRenewResult;

class CommonFlowHandler
{
    /**
     * @var MassRenewer
     */
    private $massRenewer;
    /**
     * @var SubscriptionRepository
     */
    private $repository;


    /**
     * CommonFlowHandler constructor.
     * @param MassRenewer            $massRenewer
     * @param SubscriptionRepository $repository
     */
    public function __construct(MassRenewer $massRenewer, SubscriptionRepository $repository)
    {
        $this->massRenewer = $massRenewer;
        $this->repository  = $repository;
    }

    public function process(CarrierInterface $carrier)
    {


        $subscriptions = $this->repository->getExpiredSubscriptions($carrier);

        if (count($subscriptions)) {
            return $this->massRenewer->massRenew($subscriptions, $carrier);
        } else {
            return new MassRenewResult(0, 0, 0, null);
        }
    }

}