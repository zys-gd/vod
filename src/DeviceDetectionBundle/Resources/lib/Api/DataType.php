<?php
/*
 *  Copyright (c) 2008-2015 by Afilias Technologies Limited (dotMobi). All rights reserved.
 */

//namespace Mobi_Mtld_DA;

/**
 * The data types for various properties. Each Property object returned has a
 * getDataType() method.
 *
 * @copyright Copyright (c) 2008-2015 by Afilias Technologies Limited (dotMobi). All rights reserved.
 * @author Afilias Technologies Ltd
 */
class Mobi_Mtld_DA_DataType {
    /** Type boolean */
    const BOOLEAN = 0;
    /** Type byte */
    const BYTE    = 1;
    /** Type short int */
    const SHORT   = 2;
    /** Type int */
    const INTEGER = 3;
    /** Type long int */
    const LONG    = 4;
    /** Type float */
    const FLOAT   = 5;
    /** Type double */
    const DOUBLE  = 6;
    /** Type string */
    const STRING  = 7;
    /** Unknown type */
    const UNKNOWN = 8;

    /** Maps data types to data type names array {byte: string} */
    private static $names = array(
        self::BOOLEAN => 'Boolean',
        self::BYTE    => 'Byte',
        self::SHORT   => 'Short',
        self::INTEGER => 'Integer',
        self::LONG    => 'Long',
        self::FLOAT   => 'Float',
        self::DOUBLE  => 'Double',
        self::STRING  => 'String',
        self::UNKNOWN => 'Unknown',
    );

    /**
     * Get the name of a given data type.
     * @param byte dataTypeID The id to lookup the name for
     * @return string The name of the data type.
     */
    public static function getName($dataTypeID) {
        return isset(self::$names[$dataTypeID])? (string)self::$names[$dataTypeID]: null;
    }
}
