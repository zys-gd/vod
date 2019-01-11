<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 11.01.19
 * Time: 10:17
 */

namespace IdentificationBundle\Service\Action\Identification\Handler;


use Symfony\Component\HttpFoundation\Request;

interface HasHeaderEnrichment
{
    public function getMsisdn(Request $request): ?string;
}