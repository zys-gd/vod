<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 09.01.19
 * Time: 10:26
 */

namespace IdentificationBundle\Identification\Handler;


use Symfony\Component\HttpFoundation\Request;

interface HasCustomFlow
{
    public function process(Request $request);
}