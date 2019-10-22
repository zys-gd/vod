<?php

namespace SubscriptionBundle\Subscription\Renew\Cron;

use Doctrine\ORM\EntityManager;
use SubscriptionBundle\Entity\CronTask;
use SubscriptionBundle\Repository\CronTaskRepository;
use SubscriptionBundle\Subscription\Renew\Cron\Exception\NoTaskException;
use SubscriptionBundle\Subscription\Renew\Cron\Exception\TaskRunningException;

class CronTaskStatus
{
    const TASK_STATUS_RUN = 1;
    const TASK_STATUS_STOP = 0;
    /**
     * @var CronTask $cronTask
     */
    protected $cronTask = null;

    protected $dontChangeData = false;

    protected $em = null;
    /**
     * @var CronTaskRepository
     */
    private $cronTaskRepository;

    public function __construct(EntityManager $em, CronTaskRepository $cronTaskRepository)
    {
        $this->em                 = $em;
        $this->cronTaskRepository = $cronTaskRepository;
    }

    public function initializeCronTaskByName(String $name)
    {
        $this->initializeCronTask([
            'cronName' => $name,
            'isPaused' => false
        ]);
        return $this;
    }

    private function initializeCronTask(array $params = null): void
    {
        if (is_null($params)) {
            return;
        }

        $this->cronTask = $this->cronTaskRepository->findOneBy($params);

        if ($this->cronTask) {
            if ($this->cronTask->getIsRunning()) {
                $this->dontChangeData = true;
                throw new TaskRunningException('The task already runs!');
            }
        }
    }

    public function isRunning()
    {
        if ($this->cronTask) {
            return (bool)$this->cronTask->getIsRunning();
        } else {
            throw new NoTaskException('Cron task is not set!');
        }
    }

    /**
     * @throws NoTaskException
     */
    public function start()
    {
        $this->setStatus(self::TASK_STATUS_RUN);
    }

    protected function setStatus($status)
    {
        if ($this->cronTask) {
            $this->cronTask->setLastUpdatedAt(new \DateTimeImmutable());
            $this->cronTask->setIsRunning($status);
            $this->save();
        } else {
            throw new NoTaskException('Cron task is not set!');
        }
    }

    protected function save()
    {
        $this->em->persist($this->cronTask);
        $this->em->flush();
    }

    public function __destruct()
    {
        try {

            if (!$this->dontChangeData) {
                $this->stop();
            }
        } catch (NoTaskException $ex) {

        }
    }

    public function stop()
    {
        $this->setStatus(self::TASK_STATUS_STOP);
    }
}