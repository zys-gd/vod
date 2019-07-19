<?php


namespace App\API\Controller;


use App\Domain\Repository\AffiliateBannedPublisherRepository;
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
     * AffiliateBannedPublishersController constructor.
     *
     * @param AffiliateBannedPublisherRepository $affiliateBannedPublisherRepository
     */
    public function __construct(AffiliateBannedPublisherRepository $affiliateBannedPublisherRepository)
    {
        $this->affiliateBannedPublisherRepository = $affiliateBannedPublisherRepository;
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
        } catch (AffiliateIdNotFoundException $exception) {
            return new JsonResponse([
                'result'  => true,
                'isExist' => false
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
        } catch (AffiliateIdNotFoundException $exception) {
            return new JsonResponse([
                'result'  => true,
                'isExist' => false
            ]);
        } catch (PublisherIdNotFoundException $exception) {
            return new JsonResponse([
                'result'  => true,
                'isExist' => false
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
            $this->affiliateBannedPublisherRepository->banPublisher($affiliateId, $publisherId);
            return new JsonResponse([
                'result'   => true,
                'isBanned' => true
            ]);
        } catch (AffiliateIdNotFoundException $exception) {
            return new JsonResponse([
                'result'  => true,
                'isBanned' => false
            ]);
        } catch (PublisherIdNotFoundException $exception) {
            return new JsonResponse([
                'result'  => true,
                'isBanned' => false
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

        $this->affiliateBannedPublisherRepository->unbanPublisher($affiliateId, $publisherId);

        try {
            return new JsonResponse([
                'result'   => true,
                'removed' => true
            ]);
        } catch (AffiliateIdNotFoundException $exception) {
            return new JsonResponse([
                'result'  => true,
                'removed' => false
            ]);
        } catch (PublisherIdNotFoundException $exception) {
            return new JsonResponse([
                'result'  => true,
                'removed' => false
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