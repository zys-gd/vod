<?php


namespace App\Domain\Service\AffiliateBannedPublisher;


use App\Domain\Entity\Carrier;
use App\Domain\Repository\AffiliateBannedPublisherRepository;
use SubscriptionBundle\Entity\Affiliate\AffiliateInterface;

class AffiliateBannedPublisherChecker
{
    /**
     * @var AffiliateBannedPublisherRepository
     */
    private $affiliateBannedPublisherRepository;

    public function __construct(AffiliateBannedPublisherRepository $affiliateBannedPublisherRepository)
    {
        $this->affiliateBannedPublisherRepository = $affiliateBannedPublisherRepository;
    }

    /**
     * @param array              $query
     * @param AffiliateInterface $affiliate
     * @param Carrier            $carrier
     *
     * @return bool
     */
    public function isPublisherBanned(array $query, AffiliateInterface $affiliate, Carrier $carrier): bool
    {
        foreach ($query as $paramName => $value) {
            if ($paramName == 'pid' || strpos($paramName, 'pub') === 0) {
                $affiliateBannedPublisher = $this->affiliateBannedPublisherRepository->findBannedPublisher($affiliate, $value);
                if ($bannedCarrier = $affiliateBannedPublisher->getCarrier()) {
                    return $bannedCarrier === $carrier;
                }
                return !!$affiliateBannedPublisher;
            }
        }

        return false;
    }
}