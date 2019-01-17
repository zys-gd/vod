<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 15.05.18
 * Time: 10:53
 */

namespace PiwikBundle\Enqueue;


use Enqueue\AmqpExt\AmqpContext;
use Enqueue\Client\ProducerInterface;
use Enqueue\Client\TopicSubscriberInterface;
use Interop\Amqp\Impl\AmqpBind;
use Interop\Queue\PsrContext;
use Interop\Queue\PsrMessage;
use Interop\Queue\PsrProcessor;
use PiwikBundle\Api\ClientAbstract;
use PiwikBundle\Service\PiwikDataSender;

class PiwikEventsConsumer implements PsrProcessor, TopicSubscriberInterface
{

    /**
     * @var AmqpContext
     */
    private $amqpContext;

    /**
     * @var PiwikDataSender
     */
    private $dataSender;
    /**
     * @var ProducerInterface
     */
    private $producer;

    /**
     * PiwikEventsConsumer constructor.
     * @param PiwikDataSender   $dataSender
     * @param ProducerInterface $producer
     * @param AmqpContext       $amqpContext
     */
    public function __construct(PiwikDataSender $dataSender, ProducerInterface $producer, AmqpContext $amqpContext)
    {
        $this->dataSender  = $dataSender;
        $this->producer    = $producer;
        $this->amqpContext = $amqpContext;
    }


    /**
     * The method has to return either self::ACK, self::REJECT, self::REQUEUE string.
     *
     * The method also can return an object.
     * It must implement __toString method and the method must return one of the constants from above.
     *
     * @param PsrMessage $message
     * @param PsrContext $context
     *
     * @return string|object with __toString method implemented
     */
    public function process(PsrMessage $message, PsrContext $context)
    {
        try {

            $this->sendToAMQP($message->getBody());
            return self::ACK;
        } catch (\Exception $e) {
            return self::REQUEUE;
        }
    }

    /**
     * The result maybe either:.
     *
     * ['aTopicName']
     *
     * or
     *
     * ['aTopicName' => [
     *     'processorName' => 'processor',
     *     'queueName' => 'a_client_queue_name',
     *     'queueNameHardcoded' => true,
     *   ]]
     *
     * processorName, queueName and queueNameHardcoded are optional.
     *
     * Note: If you set queueNameHardcoded to true then the queueName is used as is and therefor the driver is not used to create a transport queue name.
     *
     * @return array
     */
    public static function getSubscribedTopics()
    {
        return [
            ClientAbstract::EXCHANGE_NAME
        ];
    }

    private function sendToAMQP($body)
    {
        $topic = $this->amqpContext->createTopic('piwik-events-2');
        $topic->addFlag(AMQP_EX_TYPE_DIRECT);
        $topic->addFlag(AMQP_DURABLE);
        $this->amqpContext->declareTopic($topic);
        $queue = $this->amqpContext->createQueue('piwik-events-send-2');
        $queue->addFlag(AMQP_DURABLE);
        $this->amqpContext->declareQueue($queue);
        $this->amqpContext->bind(new AmqpBind($topic, $queue));

        $message = $this->amqpContext->createMessage($body);
        $this->amqpContext->createProducer()->send($topic, $message);
    }
}