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
    public function isPublisherBanned(array $query, AffiliateInterface $affiliate, Carrier $carrier = null): bool
    {
        $keys                = array_keys($query);
        $publishersFromQuery = preg_grep('/^pid|^pub.*/', $keys);

        foreach ($publishersFromQuery as $key) {
            $publisherId = $query[$key];

            if ($carrier) {
                $affiliateBannedPublisher = $this->affiliateBannedPublisherRepository->findBannedPublisher4Carrier($affiliate, $publisherId, $carrier);
            }
            else {
                $affiliateBannedPublisher = $this->affiliateBannedPublisherRepository->findTotallyBannedPublisher($affiliate, $publisherId);
            }

            return !!$affiliateBannedPublisher;
        }

        return false;
    }
}