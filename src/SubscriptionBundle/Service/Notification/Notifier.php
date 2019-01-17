<?php
/**
 * Created by PhpStorm.
 * User: Администратор
 * Date: 28.10.2018
 * Time: 16:46
 */

namespace SubscriptionBundle\Service\Notification;


use IdentificationBundle\Entity\CarrierInterface;
use Psr\Log\LoggerInterface;
use SubscriptionBundle\BillingFramework\Notification\API\DTO\SMSRequest;
use SubscriptionBundle\BillingFramework\Notification\API\RequestSender;
use SubscriptionBundle\BillingFramework\Notification\Exception\MissingSMSTextException;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Entity\SubscriptionPack;
use SubscriptionBundle\Service\Notification\Common\MessageCompiler;
use SubscriptionBundle\Service\Notification\Common\ProcessIdExtractor;
use SubscriptionBundle\Service\Notification\Common\ShortUrlHashGenerator;
use SubscriptionBundle\Service\Notification\Impl\NotificationHandlerProvider;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
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
     * @var ShortUrlHashGenerator
     */
    private $shortUrlHashGenerator;


    /**
     * Notifier constructor.
     * @param MessageCompiler             $messageCompiler
     * @param RequestSender               $sender
     * @param LoggerInterface             $logger
     * @param ProcessIdExtractor          $processIdExtractor
     * @param RouterInterface             $router
     * @param NotificationHandlerProvider $notificationHandlerProvider
     * @param ShortUrlHashGenerator       $shortUrlHashGenerator
     */
    public function __construct(
        MessageCompiler $messageCompiler,
        RequestSender $sender,
        LoggerInterface $logger,
        ProcessIdExtractor $processIdExtractor,
        RouterInterface $router,
        NotificationHandlerProvider $notificationHandlerProvider,
        ShortUrlHashGenerator $shortUrlHashGenerator
    )
    {
        $this->messageCompiler             = $messageCompiler;
        $this->sender                      = $sender;
        $this->logger                      = $logger;
        $this->router                      = $router;
        $this->processIdExtractor          = $processIdExtractor;
        $this->notificationHandlerProvider = $notificationHandlerProvider;
        $this->shortUrlHashGenerator = $shortUrlHashGenerator;
    }

    public function sendNotification(
        string $type,
        Subscription $subscription,
        SubscriptionPack $subscriptionPack,
        CarrierInterface $carrier
    )
    {

        try {

            $handler = $this->notificationHandlerProvider->get($type, $carrier);

            if (!$handler->isNotificationShouldBeSent()) {
                return;
            }

            $User = $subscription->getUser();

            if ($handler->isProcessIdUsedInNotification()) {
                $processId = $this->processIdExtractor->extractProcessId($User);
            } else {
                $processId = null;
            }


            if (!$User->getUrlId()) {
                $User->setShortUrlId($this->shortUrlHashGenerator->generate());
                $this->logger->debug('Generated auto-login URL for user. ', ['url' => $User->getUrlId()]);
            }

            $url = $this->router->generate(
                'identify_by_url',
                ['urlId' => $User->getUrlId()],
                UrlGeneratorInterface::ABSOLUTE_URL
            );

            $notification = $this->messageCompiler->compileNotification(
                $type,
                $User,
                $subscriptionPack,
                $processId,
                ['_autologin_url_' => $url]
            );
            $this->sender->sendNotification($notification, $carrier->getBillingCarrierId());


        } catch (MissingSMSTextException $exception) {
            $this->logger->error('Missing sms text');
        }

    }

    public function sendSMS(CarrierInterface $carrier, string $clientUser, string $urlId, string $subscriptionPlan, string $lang)
    {
        $request = new SMSRequest($clientUser, $urlId, $subscriptionPlan, $lang);

        $this->sender->sendSMS($request, $carrier->getBillingCarrierId());

    }
}