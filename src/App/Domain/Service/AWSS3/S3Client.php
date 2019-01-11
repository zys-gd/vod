<?php

namespace App\Domain\Service\AWSS3;

use Aws\S3\S3Client as OriginalS3;

/**
 * Class S3Client
 */
class S3Client extends OriginalS3
{
    /**
     * S3Client constructor
     *
     * @param array $args
     */
    public function __construct(array $args)
    {
        parent::__construct($args);

    }
}