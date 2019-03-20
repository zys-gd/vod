<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 20.03.19
 * Time: 18:22
 */

namespace ExtrasBundle\Testing\Liip;


use Doctrine\Common\DataFixtures\Executor\AbstractExecutor;
use Liip\FunctionalTestBundle\Services\DatabaseBackup\DatabaseBackupInterface;
use Liip\FunctionalTestBundle\Services\DatabaseBackup\SqliteDatabaseBackup;

class SqliteDatabaseBackupWrapper implements DatabaseBackupInterface
{
    /**
     * @var SqliteDatabaseBackup
     */
    private $sqliteDatabaseBackup;
    /**
     * @var string
     */
    private $kernelCacheDir;


    /**
     * SqliteDatabaseBackupWrapper constructor.
     * @param SqliteDatabaseBackup $sqliteDatabaseBackup
     * @param string               $kernelCacheDir
     */
    public function __construct(SqliteDatabaseBackup $sqliteDatabaseBackup, string $kernelCacheDir)
    {
        $this->sqliteDatabaseBackup = $sqliteDatabaseBackup;
        $this->kernelCacheDir       = $kernelCacheDir;
    }

    public function init(array $metadatas, array $classNames, bool $append = false): void
    {
        $this->sqliteDatabaseBackup->init($metadatas, $classNames);
    }

    public function getBackupFilePath(): string
    {
        return $this->sqliteDatabaseBackup->getBackupFilePath();
    }

    public function isBackupActual(): bool
    {
        return $this->sqliteDatabaseBackup->isBackupActual();
    }

    public function backup(AbstractExecutor $executor): void
    {
        $this->sqliteDatabaseBackup->backup($executor);
    }

    public function restore(AbstractExecutor $executor, array $excludedTables = []): void
    {
        $this->sqliteDatabaseBackup->restore($executor, $excludedTables);
    }
}