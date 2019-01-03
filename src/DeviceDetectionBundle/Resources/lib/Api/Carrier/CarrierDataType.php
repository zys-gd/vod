<?php
/*
 * Copyright (c) 2008-2015 by Afilias Technologies Limited (dotMobi). All rights reserved.
 */

//namespace Mobi_Mtld_DA_Carrier;

require_once Mobi_Mtld_DA_COMMON_API_PATH.'DataType.php';

/**
 * Carrier API specific data types. These are used to optimise the data in the 
 * data file.
 * 
 * These data types are internal and are mapped to the DataType::STRING_UTF8 that
 * is exposed to the customer.
 * 
 * @copyright Copyright (c) 2008-2015 by Afilias Technologies Limited (dotMobi). All rights reserved.
 * @author Afilias Technologies Ltd
 */
class Mobi_Mtld_DA_Carrier_CarrierDataType extends Mobi_Mtld_DA_DataType {
    // these are special types - used to optimise adding of string values
    // note - these are for API internal only, they should not be exposed
    /** internal API use */
    const STRING_LEN_BYTE     = 100; // The string's bytes are <= Byte.MAX_LENGTH
    /** internal API use */
    const STRING_LEN_SHORT    = 101; // The string's bytes are <= Byte.MAX_LENGTH
    /** internal API use */
    const STRING_LEN_INT      = 102; // The string's bytes are <= Integer.MAX_LENGTH
    /** internal API use */
    const STRING_1_BYTE_FIXED = 103;
    /** internal API use */
    const STRING_2_BYTE_FIXED = 104;
    /** internal API use */
    const STRING_3_BYTE_FIXED = 105;
    /** internal API use */
    const STRING_4_BYTE_FIXED = 106;
    /** internal API use */
    const STRING_5_BYTE_FIXED = 107;

    /**
     * used to quickly find if an ID is a special string type. If we ever add
     * additional IDs that are not sequential we will have to modify this.
     */
    private static $START_STRING_ID = self::STRING_LEN_BYTE;
    /**
     * used to quickly find if an ID is a special string type. If we ever add
     * additional IDs that are not sequential we will have to modify this.
     */
    private static $END_STRING_ID   = self::STRING_5_BYTE_FIXED;

    /**
     * Get the base data type for the given CarrierDataType. This essentially
     * just converts the above special String types to the DataType.String type.
     * @param int dataTypeID Data type from this class
     * @return int Data type from DataType
     */
    public static function getBaseDataType($dataTypeID) {
        if ($dataTypeID >= self::$START_STRING_ID && $dataTypeID <= self::$END_STRING_ID) {
            $dataTypeID = Mobi_Mtld_DA_DataType::STRING;
        }
        return $dataTypeID;
    }
}
