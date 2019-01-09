<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 30.04.18
 * Time: 16:09
 */

namespace SubscriptionBundle\DataFixtures\ORM;


use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use SubscriptionBundle\Entity\CronTask;

class LoadCronTasksData extends AbstractFixture
{

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $data = [
            ["1", "mobilinkPakistanCronTask", "0"],
            ["2", "dialogSriLankaCronTask", "0"],
            ["3", "MTNSudanCronTask", "0"],
            ["4", "zongPakistanCronTask", "0"],
            ["5", "zainSudanCronTask", "0"],
            ["7", "telenorPakistanCronTask", "0"],
            ["8", "SmartfrenIndCronTask", "0"],
            ["9", "indosatIndonesiaCronTask", "0"],
            ["10", "telenorPakistanRenewAlertCronTask", "0"],
            ["11", "telkomKenyaCronTask", "0"],
            ["12", "globePhilippinesTrialCronTask", "0"],
        ];


        foreach ($data as $row) {
            list($id, $name, $isRunning) = $row;

            $cronTask = new CronTask();
            $cronTask->setCronName($name);
            $cronTask->setIsRunning($isRunning);

            $manager->persist($cronTask);
            $this->addReference(sprintf('cron_task_%s', $id), $cronTask);
        }

        $manager->flush();
    }

}