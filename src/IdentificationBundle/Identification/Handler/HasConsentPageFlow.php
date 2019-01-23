<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 23.01.19
 * Time: 13:44
 */

namespace IdentificationBundle\Identification\Handler;


use Symfony\Component\HttpFoundation\Request;

interface HasConsentPageFlow
{
    public function onProcess(Request $request): void;
}