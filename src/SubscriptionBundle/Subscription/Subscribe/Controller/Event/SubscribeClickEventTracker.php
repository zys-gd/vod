<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 05.11.19
 * Time: 16:27
 */

namespace SubscriptionBundle\Subscription\Subscribe\Controller\Event;


use SubscriptionBundle\Piwik\DataMapper\UnsubscribeClickEventMapper;
use SubscriptionBundle\Piwik\DataMapper\UserInformationMapper;
use SubscriptionBundle\Piwik\EventPublisher;
use Symfony\Component\HttpFoundation\Request;

class SubscribeClickEventTracker
{
    /**
     * @var UserInformationMapper
     */
    private $userInformationMapper;
    /**
     * @var UnsubscribeClickEventMapper
     */
    private $subscribeClickEventMapper;
    /**
     * @var EventPublisher
     */
    private $eventPublisher;


    /**
     * SubscribeClickEventTracker constructor.
     * @param UserInformationMapper       $userInformationMapper
     * @param UnsubscribeClickEventMapper $subscribeClickEventMapper
     * @param EventPublisher              $eventPublisher
     */
    public function __construct(
        UserInformationMapper $userInformationMapper,
        UnsubscribeClickEventMapper $subscribeClickEventMapper,
        EventPublisher $eventPublisher
    )
    {
        $this->userInformationMapper     = $userInformationMapper;
        $this->subscribeClickEventMapper = $subscribeClickEventMapper;
        $this->eventPublisher            = $eventPublisher;
    }

    public function trackEvent(Request $request): void
    {
        $userInfo = $this->userInformationMapper->mapFromRequest($request);

        $event = $this->subscribeClickEventMapper->map($userInfo);

        $this->eventPublisher->publish($event);
    }

}