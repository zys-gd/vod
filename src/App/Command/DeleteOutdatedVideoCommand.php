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
 * Class DeleteOutdatedVideos
 */
class DeleteOutdatedVideoCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'app:delete-outdated-videos';

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
     * DeleteOutdatedVideos constructor
     *
     * @param EntityManagerInterface  $entityManager
     * @param UploadedVideoRepository $uploadedVideoRepository
     * @param CloudinaryConnector     $cloudinaryConnector
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        UploadedVideoRepository $uploadedVideoRepository,
        CloudinaryConnector $cloudinaryConnector
    )
    {
        $this->entityManager           = $entityManager;
        $this->uploadedVideoRepository = $uploadedVideoRepository;
        $this->cloudinaryConnector     = $cloudinaryConnector;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Delete outdated video');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     *
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $outdatedVideos = $this->uploadedVideoRepository->findOutdatedVideo(
            new \DateTimeImmutable('-6 month')
        );

        $count = count($outdatedVideos);
        $output->writeln("$count outdated video was found");

        $successfullyDeleted = 0;
        $errors              = [];

        /** @var UploadedVideo $outdatedVideo */
        foreach ($outdatedVideos as $outdatedVideo) {
            try {
                $response = $this->cloudinaryConnector->destroyVideo($outdatedVideo->getRemoteId());
                $result   = $response['result'];

                if (
                    $result === CloudinaryConnector::SUCCESS_DESTROY_RESULT ||
                    $result === CloudinaryConnector::NOT_FOUND_DESTROY_RESULT
                ) {
                    $successfullyDeleted++;
                    $this->entityManager->remove($outdatedVideo);
                }
            } catch (\Exception $exception) {
                $errors[] = $outdatedVideo->getRemoteId();
            }
        }

        $errorsIds = empty($errors) ? 'no errors occurred' : implode(', ', $errors);

        $output->writeln("$successfullyDeleted video was successfully deleted");
        $output->writeln("Errors: $errorsIds");

        $this->entityManager->flush();
        $this->entityManager->clear();
    }
}