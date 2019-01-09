<?php

namespace SubscriptionBundle\Service\SubscriptionText;


use AppBundle\Repository\LanguagesRepository;
use SubscriptionBundle\Entity\SubscriptionPack;
use SubscriptionBundle\Service\SubscriptionText\Periodicity\Custom;
use SubscriptionBundle\Service\SubscriptionText\Periodicity\Daily;
use SubscriptionBundle\Service\SubscriptionText\Periodicity\IPeriodicity;
use SubscriptionBundle\Service\SubscriptionText\Periodicity\Weekly;

class SubscriptionTextProvider
{
    /**
     * @var LanguagesRepository
     */
    private $languagesRepository;
    /**
     * @var QueryManager
     */
    private $queryManager;

    /**
     * SubscriptionTextProvider constructor.
     * @param LanguagesRepository $languagesRepository
     * @param QueryManager $queryManager
     */
    public function __construct(LanguagesRepository $languagesRepository, QueryManager $queryManager)
    {
        $this->languagesRepository = $languagesRepository;
        $this->queryManager = $queryManager;
    }

    /**
     * @param SubscriptionPack $oSubPack
     * @return string
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function provideQuery(SubscriptionPack $oSubPack): string
    {
        $billingCarrierId = $oSubPack->getCarrierId();
        $subPackId = $oSubPack->getUuid();
        $engLangId = $this->languagesRepository->getEnglishLanguageId();
        $oPeriodicity = $this->getSubscriptionPeriodicity($oSubPack);
        $period = $oSubPack->getFinalPeriodForSubscription();
        $credits = $oSubPack->getFinalCreditsForSubscription();
        $query = $this->queryManager->buildQuery($billingCarrierId, $subPackId, $engLangId, $oPeriodicity, $oSubPack->isFirstSubscriptionPeriodIsFree(), $period, $credits);
        return $query;
    }

    /**
     * @param SubscriptionPack $oSubPack
     * @return IPeriodicity
     */
    private function getSubscriptionPeriodicity(SubscriptionPack $oSubPack): IPeriodicity
    {
        switch ($oSubPack->getPeriodicity()) {
            case SubscriptionPack::WEEKLY:
                $oPeriodicity = new Weekly();
                break;
            case SubscriptionPack::DAILY:
                $oPeriodicity = new Daily();
                break;
            case SubscriptionPack::MONTHLY:
            case SubscriptionPack::CUSTOM_PERIODICITY:
            default:
                $oPeriodicity = new Custom();
                break;
        }

        return $oPeriodicity;
    }
}