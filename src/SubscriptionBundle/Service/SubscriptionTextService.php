<?php

namespace SubscriptionBundle\Service;

use App\Domain\Repository\TranslationRepository;
use SubscriptionBundle\Entity\SubscriptionPack;
use SubscriptionBundle\Service\SubscriptionText\SubscriptionTextProvider;

class SubscriptionTextService
{
    /**
     * @var SubscriptionTextProvider
     */
    private $subscriptionTextProvider;
    /**
     * @var TranslationRepository
     */
    private $translationRepository;

    /**
     * SubscriptionTextService constructor.
     * @param TranslationRepository $translationRepository
     * @param SubscriptionTextProvider $subscriptionTextProvider
     */
    public function __construct(
        TranslationRepository $translationRepository,
        SubscriptionTextProvider $subscriptionTextProvider
    )
    {
        $this->subscriptionTextProvider = $subscriptionTextProvider;
        $this->translationRepository = $translationRepository;
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