<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 02.09.19
 * Time: 14:06
 */

namespace SubscriptionBundle\Subscription\Subscribe\ProcessStarter;


use CommonDataBundle\Entity\Interfaces\CarrierInterface;

class SubscribeProcessStarterProvider
{
    /**
     * @var CommonStarter
     */
    private $defaultStarter;


    /**
     * @var SubscribeProcessStarterInterface[]
     */
    private $starters = [];


    /**
     * SubscribeProcessStarterProvider constructor.
     * @param CommonStarter $defaultStarter
     */
    public function __construct(CommonStarter $defaultStarter)
    {
        $this->defaultStarter = $defaultStarter;
    }

    public function get(CarrierInterface $carrier): SubscribeProcessStarterInterface
    {
        foreach ($this->starters as $starter) {
            if ($starter->isSupports($carrier)) {
                return $starter;
            }
        }

        return $this->defaultStarter;
    }

    public function addHandler(SubscribeProcessStarterInterface $subscribeProcessStarter): void
    {
        $this->starters[] = $subscribeProcessStarter;
    }
}