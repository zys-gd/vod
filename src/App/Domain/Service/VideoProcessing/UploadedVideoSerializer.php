<?php


namespace App\Domain\Service\VideoProcessing;


use App\Domain\Entity\UploadedVideo;

class UploadedVideoSerializer
{
    /**
     * @param UploadedVideo $video
     * @return array
     */
    public function serializeShort(UploadedVideo $video): array
    {
        return [
            'title'        => $video->getTitle(),
            'publicId'     => $video->getRemoteId(),
            'thumbnailUrl' => $video->getThumbnails()[0],
            'jsonOptions'  => json_encode($video->getOptions())
        ];
    }

    /**
     * @param UploadedVideo $video
     * @return string
     */
    public function serializeJson(UploadedVideo $video): string
    {
        $videoData = [
            'uuid' => $video->getUuid(),
            'mainCategory' => $video->getSubcategory()->getParent()->getUuid(),
            'subcategory' => $video->getSubcategory()->getUuid(),
            'videoPartner' => $video->getVideoPartner()->getUuid(),
            'title' => $video->getTitle(),
            'description' => $video->getDescription(),
            'expiredDate' => $video->getExpiredDate() ? $video->getExpiredDate()->format('Y-MM-dd HH:mm') : null,
            'remoteId' => $video->getRemoteId(),
            'remoteUrl' => $video->getRemoteUrl(),
            'thumbnails' => $video->getThumbnails()
        ];

        return json_encode($videoData);
    }
}