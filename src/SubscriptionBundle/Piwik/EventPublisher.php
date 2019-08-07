<?php

namespace SubscriptionBundle\Piwik;

use ExtrasBundle\Utils\TimestampGenerator;
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
     * EventPublisher constructor.
     *
     * @param SenderInterface    $sender
     * @param FormatterInterface $formatter
     */
    public function __construct(SenderInterface $sender, FormatterInterface $formatter)
    {
        $this->sender    = $sender;
        $this->formatter = $formatter;
    }


    /**
     * @param ConversionEvent $conversionEvent
     * @return bool
     */
    public function publish(ConversionEvent $conversionEvent): bool
    {
        $data   = $this->formatter->prepareFormattedData($conversionEvent);
        $result = $this->sender->sendEvent(
            $data,
            TimestampGenerator::generateMicrotime()
        );

        return $result;
    }
}