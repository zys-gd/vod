<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 09.01.19
 * Time: 10:25
 */

namespace IdentificationBundle\Identification\Handler;


use IdentificationBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;

interface HasCommonFlow
{

    public function getAdditionalIdentificationParams(Request $request): array;

    public function getExistingUser(string $msisdn): ?User;

}