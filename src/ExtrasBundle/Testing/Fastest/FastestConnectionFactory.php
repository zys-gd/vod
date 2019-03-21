<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 21.09.18
 * Time: 17:37
 */

namespace ExtrasBundle\Testing\Fastest;


use Doctrine\Bundle\DoctrineBundle\ConnectionFactory;
use Doctrine\Common\EventManager;
use Doctrine\DBAL\Configuration;
use Liuggio\Fastest\Process\EnvCommandCreator;

class FastestConnectionFactory extends ConnectionFactory
{

    /**
     * Create a connection by name.
     *
     * @param array         $params
     * @param Configuration $config
     * @param EventManager  $eventManager
     * @param array         $mappingTypes
     *
     * @return \Doctrine\DBAL\Connection
     */
    public function createConnection(array $params, Configuration $config = null, EventManager $eventManager = null, array $mappingTypes = [])
    {
        if (isset($params['dbname'])) {
            $dbName = $this->getDbNameFromEnv($params['dbname']);
        } else {
            $dbName = $this->getDbNameFromEnv($params['master']['dbname']);
        }


        if ('ExtrasBundle\Development\DBAL\Sqlite\ModifiedDriver' === $params['driverClass']) {
            if (isset($params['path'])) {
                $params['path'] = str_replace('__DBNAME__', $dbName, $params['path']);
            }

            if (isset($params['master']['path'])) {
                $params['master']['path'] = str_replace('__DBNAME__', $dbName, $params['master']['path']);
            }

            if (!empty($params['slaves'])) {
                foreach ($params['slaves'] as &$slave) {
                    $slave['path'] = str_replace('__DBNAME__', $dbName, $slave['path']);
                }
            }
        } else {
            $params['dbname'] = $dbName;
        }

        return parent::createConnection($params, $config, $eventManager, $mappingTypes);
    }

    protected function getDbNameFromEnv($dbName)
    {
        if ($this->issetDbNameEnvValue()) {
            return $dbName.'_'.$this->getDbNameEnvValue();
        }

        return $dbName;
    }

    protected function issetDbNameEnvValue()
    {
        $dbName = $this->getDbNameEnvValue();

        return !empty($dbName);
    }

    protected function getDbNameEnvValue()
    {
        return getenv(EnvCommandCreator::ENV_TEST_CHANNEL_READABLE);
    }

}