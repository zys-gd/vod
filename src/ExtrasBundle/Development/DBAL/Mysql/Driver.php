<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 20.04.18
 * Time: 10:46
 */

namespace ExtrasBundle\Development\DBAL\Mysql;


use ExtrasBundle\Development\DBAL\Mysql\Platform;
use Doctrine\DBAL\Driver\PDOMySql\Driver as BaseDriver;

class Driver extends BaseDriver
{
    public function createDatabasePlatformForVersion($version)
    {
        return $this->getDatabasePlatform();
    }

    /**
     * {@inheritdoc}
     */
    public function getDatabasePlatform()
    {
        return new Platform();
    }
}