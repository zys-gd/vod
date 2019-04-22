<?php

namespace App\Command;

use App\Domain\Entity\UploadedVideo;
use App\Domain\Repository\UploadedVideoRepository;
use App\Domain\Service\VideoProcessing\Connectors\CloudinaryConnector;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class DeleteExpiredVideos
 */
class DeleteExpiredVideo extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'app:delete-expired-videos';

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var UploadedVideoRepository
     */
    private $uploadedVideoRepository;

    /**
     * @var CloudinaryConnector
     */
    private $cloudinaryConnector;

    /**
     * DeleteExpiredVideos constructor
     *
     * @param EntityManagerInterface $entityManager
     * @param UploadedVideoRepository $uploadedVideoRepository
     * @param CloudinaryConnector $cloudinaryConnector
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        UploadedVideoRepository $uploadedVideoRepository,
        CloudinaryConnector $cloudinaryConnector
    ) {
        $this->entityManager = $entityManager;
        $this->uploadedVideoRepository = $uploadedVideoRepository;
        $this->cloudinaryConnector = $cloudinaryConnector;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Delete expired video');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return void
     *
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $expiredVideos = $this->uploadedVideoRepository->findExpiredVideo();

        /** @var UploadedVideo $expiredVideo */
        foreach ($expiredVideos as $expiredVideo) {
            try {
                $response = $this->cloudinaryConnector->destroyVideo($expiredVideo->getRemoteId());
                $result = $response['result'];

                if (
                    $result === CloudinaryConnector::SUCCESS_DESTROY_RESULT
                    || $result === CloudinaryConnector::NOT_FOUND_DESTROY_RESULT
                ) {
                    $this->entityManager->remove($expiredVideo);
                }
            } catch (\Exception $exception) {
                //do nothing
            }
        }

        $this->entityManager->flush();
        $this->entityManager->clear();
    }
}