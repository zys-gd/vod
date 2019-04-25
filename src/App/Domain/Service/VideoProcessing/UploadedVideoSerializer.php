<?php


namespace App\Domain\Service\VideoProcessing;


use App\Domain\Entity\UploadedVideo;

class UploadedVideoSerializer
{
    /**
     * @param UploadedVideo $video
     * @return array
     */
    public function serialize(UploadedVideo $video): array
    {
        return [
            'uuid'       => $video->getUuid(),
            'title'      => $video->getTitle(),
            'publicId'   => $video->getRemoteId(),
            'thumbnails' => $video->getThumbnails(),
            'options'    => $video->getOptions(),
            'jsonOptions' => json_encode($video->getOptions())
        ];
    }
}