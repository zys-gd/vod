<?php

namespace DeviceDetectionBundle\Command;

use Exception;
use ZipArchive;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;

use DeviceDetectionBundle\Service\Device;
use DeviceDetectionBundle\Exceptions\ParameterException;
use DeviceDetectionBundle\Exceptions\CommandException;
use DeviceDetectionBundle\Exceptions\LicenseException;

class UpdateDatabaseCommand extends ContainerAwareCommand
{
    /**
     * Address from which you can get the new database.
     */
    const DATABASE_DOWNLOAD_URL = 'https://deviceatlas.com/getJSON.php?licencekey=%1$s&format=zip';

    /**
     * How much memory should be used when downloading and writing the new database.
     */
    const READ_WRITE_BYTES = 1024 * 8;

    /**
     * Text displayed on the web page when the license is invalid.
     */
    const ERROR_LICENSE = 'There was an error: Licence key not valid';

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('devicedetection:db:update')
             ->setDescription('Updates device detection database.')
             ->setHelp('Downloads the latest device detection database from the supplier.');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Downloading database');

        try {
            $this->_downloadDatabase();

        } catch( LicenseException $error ){

            $output->writeln('License is invalid or expired');

            return;

        } catch( CommandException $error ){

            $output->writeln('Command failed with message ' . $error->getMessage());

            return;
        }

        $output->writeln('Extracting database');

        $this->_unzipDatabase();

        $output->writeln('Applying database');

        $this->_applyDatabase();

        $output->writeln('Done');
    }

    /**
     * Returns the path where the new database will be downloaded.
     * @return string
     */
    protected function _getDownloadPath()
    {
        return dirname(Device::DATABASE_FILE_PATH) . '/new/db.zip';
    }

    /**
     * Returns the url from where you can download the new device detection database.
     * @return string
     * @throws ParameterException
     */
    protected function _getDownloadUrl()
    {
        if (!$this->getContainer()->hasParameter('license_key')) {

            throw new ParameterException('Missing "license_key" parameter');
        }

        $license = $this->getContainer()->getParameter('license_key');

        return sprintf(static::DATABASE_DOWNLOAD_URL, $license);
    }

    /**
     * Downloads datbase from the supplied url into the provided path.
     * @throws CommandException
     */
    protected function _downloadDatabase()
    {
        $url  = $this->_getDownloadUrl();
        $path = $this->_getDownloadPath();
        $dir  = dirname($path);

        try {

            $remoteFile = @fopen($url, 'r');

        } catch( Exception $error ){

            throw new CommandException('Could not contact the provider');
        }

        if (!$remoteFile) {

            throw new CommandException('Could not read the remote database');
        }

        if (fread($remoteFile, static::READ_WRITE_BYTES) == static::ERROR_LICENSE) {

            throw new LicenseException;
        }

        if (!file_exists($dir) && !mkdir($dir, 0777, true)) {

            throw new CommandException('Could not create folder structure');
        }

        $localFile  = fopen($path, 'w');

        if (!$localFile) {

            throw new CommandException('Could not write the local database');
        }

        while (!feof($remoteFile)) {

            fwrite($localFile, fread($remoteFile, static::READ_WRITE_BYTES), static::READ_WRITE_BYTES);
        }

        fclose($remoteFile);
        fclose($localFile);
    }

    /**
     * Extracts the database zip file.
     * @throws CommandException
     */
    protected function _unzipDatabase()
    {
        $downloadPath = $this->_getDownloadPath();
        $unzipDir     = dirname($downloadPath);
        $zip          = new ZipArchive;

        if ($zip->open($downloadPath) !== true) {

            throw new CommandException('Could not open zip file');
        }

        if (!$zip->extractTo($unzipDir)) {

            throw new CommandException('Could not extract zip file');
        }

        if (!$zip->close()) {

            throw new CommandException('Could not close zip file');
        }
    }

    /**
     * Replaces the old database with the new database.
     * @throws CommandException
     */
    protected function _applyDatabase()
    {
        $downloadPath = $this->_getDownloadPath();
        $downloadDir  = dirname($downloadPath);
        $files        = scandir($downloadDir);

        $newDbFilePath = '';

        foreach ($files as $name) {

            if (substr($name, -4) != 'json') {
                continue;
            }

            $newDbFilePath = $downloadDir . '/' . $name;

            break;
        }

        if (!$newDbFilePath) {

            throw new CommandException('Could not identify the newly downloaded database');
        }

        if (!rename($newDbFilePath, Device::DATABASE_FILE_PATH)) {

            throw new CommandException('Could not apply changes to database');
        }

        if (!unlink($downloadPath) || !rmdir($downloadDir)) {

            throw new CommandException('Could not perform cleanup');
        }
    }
}