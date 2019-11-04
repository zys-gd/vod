<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 03.05.18
 * Time: 18:11
 */

namespace SubscriptionBundle\BillingFramework\Process\API;


use stdClass;
use SubscriptionBundle\BillingFramework\Process\API\DTO\ProcessResult;
use SubscriptionBundle\BillingFramework\Process\API\Exception\EmptyResponse;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class ProcessResponseMapper
{
    const PROCESS_STATUS_FAILED = 'failed';
    const PROCESS_STATUS_SUCCESSFUL = 'successful';


    /**
     * @param string   $type
     * @param stdClass $responseData
     * @return ProcessResult
     * @throws EmptyResponse
     */
    public function map(string $type, stdClass $responseData)
    {
        if (isset($responseData->data) && is_array($responseData->data) && !$responseData->data) {
            throw new EmptyResponse();
        } else if (isset($responseData->data) && $responseData->data) {
            $responseData = $responseData->data;
        }

        $responseData = $this->prepareResponseData($responseData);
        $normalizer   = new ObjectNormalizer(null, new CamelCaseToSnakeCaseNameConverter());
        /** @var ProcessResult $processResponse */
        $processResponse = $normalizer->denormalize(
            array_merge((array)$responseData, ['type' => $type]),
            ProcessResult::class
        );

        return $processResponse;
    }

    private function prepareResponseData($responseData)
    {
        if (!is_object($responseData)) {
            $responseData = (object)$responseData;
        }

        return $responseData;

    }

}