<?php

namespace App\Command;

use App\Domain\Repository\UploadedVideoRepository;
use App\Domain\Service\VideoProcessing\Connectors\CloudinaryConnector;
use App\Domain\Service\VideoProcessing\VideoManager;
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
     * @var VideoManager
     */
    private $videoManager;

    /**
     * DeleteExpiredVideos constructor
     *
     * @param EntityManagerInterface $entityManager
     * @param UploadedVideoRepository $uploadedVideoRepository
     * @param VideoManager $videoManager
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        UploadedVideoRepository $uploadedVideoRepository,
        VideoManager $videoManager
    ) {
        $this->entityManager = $entityManager;
        $this->uploadedVideoRepository = $uploadedVideoRepository;
        $this->videoManager = $videoManager;

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

        foreach ($expiredVideos as $expiredVideo) {
            try {
                $response = $this->videoManager->destroyUploadedVideo($expiredVideo);
                $result = $response['result'];
                $output->writeln($result);
                $output->writeln(CloudinaryConnector::SUCCESS_DESTROY_RESULT);

                if ($result === CloudinaryConnector::SUCCESS_DESTROY_RESULT) {
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