<?php
/*
 * Copyright (c) 2008-2015 by Afilias Technologies Limited (dotMobi). All rights reserved.
 */

//namespace Mobi_Mtld_DA_Carrier;

require_once Mobi_Mtld_DA_CARRIER_API_PATH.'ByteReader.php';
require_once Mobi_Mtld_DA_CARRIER_API_PATH.'BucketHandler.php';
require_once Mobi_Mtld_DA_CARRIER_API_PATH.'BucketType.php';

/**
 * This class is responsible for loading the data file and walking the IPV4 Radix 
 * Trie to find the properties for a given User-Agent.
 * 
 * @copyright Copyright (c) 2008-2015 by Afilias Technologies Limited (dotMobi). All rights reserved.
 * @author Afilias Technologies Ltd
 */
class Mobi_Mtld_DA_Carrier_CarrierData {
    /** Data file component */
    private static $MAGIC_NUMBER = 'DA';
    /** Data file component */
    private static $FILE_ID      = 1;

    /** magic number [2B] + file id [1B] + header length [2B] */
    private static $START_BYTES_LEN        = 5;
    /** e.g. 2013-08-07T15:36:44+0000 */
    private static $CREATION_DATE_LEN      = 24;
    /** bucket ID [2B], bucket CRC-32 [4B], bucket length [4B] */
    private static $BUCKET_START_BYTES_LEN = 10;

    /** Data file copyright tag */
    private $copyright;
    /** Data file creation date */
    private $creationDate;
    /** Data file version */
    private $version;
    /** Data cursor */
    private $cursor;

    /** Data for the IPv4 radix tree */
    private static $NULL_PTR     = -1;
    /** Starting pointer */
    private static $ROOT_PTR     = 0;
    /** MAX_IPV4_BIT */
    private static $MAX_IPV4_BIT = 0x80000000;
    /** Trie component */
    private $treeLefts;
    /** Trie component */
    private $treeRights;
    /** Trie component */
    private $treeProperties;
    /** Property Names */
    private $propertyNames       = null;
    /** Property String Names */
    private $propertyStringNames = null;

    /** Error message */
    private static $PROBLEM_READING_DATA_FILE = 'Problem reading data file.';
    /** Error message */
    private static $INVALID_DATA_FILE         = 'Invalid data file.';

    /** Carrier data */
    private $data;

    /**
     * Load carrier data file.
     * @param string filePath carrier data file
     * @throws Mobi_Mtld_DA_Exception_DataFileException
     *         Thrown when the data file can not be opened
     */
    public function loadDataFromFile($filePath) {
        require_once Mobi_Mtld_DA_COMMON_API_PATH.'Exception/DataFileException.php';

        try {
            $this->data = @file_get_contents($filePath);
            if ($this->data === false) {
                throw new Mobi_Mtld_DA_Exception_DataFileException(self::$PROBLEM_READING_DATA_FILE);
            }
            $this->readHeader();
            $this->readBuckets();
        } catch (RuntimeException $ex) {
            throw new Mobi_Mtld_DA_Exception_DataFileException(self::$PROBLEM_READING_DATA_FILE, null, $ex);
        }
    }

    /**
     * The header of the file contains the following data:
     * 
     *    2B  DA (US-ASCII)
     *    1B  File type ID (1: carrier data, 2:some other data.... etc)
     *    2B  Header length - the total size of the header including the preceding bytes
     *    2B  Length of copyright text
     *    ?B  Copyright (US-ASCII) "(c) Copyright 2013 - Afilias Technologies Ltd"
     *    24B Creation date (US-ASCII) "2013-08-07T15:36:44+0000"
     *    1B  Version, major
     *    1B  Version, minor
     *    4B  Licence ID
     *    4B  CRC-32 - all data after first bucket offset
     * 
     * @throws IOException
     * @throws EOFException
     * @throws Mobi_Mtld_DA_Exception_DataFileException
     */
    private function readHeader() {
        // firstly read first $START_BYTES_LEN bytes to get the magic number, file ID and header length
        $headerLength = $this->checkFileTypeGetHeaderLength(
            substr($this->data, 0, self::$START_BYTES_LEN)
        );
        // read the rest of header - note headlength contains the total size of the header
        $reader = new Mobi_Mtld_DA_Carrier_ByteReader(
            substr(
                $this->data,
                self::$START_BYTES_LEN,
                $headerLength - self::$START_BYTES_LEN 
            )
        );
        // set data cursor to the byte after header data
        $this->cursor = $headerLength;

        // fetch data from header
        $this->copyright    = $reader->getStringAscii($reader->getShort());
        $this->creationDate = $reader->getStringAscii(self::$CREATION_DATE_LEN);
        $this->version      = $reader->getByte() . '.' . $reader->getByte();
        // unused - just reading the int to make sure its ok
        $licenceId = $reader->getInt();
    }

