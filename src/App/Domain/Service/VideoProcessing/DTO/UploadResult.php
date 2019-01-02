<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 24.12.18
 * Time: 14:11
 */

namespace App\Domain\Service\VideoProcessing\DTO;


class UploadResult
{
    /**
     * @var string
     */
    private $url;
    /**
     * @var string
     */
    private $remoteId;


    /**
     * UploadResult constructor.
     * @param string $url
     * @param string $remoteId
     */
    public function __construct(string $url, string $remoteId)
    {
        $this->url      = $url;
        $this->remoteId = $remoteId;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getRemoteId(): string
    {
        return $this->remoteId;
    }


}