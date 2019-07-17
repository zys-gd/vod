<?php


namespace App\Domain\Service;



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
     * @param AffiliateInterface $affiliate
     * @param array              $query
     *
     * @return bool
     */
    public function isPublisherBanned(AffiliateInterface $affiliate, array $query): bool
    {
        foreach ($query as $paramName => $value) {
            if($paramName == 'pid' || strpos($paramName, 'pub') === 0) {
                return !!$this->affiliateBannedPublisherRepository->findBannedPublisher($affiliate, $value);
            }
        }

        return false;
    }
}