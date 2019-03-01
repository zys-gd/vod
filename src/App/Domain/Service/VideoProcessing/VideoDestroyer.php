<?php

namespace App\Domain\Service\VideoProcessing;

use App\Domain\Service\VideoProcessing\Connectors\CloudinaryConnector;

/**
 * Class VideoDestroyer
 */
class VideoDestroyer
{
    /**
     * @var CloudinaryConnector
     */
    private $cloudinaryConnector;

    /**
     * VideoDestroyer constructor
     *
     * @param CloudinaryConnector $cloudinaryConnector
     */
    public function __construct(CloudinaryConnector $cloudinaryConnector)
    {
        $this->cloudinaryConnector = $cloudinaryConnector;
    }

    /**
     * @param string $remoteId
     *
     * @return mixed
     */
    public function destroy(string $remoteId)
    {
        return $this->cloudinaryConnector->destroyVideo($remoteId);
    }
}