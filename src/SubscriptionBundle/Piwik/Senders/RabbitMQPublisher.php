<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 06.08.19
 * Time: 12:29
 */

namespace SubscriptionBundle\Piwik\Senders;


use ExtrasBundle\Utils\TimestampGenerator;

class RabbitMQPublisher
{
    public function publish()
    {
        $dataForQueue = [$args[0], TimestampGenerator::generateMicrotime()];

        $this->logger->info('Sending Piwik event', ['piwikData' => $dataForQueue]);

        $this->rabbitMQProducer->sendEvent(json_encode(['piwikData' => $dataForQueue]));
    }
}
