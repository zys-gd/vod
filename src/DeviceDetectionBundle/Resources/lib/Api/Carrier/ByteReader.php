<?php
/*
 * Copyright (c) 2008-2015 by Afilias Technologies Limited (dotMobi). All rights reserved.
 */

//namespace Mobi_Mtld_DA_Carrier;

/**
 * This class gets a chunk of binary data (bytes) then the methods are used to
 * read data segments based on the data of data being read from the chunk, the
 * read pointer will advance to the next bytes.
 *
 * All numeric values must be read in little endian byte order.
 * All numeric data is signed unless the method specifies otherwise - e.g. getIntUnsigned()
 * 
 * @copyright Copyright (c) 2008-2015 by Afilias Technologies Limited (dotMobi). All rights reserved.
 * @author Afilias Technologies Ltd
 */
class Mobi_Mtld_DA_Carrier_ByteReader {
    /** Data buffer */
    private $buff;
    /** Pointer position */
    private $position = 0;

    /**
     * Creates instance.
     * @param string data A string of data
     */
    public function __construct($data) {
        $this->buff = $data;
    }

    /**
     * Skip bytes.
     * @param int lenShort number of bytes to skip
     */
    public function skip($lenShort)
    {
        $this->position += $lenShort;
    }

    /**
     * Read one byte.
     * @return string Read data
     */
    public function getByte()
    {
        return (int)implode('', unpack('c', $this->buff[$this->position++]));
    }

    /**
     * Read 1byte as boolean.
     * @return bool Read data
     */
    public function getBoolean() {
        return $this->getByte() === 1;
    }

    /**
     * Read 2bytes as short.
     * @return int Read data
     */
    public function getShort()
    {
        $pos = $this->position;
        $this->position += 2;
        return (int)implode('', unpack('v', substr($this->buff, $pos, 2)));
    }

    /**
     * Read 4bytes as integer.
     * @return int Read data
     */
    public function getInt()
    {
        $pos = $this->position;
        $this->position += 4;
        return (int)implode('', unpack('i', substr($this->buff, $pos, 4)));
    }

    /**
     * Read 4bytes as unsigned integer.
     * @return int Read data
     */
    public function getIntUnsigned()
    {
        $pos = $this->position;
        $this->position += 4;
        return (int)implode('', unpack('L', substr($this->buff, $pos, 4)));
    }

    /**
     * Read
     * @return int Read data
     */
    public function getLong()
    {
        $pos = $this->position;
        $this->position += 8;
        return (int)implode('', unpack('l', substr($this->buff, $pos, 8)));
    }

    /**
     * Read 4 bytes as float.
     * @return float Read data
     */
    public function getFloat()
    {
        $pos = $this->position;
        $this->position += 4;
        return (int)implode('', unpack('f', substr($this->buff, $pos, 4)));
    }

    /**
     * Read 8bytes as double.
     * @return double Read data
     */
    public function getDouble()
    {
        $pos = $this->position;
        $this->position += 8;
        return (int)implode('', unpack('g', substr($this->buff, $pos, 8)));
    }

    /**
     * Read n bytes.
     * @param int length Number of bytes to read
     * @return int Read data
     */
    public function getBytes($length)
    {
        $pos = $this->position;
        $this->position += $length;
        return unpack('c*', substr($this->buff, $pos, $length));
    }

    /**
     * Read n bytes as string.
     * @param int length Number of bytes to read
     * @return string Read data
     */
    public function getStringAscii($length)
    {
        $pos = $this->position;
        $this->position += $length;
        return substr($this->buff, $pos, $length);
    }

    /**
     * Read n bytes as UTF8 string.
     * @param int length Number of bytes to read
     * @return string Read data
     */
    public function getStringUtf8($length)
    {
        $pos = $this->position;
        $this->position += $length;
        return substr($this->buff, $pos, $length);
    }
}
