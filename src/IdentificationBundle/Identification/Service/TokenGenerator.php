<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 10.01.19
 * Time: 18:32
 */

namespace IdentificationBundle\Identification\Service;


class TokenGenerator
{
    public function generateToken(): string
    {
        return md5(microtime(true));
    }
}