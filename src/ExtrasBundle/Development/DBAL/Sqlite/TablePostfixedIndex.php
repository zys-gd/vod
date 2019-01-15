<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 24.05.18
 * Time: 13:43
 */

namespace ExtrasBundle\Development\DBAL\Sqlite;


use Doctrine\DBAL\Platforms\AbstractPlatform;

class TablePostfixedIndex extends \Doctrine\DBAL\Schema\Index
{
    private $tableName;

    public function __construct(string $indexName, $columns, bool $isUnique = false, bool $isPrimary = false, $flags = array(), array $options = array(), string $tableName)
    {

        $this->tableName = $tableName;
        parent::__construct($indexName, $columns, $isUnique, $isPrimary, $flags, $options);
    }


    public function getQuotedName(AbstractPlatform $abstractPlatform)
    {

        $name = parent::getQuotedName($abstractPlatform);

        return $name . '_' . $this->tableName;
    }

}