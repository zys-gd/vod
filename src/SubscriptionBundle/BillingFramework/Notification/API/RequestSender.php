<?php
/**
 * Created by PhpStorm.
 * User: Artem Ryskin
 * Date: 23.03.2017
 * Time: 15:47
 */

namespace SubscriptionBundle\BillingFramework\Notification\API;

use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;
use SubscriptionBundle\BillingFramework\Notification\API\DTO\NotificationMessage;
use SubscriptionBundle\BillingFramework\Notification\API\DTO\SMSRequest;
use SubscriptionBundle\BillingFramework\Notification\API\Exception\NotificationSendFailedException;
use SubscriptionBundle\BillingFramework\Notification\DTO;
use SubscriptionBundle\BillingFramework\Process\API\LinkCreator;

class RequestSender
{

    /**
     * @var Client
     */
    private $client;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var LinkCreator
     */
    private $linkCreator;

    public function __construct(Client $client, LoggerInterface $logger, LinkCreator $linkCreator)
    {
        $this->client      = $client;
        $this->logger      = $logger;
        $this->linkCreator = $linkCreator;
    }

    public function sendNotification(NotificationMessage $notificationMessage, $carrierId)
    {

        $data = $this->extractNotificationData($notificationMessage);
        try {
            $response = $this->client->post($this->generateUrl($carrierId), ['json' => $data]);
        } catch (\Exception $ex) {
            $this->logger->error(
                'Error while sending notification.',
                ['notificationMessage' => $notificationMessage]
            );
            throw new NotificationSendFailedException('Could not send notification', $ex->getCode(), $ex);
        }
    }

    private function extractNotificationData(NotificationMessage $notificationMessage): array
    {
        return [
            'type'           => $notificationMessage->getType(),
            'body'           => $notificationMessage->getBody(),
            'msisdn'         => $notificationMessage->getPhone(),
            'opId'           => $notificationMessage->getOperatorId(),
            'billingProcess' => $notificationMessage->getBillingProccess()
        ];
    }

    private function generateUrl($carrierId)
    {
        return $this->linkCreator->createCustomLink(sprintf('public/special/%s/subnotif', $carrierId));
    }

    public function sendSMS(SMSRequest $request, $carrierId)
    {
        $data = $this->extractSMSRequestData($request);

        try {
            $response = $this->client->post($this->generateUrl($carrierId), ['json' => $data]);
        } catch (\Exception $ex) {
            $this->logger->error(
                'Error while sending sms request.',
                ['smsRequest' => $request]
            );
            throw new NotificationSendFailedException('Could not send sms request', $ex->getCode(), $ex);
        }

    }

    private function extractSMSRequestData(SMSRequest $request): array
    {
        return [
            'client_user'       => $request->getClientUser(),
            'url_id'            => $request->getUrlId(),
            'subscription_plan' => $request->getSubscriptionPlan(),
            'lang'              => $request->getLang(),
        ];
    }

}
