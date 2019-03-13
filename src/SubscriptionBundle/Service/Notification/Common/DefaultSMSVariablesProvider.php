<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 05.03.19
 * Time: 17:39
 */

namespace SubscriptionBundle\Service\Notification\Common;


use IdentificationBundle\Entity\User;
use IdentificationBundle\Identification\Service\RouteProvider;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Entity\SubscriptionPack;
use SubscriptionBundle\Service\RenewDateCalculator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class DefaultSMSVariablesProvider
{
    /**
     * @var RouterInterface
     */
    private $router;
    private $renewDateCalculator;
    /**
     * @var RouteProvider
     */
    private $provider;


    /**
     * DefaultSMSVariablesProvider constructor.
     * @param RouterInterface     $router
     * @param RenewDateCalculator $renewDateCalculator
     */
    public function __construct(RouterInterface $router, RenewDateCalculator $renewDateCalculator, RouteProvider $provider)
    {
        $this->router              = $router;
        $this->renewDateCalculator = $renewDateCalculator;

        $this->provider = $provider;
    }

    public function getDefaultSMSVariables(SubscriptionPack $pack, Subscription $subscription, User $user): array
    {

        $renewDate = $this->renewDateCalculator->calculateRenewDate($subscription);

        $url = $this->router->generate(
            'identify_by_url',
            ['urlId' => $user->getUrlId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return [
            '_price_'         => $pack->getTierPrice(),
            '_currency_'      => $pack->getTierCurrency(),
            '_home_url_'      => $this->provider->getLinkToHomepage(),
            '_unsub_url_'     => $this->provider->getLinkToMyAccount(),
            '_renew_date_'    => $renewDate->format('d-m-Y'),
            '_autologin_url_' => $url
        ];

    }
}