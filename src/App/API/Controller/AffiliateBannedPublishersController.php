<?php


namespace App\API\Controller;


use App\Domain\Entity\Affiliate;
use App\Domain\Entity\AffiliateBannedPublisher;
use App\Domain\Repository\AffiliateBannedPublisherRepository;
use App\Domain\Repository\AffiliateRepository;
use App\Domain\Service\AffiliateBannedPublisher\AffiliateBannedPublisherCreator;
use App\Domain\Service\AffiliateBannedPublisher\AffiliateBannedPublisherRemover;
use App\Exception\AffiliateIdNotFoundException;
use App\Exception\PublisherIdNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/abp")
 */
class AffiliateBannedPublishersController extends AbstractController
{
    /**
     * @var AffiliateBannedPublisherRepository
     */
    private $affiliateBannedPublisherRepository;
    /**
     * @var AffiliateRepository
     */
    private $affiliateRepository;
    /**
     * @var AffiliateBannedPublisherCreator
     */
    private $affiliateBannedPublisherCreator;
    /**
     * @var AffiliateBannedPublisherRemover
     */
    private $affiliateBannedPublisherRemover;

    /**
     * AffiliateBannedPublishersController constructor.
     *
     * @param AffiliateBannedPublisherRepository $affiliateBannedPublisherRepository
     * @param AffiliateRepository                $affiliateRepository
     * @param AffiliateBannedPublisherCreator    $affiliateBannedPublisherCreator
     * @param AffiliateBannedPublisherRemover    $affiliateBannedPublisherRemover
     */
    public function __construct(AffiliateBannedPublisherRepository $affiliateBannedPublisherRepository,
        AffiliateRepository $affiliateRepository,
        AffiliateBannedPublisherCreator $affiliateBannedPublisherCreator,
        AffiliateBannedPublisherRemover $affiliateBannedPublisherRemover)
    {
        $this->affiliateBannedPublisherRepository = $affiliateBannedPublisherRepository;
        $this->affiliateRepository                = $affiliateRepository;
        $this->affiliateBannedPublisherCreator    = $affiliateBannedPublisherCreator;
        $this->affiliateBannedPublisherRemover    = $affiliateBannedPublisherRemover;
    }

    /**
     * @Route("/{affiliateId}", methods={"GET"})
     * @param string $affiliateId
     *
     * @return JsonResponse
     */
    public function affiliateBannedPublishersAction(string $affiliateId): JsonResponse
    {
        try {
            $affiliateBunnetPublishers = $this->affiliateBannedPublisherRepository->findBannedPublishersAsArray($affiliateId);
            return new JsonResponse([
                'result'                    => true,
                'affiliateBunnetPublishers' => $affiliateBunnetPublishers
            ]);
        } catch (\Throwable $exception) {
            return $this->createResponseForException($exception);
        }
    }

    /**
     * @Route("/{affiliateId}/{publisherId}", methods={"GET"})
     * @param string $affiliateId
     * @param string $publisherId
     *
     * @return JsonResponse
     */
    public function affiliateBannedPublisherAction(string $affiliateId, string $publisherId): JsonResponse
    {
        try {
            $affiliateBunnetPublisher = $this->affiliateBannedPublisherRepository->findOneBy([
                'affiliate'   => $affiliateId,
                'publisherId' => $publisherId
            ]);
            return new JsonResponse([
                'result'   => true,
                'isBanned' => !!$affiliateBunnetPublisher
            ]);
        } catch (\Throwable $exception) {
            return $this->createResponseForException($exception);
        }
    }

    /**
     * @Route("/{affiliateId}/{publisherId}", methods={"PUT"})
     * @param string $affiliateId
     * @param string $publisherId
     *
     * @return JsonResponse
     */
    public function banAction(string $affiliateId, string $publisherId): JsonResponse
    {
        try {
            /** @var Affiliate $affiliate */
            $affiliate = $this->affiliateRepository->find($affiliateId);
            $this->affiliateBannedPublisherCreator->banPublisher($affiliate, $publisherId);
            return new JsonResponse([
                'result'   => true,
                'isBanned' => true
            ]);
        } catch (\Throwable $exception) {
            return $this->createResponseForException($exception);
        }
    }

    /**
     * @Route("/{affiliateId}/{publisherId}", methods={"DELETE"})
     * @param string $affiliateId
     * @param string $publisherId
     *
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function unbanAction(string $affiliateId, string $publisherId): JsonResponse
    {
        try {
            /** @var Affiliate $affiliate */
            $affiliate = $this->affiliateRepository->find($affiliateId);
            /** @var AffiliateBannedPublisher $affiliateBannedPublisher */
            $affiliateBannedPublisher = $this->affiliateBannedPublisherRepository->findOneBy([
                'affiliate'   => $affiliate,
                'publisherId' => $publisherId
            ]);
            $this->affiliateBannedPublisherRemover->unbanPublisher($affiliateBannedPublisher);

            return new JsonResponse([
                'result'  => true,
                'removed' => true
            ]);
        } catch (\Throwable $exception) {
            return $this->createResponseForException($exception);
        }
    }

    /**
     * @param \Exception $e
     *
     * @return JsonResponse
     */
    private function createResponseForException(\Exception $e): JsonResponse
    {
        $code = $e instanceof HttpException
            ? $e->getStatusCode()
            : $e->getCode();


        return new JsonResponse([
            'result'  => false,
            'code'    => $code,
            'message' => $e->getMessage()
        ]);
    }
}