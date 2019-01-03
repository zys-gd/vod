<?php

namespace DeviceDetectionBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use DeviceDetectionBundle\Service\Device;
use DeviceDetectionBundle\Exceptions\CommandException;

class OptimizeDatabaseCommand extends Command
{
    /**
     * The path to the optimizer script.
     */
    const PHP_OPTIMIZER_PATH = __DIR__ . '/../Resources/lib/ExtraTools/DeviceApiCache/data-file-optimizer.php';

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('devicedetection:db:optimize')
             ->setDescription('Optimizes device detection database.')
             ->setHelp('The optimizer breaks the data file into smaller parts and caches them on the disk. The API
            will use the cached data instead and will only lead the data needed for a certain lookup into the memory.');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Clearing previous optimization');

        $this->_clear();

        $output->writeln('Optimizing database');

        $this->_optimize();

        $output->writeln('Done');
    }

    /**
     * Creates an optimised version of the database which can then be used by the api.
     * @throws CommandException
     */
    protected function _optimize()
    {
        if (!file_exists(Device::DATABASE_FILE_PATH)) {

            throw new CommandException('No database found to be optimized, please run "update" first');
        }

        if (!file_exists(Device::DATABASE_OPTIMIZED_PATH) && !mkdir(Device::DATABASE_OPTIMIZED_PATH, 0777, true)) {

            throw new CommandException('Could not create optimized directory');
        }

        $cmd = 'php %1$s -d %2$s -t %3$s';
        $cmd = sprintf($cmd, static::PHP_OPTIMIZER_PATH, Device::DATABASE_FILE_PATH, Device::DATABASE_OPTIMIZED_PATH);

        $this->_run($cmd, $output, $code);

        if ($code !== 0) {

            throw new CommandException('Optimization failed: ' . $output);
        }
    }

    /**
     * Removes the previously created optimized version of the database.
     */
    protected function _clear()
    {
        $cmd = 'rm -rf %1$s';
        $cmd = sprintf($cmd, Device::DATABASE_OPTIMIZED_PATH);

        $this->_run($cmd, $output, $code);

        if ($code !== 0) {

            throw new CommandException('Could not clean the previously optimized database: ' . $output);
        }
    }

    /**
     * Executes the supplied command.
     * @param $cmd
     * @param null $output
     * @param null $code
     */
    protected function _run($cmd, &$output = null, &$code = null)
    {
        exec($cmd, $output, $code);
    }
}