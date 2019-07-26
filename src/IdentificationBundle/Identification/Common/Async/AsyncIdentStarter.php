<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 10.01.19
 * Time: 14:01
 */

namespace IdentificationBundle\Identification\Common\Async;


use IdentificationBundle\Identification\Service\IdentificationDataStorage;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AsyncIdentStarter
{
    /**
     * @var IdentificationDataStorage
     */
    private $dataStorage;


    /**
     * AsyncIdentStarter constructor.
     * @param IdentificationDataStorage $dataStorage
     */
    public function __construct(IdentificationDataStorage $dataStorage)
    {
        $this->dataStorage = $dataStorage;
    }

    public function start(ProcessResult $processResult, string $token): RedirectResponse
    {
        $this->dataStorage->storeValue('redirectIdent[token]', $token);

        return new RedirectResponse($processResult->getUrl());
    }
}