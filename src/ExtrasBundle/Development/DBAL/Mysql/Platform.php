<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 20.04.18
 * Time: 10:45
 */

namespace ExtrasBundle\Development\DBAL\Mysql;


use Doctrine\DBAL\Platforms\MySqlPlatform;

class Platform extends MySqlPlatform
{
    /**
     * {@inheritdoc}
     */
    public function getTruncateTableSQL($tableName, $cascade = false)
    {
        return sprintf('SET foreign_key_checks = 0;TRUNCATE %s;SET foreign_key_checks = 1;', $tableName);
    }
}