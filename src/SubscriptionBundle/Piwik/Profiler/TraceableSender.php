<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 16.08.19
 * Time: 17:02
 */

namespace SubscriptionBundle\Piwik\Profiler;


use ExtrasBundle\Utils\TimestampGenerator;
use SubscriptionBundle\Piwik\Senders\SenderInterface;

class TraceableSender implements SenderInterface
{
    /**
     * @var SenderInterface
     */
    private $sender;
    private $calls = [];


    /**
     * TraceableSender constructor.
     * @param SenderInterface $sender
     */
    public function __construct(SenderInterface $sender)
    {
        $this->sender = $sender;
    }

    public function sendEvent($data): bool
    {
        $result = $this->sender->sendEvent($data);

        $this->calls[] = [
            'time'   => TimestampGenerator::generateMicrotime(),
            'data'   => $data,
            'result' => $result
        ];

        return $result;
    }

    public function getCalls(): array
    {
        return $this->calls;
    }

    public function reset(): void
    {
        $this->calls = [];
    }
}