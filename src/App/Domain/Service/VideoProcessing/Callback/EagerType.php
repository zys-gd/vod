<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 24.12.18
 * Time: 17:06
 */

namespace App\Domain\Service\VideoProcessing\Callback;


use App\Domain\Entity\UploadedVideo;
use App\Domain\Repository\UploadedVideoRepository;
use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
     * Array of options which should save to "options" field
     *
     * @var array
     */
    private $optionsTobeSaved = [
        'so' => 'start_offset'
    ];

    /**
     * EagerType constructor.
     * @param UploadedVideoRepository $repository
     * @param EntityManager $entityManager
     */
    public function __construct(
        UploadedVideoRepository $repository,
        EntityManager $entityManager
    ) {
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

        $options = $this->getOptionsFromRequest($request);

        $video
            ->updateStatus(UploadedVideo::STATUS_TRANSFORMATION_READY)
            ->setOptions($options);

        $this->entityManager->flush();
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    private function getOptionsFromRequest(Request $request)
    {
        $eager = $request->request->get('eager');
        $transformation = array_shift($eager);
        $options = [];

        if (!empty($transformation)) {
            $transformationChain = explode('/', $transformation['transformation']);

            foreach ($transformationChain as $transformation) {
                $transformationParams = explode(',', $transformation);

                foreach ($transformationParams as $param) {
                    $params = explode('_', $param);

                    $key = $params[0];
                    $value = empty($params[1]) ? null : $params[1];

                    if (!empty($this->optionsTobeSaved[$key])) {
                        $fullName = $this->optionsTobeSaved[$key];
                        $options[$fullName] = $value;
                    }
                }
            }
        }

        return $options;
    }
}