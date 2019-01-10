<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 10.01.19
 * Time: 14:01
 */

namespace IdentificationBundle\Service\Action\Identification\Common\Redirect;


use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class RedirectIdentHandler
{
    public function doHandle(ProcessResult $processResult): Response
    {
        return new RedirectResponse($processResult->getUrl());
    }
}