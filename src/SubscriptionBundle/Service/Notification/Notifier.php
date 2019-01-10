<?php
/**
 * Created by PhpStorm.
 * User: Администратор
 * Date: 28.10.2018
 * Time: 16:46
 */

namespace SubscriptionBundle\Service\Notification;


use AppBundle\Entity\Carrier;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use SubscriptionBundle\BillingFramework\Notification\API\DTO\SMSRequest;
use SubscriptionBundle\BillingFramework\Notification\API\RequestSender;
use SubscriptionBundle\BillingFramework\Notification\Exception\MissingSMSTextException;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Entity\SubscriptionPack;
use SubscriptionBundle\Service\Notification\Common\MessageCompiler;
use SubscriptionBundle\Service\Notification\Common\ProcessIdExtractor;
use SubscriptionBundle\Service\Notification\Impl\NotificationHandlerProvider;
use UserBundle\Service\UsersService;

class Notifier
{
    /**
     * @var MessageCompiler
     */
    private $messageCompiler;
    /**
     * @var RequestSender
     */
    private $sender;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var UsersService
     */
    private $usersService;
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var ProcessIdExtractor
     */
    private $processIdExtractor;
    /**
     * @var NotificationHandlerProvider
     */
    private $notificationHandlerProvider;


    /**
     * Notifier constructor.
     * @param MessageCompiler             $messageCompiler
     * @param RequestSender               $sender
     * @param LoggerInterface             $logger
     * @param ProcessIdExtractor          $processIdExtractor
     * @param UsersService                $usersService
     * @param RouterInterface             $router
     * @param NotificationHandlerProvider $notificationHandlerProvider
     */
    public function __construct(
        MessageCompiler $messageCompiler,
        RequestSender $sender,
        LoggerInterface $logger,
        ProcessIdExtractor $processIdExtractor,
        UsersService $usersService,
        RouterInterface $router,
        NotificationHandlerProvider $notificationHandlerProvider
    )
    {
        $this->messageCompiler             = $messageCompiler;
        $this->sender                      = $sender;
        $this->logger                      = $logger;
        $this->usersService                = $usersService;
        $this->router                      = $router;
        $this->processIdExtractor          = $processIdExtractor;
        $this->notificationHandlerProvider = $notificationHandlerProvider;
    }

    public function sendNotification(
        string $type,
        Subscription $subscription,
        SubscriptionPack $subscriptionPack,
        Carrier $carrier
    )
    {

        try {

            $handler = $this->notificationHandlerProvider->get($type, $carrier);

            if (!$handler->isNotificationShouldBeSent()) {
                return;
            }

            $billableUser = $subscription->getUser();

            if ($handler->isProcessIdUsedInNotification()) {
                $processId = $this->processIdExtractor->extractProcessId($billableUser);
            } else {
                $processId = null;
            }


            if (!$billableUser->getUrlId()) {
                $this->usersService->setAutologinUrlId($billableUser);
                $this->logger->debug('Generated auto-login URL for user. ', ['url' => $billableUser->getUrlId()]);
            }

            $url          = $this->router->generate(
                'user-identity',
                ['urlId' => $billableUser->getUrlId()],
                UrlGeneratorInterface::ABSOLUTE_URL
            );

            $notification = $this->messageCompiler->compileNotification(
                $type,
                $billableUser,
                $subscriptionPack,
                $processId,
                ['_autologin_url_' => $url]
            );
            $this->sender->sendNotification($notification, $carrier->getIdCarrier());


        } catch (MissingSMSTextException $exception) {
            $this->logger->error('Missing sms text');
        }

    }

    public function sendSMS(Carrier $carrier, string $clientUser, string $urlId, string $subscriptionPlan, string $lang)
    {
        $request = new SMSRequest($clientUser, $urlId, $subscriptionPlan, $lang);

        $this->sender->sendSMS($request, $carrier->getIdCarrier());

    }
}