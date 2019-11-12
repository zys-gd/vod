<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 05.11.19
 * Time: 16:27
 */

namespace SubscriptionBundle\Subscription\Unsubscribe\Controller\Event;


use SubscriptionBundle\Piwik\DataMapper\UnsubscribeClickEventMapper;
use SubscriptionBundle\Piwik\DataMapper\UserInformationMapper;
use SubscriptionBundle\Piwik\EventPublisher;
use Symfony\Component\HttpFoundation\Request;

class UnsubscribeClickEventTracker
{
    /**
     * @var UserInformationMapper
     */
    private $userInformationMapper;
    /**
     * @var UnsubscribeClickEventMapper
     */
    private $clickEventMapper;
    /**
     * @var EventPublisher
     */
    private $eventPublisher;


    /**
     * SubscribeClickEventTracker constructor.
     * @param UserInformationMapper       $userInformationMapper
     * @param UnsubscribeClickEventMapper $clickEventMapper
     * @param EventPublisher              $eventPublisher
     */
    public function __construct(
        UserInformationMapper $userInformationMapper,
        UnsubscribeClickEventMapper $clickEventMapper,
        EventPublisher $eventPublisher
    )
    {
        $this->userInformationMapper = $userInformationMapper;
        $this->clickEventMapper      = $clickEventMapper;
        $this->eventPublisher        = $eventPublisher;
    }

    public function trackEvent(Request $request): void
    {
        $userInfo = $this->userInformationMapper->mapFromRequest($request);

        $event = $this->clickEventMapper->map($userInfo);

        $this->eventPublisher->publish($event);
    }

}