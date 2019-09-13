<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 05.03.19
 * Time: 17:39
 */

namespace SubscriptionBundle\Subscription\Notification\Common;


use IdentificationBundle\Entity\User;
use IdentificationBundle\Identification\Service\RouteProvider;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Entity\SubscriptionPack;
use SubscriptionBundle\Subscription\Renew\Service\RenewDateCalculator;
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
     *
     * @param RouterInterface     $router
     * @param RenewDateCalculator $renewDateCalculator
     */
    public function __construct(
        RouterInterface $router,
        RenewDateCalculator $renewDateCalculator,
        RouteProvider $provider
    )
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
            ['urlId' => $user->getShortUrlId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $shortUrl = $this->router->generate(
            'identify_by_url',
            ['urlId' => $user->getShortUrlId()],
            UrlGeneratorInterface::NETWORK_PATH
        );

        return [
            '_price_'              => $pack->getTierPrice(),
            '_intprice_'          => intval($pack->getTierPrice()),
            '_currency_'           => $pack->getTierCurrency(),
            '_home_url_'           => $this->provider->getLinkToHomepage(),
            '_shorthome_url_'      => preg_replace('|\/\/|', '', $this->provider->getShortLinkToHomepage()),
            '_unsub_url_'          => $this->provider->getLinkToMyAccount(),
            '_renew_date_'         => $renewDate->format('d-m-Y'),
            '_autologin_url_'      => $url,
            '_shortautologin_url_' => preg_replace('|\/\/|', '', $shortUrl),
            '_contact_us_url_'     => $this->provider->getContactUsLink()
        ];

    }
}