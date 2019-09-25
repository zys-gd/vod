<?php


namespace App\Piwik\DataMapper;


use App\Domain\Entity\Game;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Piwik\DataMapper\UserInformationMapper;
use SubscriptionBundle\Piwik\DTO\ConversionEvent;
use SubscriptionBundle\Piwik\DTO\OrderInformation;

class GameEventMapper
{
    /**
     * @var UserInformationMapper
     */
    private $userInformationMapper;

    public function __construct(UserInformationMapper $userInformationMapper)
    {
        $this->userInformationMapper = $userInformationMapper;
    }


    public function map(Subscription $subscription, Game $game): ConversionEvent
    {

        $subscriptionPack = $subscription->getSubscriptionPack();
        $orderInformation = $this->mapOrderInformation($subscription, $game, $subscriptionPack);
        $userInfo         = $this->userInformationMapper->mapUserInformation(
            $subscription->getUser(),
            $subscription,
            0
        );
        $additionalData   = $this->getAdditionalData($game);

        return new ConversionEvent($userInfo, $orderInformation, $additionalData);
    }

    /**
     * /**
     * @param Game $game
     * @return array
     */
    private function getAdditionalData(Game $game): array
    {
        return [
            13 => ['game_name', $game->getName()],
            14 => ['game_uuid', $game->getUuid()]
        ];
    }

    private function mapOrderInformation(
        Subscription $subscription,
        Game $game,
        \SubscriptionBundle\Entity\SubscriptionPack $subscriptionPack
    ): OrderInformation
    {
        $orderIdPieces = [
            'download-ok',
            $subscription->getUuid(),
            $subscriptionPack->getUuid(),
            $game->getUuid(),
            'N' . rand(1000, 9999),
        ];
        $orderId       = implode('-', $orderIdPieces);
        $alias         = sprintf('download-%s', $game->getUuid());

        $orderInformation = new OrderInformation(
            $orderId,
            0.01,
            $alias,
            'game',
            $subscriptionPack->getTierCurrency()
        );
        return $orderInformation;
    }
}