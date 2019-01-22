<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 21.01.19
 * Time: 18:17
 */

namespace ExtrasBundle\SignatureCheck\Exception;


use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class InvalidSignatureException extends BadRequestHttpException
{

}