    /**
     * Check the first few bytes to make sure we are opening a Carrier Identification
     * file. Also get the length of the header.
     * 
     * @param string startBytes Header bytes
     * @return int header length
     * @throws Mobi_Mtld_DA_Exception_DataFileException
     */
    private function checkFileTypeGetHeaderLength($startBytes) {
        $reader     = new Mobi_Mtld_DA_Carrier_ByteReader($startBytes);
        $fileMagic  = $reader->getStringAscii(2);
        $fileTypeId = $reader->getByte();
        
        if ($fileMagic !== self::$MAGIC_NUMBER || $fileTypeId !== self::$FILE_ID) {
            throw new Mobi_Mtld_DA_Exception_DataFileException(self::$INVALID_DATA_FILE);
        }

        return $reader->getShort();
    }

    /**
     * Each bucket is comprised of the following. The BucketHandler is
     * responsible for actually parsing the data in each bucket. This method
     * keeps reading until either the end of the file or until all necessary
     * buckets have been read. It will skip buckets with IDs it does not
     * recognise to hopefully future proof the API against possible additions to
     * the data file
     * 
     * Bucket structure:
     * 
     *    2B  Bucket ID
     *    4B  CRC-32 checksum - NOTE: unsigned int!
     *    4B  Length of the data
     *    ?B  Data
     * 
     * @throws IOException
     * @throws EOFException
     * @throws Mobi_Mtld_DA_Exception_DataFileException
     */
    private function readBuckets() {
        $bucketHandler = new Mobi_Mtld_DA_Carrier_BucketHandler();
        $cursor        = $this->cursor;
        $data          = &$this->data;

        while ($bucketHandler->needsBuckets()) {
            // read the bucket header to get the bucket ID, length and CRC32
            $reader = new Mobi_Mtld_DA_Carrier_ByteReader(
                substr($data, $cursor, self::$BUCKET_START_BYTES_LEN)
            );

            $cursor  += self::$BUCKET_START_BYTES_LEN;
            $bucketId = $reader->getShort();
            $crc32    = $reader->getIntUnsigned();
            $length   = $reader->getInt();

            if (Mobi_Mtld_DA_Carrier_BucketType::isValidId($bucketId)) {
                $bucketHandler->processBucket(
                    $bucketId,
                    $crc32,
                    substr($data, $cursor, $length)
                );
                $cursor += $length;
                
            } else {
                $cursor += $length;
            }
        }

        // done reading...
        // get the radix tree and properties to be used for detection
        $this->treeLefts      = $bucketHandler->getTreeLefts();
        $this->treeRights     = $bucketHandler->getTreeRights();
        $this->treeProperties = $bucketHandler->getTreeProperties();
        // used by the propertyNameExists() method in CarrierApi
        $this->propertyStringNames = $bucketHandler->getPropertyNamesAsStrings();
        $this->propertyNames       = $bucketHandler->getPropertyNames();;
    }

   /**
     * Selects a value for a given IPv4 address, traversing tree and choosing
     * most specific value available for a given address.
     * @param int|string key IPv4 address to look up in integer or string form
     * @return mixed value at most specific IPv4 network in a tree for a given IPv4
     * address
     */
    public function getProperties($key) {

        if (is_string($key) && !preg_match('/^[0-9]+$/', $key)) {
            $key = ip2long($key);
            if ($key === false) {
                return null;
            }
            $key = sprintf('%u', $key);
        }
        if ($key === null) {
            return null;
        }

        $bit   = self::$MAX_IPV4_BIT;
        $value = null;
        $node  = self::$ROOT_PTR;

        $treeLefts      = &$this->treeLefts;
        $treeRights     = &$this->treeRights;
        $treeProperties = &$this->treeProperties;

        while ($node !== self::$NULL_PTR) {
            if ($treeProperties[$node] !== null) {
                // no breaking - continue to the max depth
                $value = $treeProperties[$node];
            }
            $node = (($key & $bit) !== 0)? $treeRights[$node]: $treeLefts[$node];
            $bit = (int)($bit / 2);
        }

        return $value;
    }

    /**
     * Return a list of all the property names.
     * @return array array of property names
     */
    public function getPropertyNames() {
        return $this->propertyNames;
    }

    /**
     * Return a list of all the property names.
     * @return string of property names
     */
    public function getPropertyNamesAsStrings() {
        return $this->propertyStringNames;
    }

    /**
     * Get data file copyright.
     * @return string the copyright
     */
    public function getCopyright() {
        return $this->copyright;
    }

    /**
     * Get data file creation date.
     * @return string the creationDate
     */
    public function getCreationDate() {
        return $this->creationDate;
    }

    /**
     * Get data file version.
     * @return string the version
     */
    public function getVersion() {
        return $this->version;
    }
}
