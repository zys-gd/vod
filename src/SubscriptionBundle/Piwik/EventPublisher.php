<?php

namespace SubscriptionBundle\Piwik;

use ExtrasBundle\Utils\TimestampGenerator;
use Psr\Log\LoggerInterface;
use SubscriptionBundle\Piwik\DTO\ConversionEvent;
use SubscriptionBundle\Piwik\Formatter\FormatterInterface;
use SubscriptionBundle\Piwik\Senders\RabbitMQ;
use SubscriptionBundle\Piwik\Senders\SenderInterface;


class EventPublisher
{
    /**
     * @var RabbitMQ
     */
    private $sender;
    /**
     * @var FormatterInterface
     */
    private $formatter;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * EventPublisher constructor.
     *
     * @param SenderInterface    $sender
     * @param FormatterInterface $formatter
     * @param LoggerInterface    $logger
     */
    public function __construct(SenderInterface $sender, FormatterInterface $formatter, LoggerInterface $logger)
    {
        $this->sender    = $sender;
        $this->formatter = $formatter;
        $this->logger    = $logger;
    }


    /**
     * @param ConversionEvent $conversionEvent
     * @return bool
     */
    public function publish(ConversionEvent $conversionEvent): bool
    {
        $data = $this->formatter->prepareFormattedData($conversionEvent);

        try {
            $result = $this->sender->sendEvent($data);
            $this->logger->info('Sending is finished', ['result' => $result]);
            return $result;

        } catch (\Exception $ex) {
            $this->logger->info('Exception on piwik sending', [
                'msg'  => $ex->getMessage(),
                'line' => $ex->getLine(),
                'code' => $ex->getCode()
            ]);
            return false;
        }

    }
}