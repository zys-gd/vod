<?php

namespace SubscriptionBundle\Service;

use AppBundle\Repository\LanguagesRepository;
use AppBundle\Repository\PlaceholderToOperatorRepository;
use SubscriptionBundle\Entity\SubscriptionPack;
use SubscriptionBundle\Service\SubscriptionText\SubscriptionTextProvider;

class SubscriptionTextService
{
    protected $placeholderToOperatorRepository = null;
    /**
     * @var SubscriptionTextProvider
     */
    private $subscriptionTextProvider;

    /**
     * SubscriptionTextService constructor.
     * @param PlaceholderToOperatorRepository $placeholderToOperatorRepository
     * @param SubscriptionTextProvider $subscriptionTextProvider
     */
    public function __construct(
        PlaceholderToOperatorRepository $placeholderToOperatorRepository,
        SubscriptionTextProvider $subscriptionTextProvider
    )
    {
        $this->placeholderToOperatorRepository = $placeholderToOperatorRepository;
        $this->subscriptionTextProvider = $subscriptionTextProvider;
    }

    /**
     * @param SubscriptionPack $oSubPack
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function insertDefaultPlaceholderTexts(SubscriptionPack $oSubPack)
    {
        $sql = $this->subscriptionTextProvider->provideQuery($oSubPack);
        $this->placeholderToOperatorRepository->insertTexts($sql);
    }
}