<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 24.05.18
 * Time: 13:26
 */

namespace ExtrasBundle\Development\DBAL\Sqlite;


use Doctrine\DBAL\Driver\PDOSqlite\Driver;

class ModifiedDriver extends Driver
{

    /**
     * {@inheritdoc}
     */
    public function getDatabasePlatform()
    {
        return new Platform();
    }

}