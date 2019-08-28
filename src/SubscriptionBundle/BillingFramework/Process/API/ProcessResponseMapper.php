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

        $status  = ($responseData->status) ?? null;
        $error   = ($responseData->error) ?? null;
        $type    = ($responseData->type) ?? null;
        $message = null;

        //TODO: Refactor based on billing framework documentation. Inject error messages form config
        if (self::PROCESS_STATUS_FAILED === $status) {
            $message = 'Your request is failed';
        } elseif ($status == 'successful_trial_subscription') {
            $status  = self::PROCESS_STATUS_SUCCESSFUL;
            $message = 'You have being subscribed for free trial!';
        } elseif (self::PROCESS_STATUS_SUCCESSFUL !== $status) {
            $message = 'Your request is in process';
        }

        if (isset($error) && $error) {
            switch ($error) {
                case 'already_done':
                    $message = 'Please try again later.';// You have already been subscribed';
                    break;
                case 'internal':
                    $message = 'Cannot process your request.';//Your request contains incorrect data';
                    break;
                case 'rejected':
                    if ($type == 'subscribe') {
                        $message = 'You can\'t subscribe now. Please try again later.';//Your request was rejected';
                    } elseif ($type == 'unsubscribe') {
                        $message = 'You can\'t unsubscribe now. Please try again later.';//Your request was rejected';
                    } else {
                        $message = 'Cannot process your request. Please try again later.';//Your request was rejected';
                    }
                    break;
                case 'connection_error':
                    $message = 'Cannot process your request.';//Can not connect to billing system';
                    break;
                case 'not_enough_credit':
                    $message = "Not enough credit! Please check your balance";
                    break;
                case 'canceled':
                    $message = 'Cannot process your request.';//The request was canceled';
                    break;
                case 'too_many_tries':
                    $message = 'Cannot process your request. Too many tries';
                    break;
                case 'user_timeout':
                    $message = 'Cannot process your request. User timeout';
                    break;
                case 'expired_timeout':
                    $message = 'Cannot process your request. Expired timeout';
                    break;
                default:
                    $message = 'Cannot process your request. Your request is failed';
                    break;
            }
        }

        $responseData->message = $message;
        $responseData->status  = $status;

        return $responseData;

    }

}