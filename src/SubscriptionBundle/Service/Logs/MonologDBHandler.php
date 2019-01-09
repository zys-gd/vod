<?php
/**
 * Created by IntelliJ IDEA.
 * User: bharatm
 * Date: 23/08/17
 * Time: 2:08 PM
 */

namespace SubscriptionBundle\Service\Logs;


use Doctrine\ORM\EntityManagerInterface;
use Monolog\Handler\AbstractProcessingHandler;
use SubscriptionBundle\Entity\Log;

class MonologDBHandler extends AbstractProcessingHandler
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * MonologDBHandler constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    /**
     * Called when writing to our database
     * @param array $record
     */
    protected function write(array $record)
    {
        $logEntry = new Log();
        $logEntry->setMessage($record['message']);
        $logEntry->setLevel($record['level']);
        $logEntry->setLevelName($record['level_name']);
        $logEntry->setExtra($record['extra']);
        $logEntry->setContext($record['context']);

        $subscription = isset($record['context']['subscription']) ? $record['context']['subscription']: null;
        if($subscription) {
            $logEntry->setSubscriptionId($subscription->getId());
        }

        $this->em->persist($logEntry);
        $this->em->flush();
    }
}