<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 24.12.18
 * Time: 17:06
 */

namespace App\Domain\Service\VideoProcessing\Callback;


use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Domain\Entity\UploadedVideo;
use App\Domain\Repository\UploadedVideoRepository;

class EagerType implements CallbackHandlerInterface
{
    /**
     * @var UploadedVideoRepository
     */
    private $repository;
    /**
     * @var EntityManager
     */
    private $entityManager;


    /**
     * EagerType constructor.
     * @param UploadedVideoRepository $repository
     * @param EntityManager           $entityManager
     */
    public function __construct(UploadedVideoRepository $repository, EntityManager $entityManager)
    {
        $this->repository    = $repository;
        $this->entityManager = $entityManager;
    }

    public function isSupports(Request $request): bool
    {
        return $request->get('notification_type') == 'eager';
    }

    public function handle(Request $request)
    {
        $publicId = $request->get('public_id', null);

        if (!$publicId) {
            throw new BadRequestHttpException(sprintf('Bad Request - missing parameters'));
        }

        /** @var UploadedVideo $video */
        $video = $this->repository->findOneBy([
            'remoteId' => $publicId
        ]);

        if (!$video) {
            throw new NotFoundHttpException(sprintf('%s not found', $publicId));
        }

        $video->updateStatus(UploadedVideo::STATUS_TRANSFORMATION_READY);

        $this->entityManager->flush();
    }
}