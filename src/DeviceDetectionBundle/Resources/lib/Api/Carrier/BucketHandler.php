<?php
/*
 * Copyright (c) 2008-2015 by Afilias Technologies Limited (dotMobi). All rights reserved.
 */

//namespace Mobi_Mtld_DA_Carrier;

require_once Mobi_Mtld_DA_CARRIER_API_PATH.'BucketType.php';
require_once Mobi_Mtld_DA_CARRIER_API_PATH.'CarrierDataType.php';
require_once Mobi_Mtld_DA_COMMON_API_PATH.'DataType.php';
require_once Mobi_Mtld_DA_COMMON_API_PATH.'PropertyName.php';
require_once Mobi_Mtld_DA_COMMON_API_PATH.'Property.php';
require_once Mobi_Mtld_DA_COMMON_API_PATH.'Properties.php';

/**
 * A bucket is a section of the data file. It has an ID, length and a byte array
 * of data. Each bucket type has a specific format. This class handles the
 * conversion of bucket data(binary bytes) to actual values.
 * 
 * The following buckets are handled:<br/>
 *   - Property Names   - a unique list of property names. The order is the index.<br/>
 *   - Property Values  - a unique list of property values. The order is the index.<br/>
 *   - Properties       - collections of property name IDs to property value IDs<br/>
 *   - IPv4 Radix Tree  - contains the data for the left/right branches of a tree
 *                        and property collection IDs<br/>
 * 
 * The bucket handler expects the buckets to be in the above order.
 * 
 * @copyright Copyright (c) 2008-2015 by Afilias Technologies Limited (dotMobi). All rights reserved.
 * @author Afilias Technologies Ltd
 */
class Mobi_Mtld_DA_Carrier_BucketHandler extends Mobi_Mtld_DA_DataType {
    /** for internal api use */
    const NO_VALUE            = -1;
    /** for internal api use */
	const NO_CONTAINER        = 0;
    /** for internal api use */
	const ORDER_SET_CONTAINER = 1;

    /** CRC Error message */
    private static $CRC32_DOES_NOT_MATCH = 'CRC-32 does not match for bucket "%s".';
    /** [PropertyName] */
    private $propertyNames       = null;
    /** [string] */
    private $propertyStringNames = null;
    /** [Property] */
    private $propertyValues      = null;
    /** [Properties] */
    private $properties          = null;
    /** [int] */
    private $treeLefts           = null;
    /** [int] */
    private $treeRights          = null;
    /** [Properties] */
    private $treeProperties      = null;

    /**
     * Checks if all the necessary buckets have been supplied and processed.
     * @return bool true if all buckets complete, FALSE otherwise
     */
    public function needsBuckets() {
        return $this->propertyNames  === null
            || $this->propertyValues === null
            || $this->properties     === null
            || $this->treeLefts      === null;
    }

    /**
     * Returns the Radix Trie "left" pointers
     * @return array Radix Trie "left" pointers
     */
    public function getTreeLefts() {
        return $this->treeLefts;
    }

    /**
     * Returns the Radix Trie "right" pointers
     * @return array Radix Trie "right" pointers
     */
    public function getTreeRights() {
        return $this->treeRights;
    }

    /**
     * Returns the properties used in the Radix Trie nodes
     * @return array Radix Trie nodes
     */
    public function getTreeProperties() {
        return $this->treeProperties;
    }

    /**
     * Returns the property names array
     * @return [PropertyNames]
     */
    public function getPropertyNames() {
        return $this->propertyNames;
    }

    /**
     * Returns the property names array
     * @return Property names as string
     */
    public function getPropertyNamesAsStrings() {
        return $this->propertyStringNames;
    }

    /**
     * Process a bucket identified by "bucketId". The bucket CRC-32 hash is 
     * verified before parsing the bucket data.
     * 
     * @param int bucketId Bucket ID
     * @param string fileCrc32 CRC number from the file
     * @param string bucketData Data
     * @throws Mobi_Mtld_DA_Exception_DataFileException 
     */
    public function processBucket($bucketId, $fileCrc32, $bucketData) {
        if ($fileCrc32 !== crc32($bucketData)) {
            require_once Mobi_Mtld_DA_COMMON_API_PATH.'Exception/DataFileException.php';
            throw new Mobi_Mtld_DA_Exception_DataFileException(
                sprintf(self::$CRC32_DOES_NOT_MATCH, $bucketId)
            );
        }
        if ($bucketId === Mobi_Mtld_DA_Carrier_BucketType::PROPERTY_NAMES) {
            $this->processPropertyNamesBucket($bucketData);
        } elseif ($bucketId === Mobi_Mtld_DA_Carrier_BucketType::PROPERTY_VALUES) {
            $this->processPropertyValuesBucket($bucketData);
        } elseif ($bucketId === Mobi_Mtld_DA_Carrier_BucketType::PROPERTIES) {
            $this->processPropertiesBucket($bucketData);
        } elseif ($bucketId === Mobi_Mtld_DA_Carrier_BucketType::IPV4_TREE) {
            $this->processIpv4TreeBucket($bucketData);
        }
    }

    /**
     * The following is the structure of this bucket:
     *<pre>
     *    2B Num of indexed items
     *    <repeating>
     *        1B data type of property value
     *        1B length of name
     *        ?B property name - ascii string
     *    </repeating>
     *</pre>
     * The order of the properties is taken as the index for each item.
     */
    private function processPropertyNamesBucket($data) {
        $reader              = new Mobi_Mtld_DA_Carrier_ByteReader($data);
        $numItems            = $reader->getShort();
        $propertyNames       = array();
        $propertyStringNames = array();

        for ($i=0; $i<$numItems; $i++) {
            $valueDataType           = $reader->getByte();
            $name                    = $reader->getStringAscii($reader->getByte());
            $propertyNames[$i]       = new Mobi_Mtld_DA_PropertyName($name, $valueDataType);
            $propertyStringNames[$i] = $name;
        }

        $this->propertyNames       = $propertyNames;
        $this->propertyStringNames = $propertyStringNames;
    }

    /**
     * The following is the structure of this bucket:
     *<pre>
	 *    2B   Number of indexed items
	 *    <repeating>
     *       
	 *     1B   container type ID: "no container", "ordered set" etc
	 *     <if container="no container">
	 *        1B        property type - int, boolean, string etc
	 *        1B/2B/4B  length of value bytes --OPTIONAL-- (only applies to some string types)
	 *        ?B        the converted value, some data types have a fixed length such as (boolean len=1, byte len=1, short len=2, int len=4, float len=4)
	 *     </if>
	 * 
	 *     <elseif container="ordered set">
	 *       1B   property type - int, boolean, string etc
	 *       2B   number of items in the set
	 *       <repeat>
	 *         <if type=string>
	 *           1B        property type - the type of string - 
	 *           1B/2B/4B  length of value bytes --OPTIONAL-- (only applies to some string types)
	 *         </if>
	 *         ?B   the converted value, some data types have a fixed length such as (boolean len=1, byte len=1, short len=2, int len=4, float len=4)
	 *       </repeat>
	 *     </if>
	 * 
	 *    </repeating>
     * 
     *    2B Num of indexed items
     *    <repeating>
     *        1B   property type
     *        <1B/2B/4B> length of value bytes --OPTIONAL-- 
     *        ?B   the converted value, some data types have a fixed length such as
     *             (bool len=1, byte len=1, short len=2, int len=4, float len=4)
     *    </repeating>
     *</pre>
     * The order of the items is taken as the index for each item.
     */
    private function processPropertyValuesBucket($data) {
        $reader         = new Mobi_Mtld_DA_Carrier_ByteReader($data);
        $numItems       = $reader->getShort();
        $propertyValues = array();

        for ($i=0; $i<$numItems; $i++) {
            $property = null;
            $containerType = $reader->getByte();

            // get a property with a single value
            if ($containerType === self::NO_CONTAINER) {
                $dataType = $reader->getByte();
                $propertyValues[] = new Mobi_Mtld_DA_Property(
                    $this->getPropertyValue($dataType, $reader),
                    Mobi_Mtld_DA_Carrier_CarrierDataType::getBaseDataType($dataType)
                );

            // read the set of properties
            } elseif ($containerType === self::ORDER_SET_CONTAINER) {
                $dataType  = $reader->getByte();
                $numValues = $reader->getShort();
                $values    = array();

                for ($j=0; $j<$numValues; $j++) {
                    $values[] = $this->getPropertyValue(
                        $dataType === Mobi_Mtld_DA_DataType::STRING? 
                            $reader->getByte(): $dataType,
                        $reader
                    );
                }

                $propertyValues[] = new Mobi_Mtld_DA_Property(
                    $values,
                    Mobi_Mtld_DA_Carrier_CarrierDataType::getBaseDataType($dataType)
                );
            }
        }

        $this->propertyValues = $propertyValues;
    }

    /**
     * The following is the structure of this bucket:
     *<pre>
     *    2B   Num of indexed items
     *    <repeating>
     *        2B num items in collection
     *           <repeating>
     *            4B    property name ID
     *            4B    property value ID
     *        </repeating>
     *    </repeating>
     *</pre>
     * The order of the properties is taken as the index for each item. As each
     * item of the collection is loaded it is looked up in the previously loaded
     * property names and property values IDs.
     * 
     * @throws IOException 
     */
    private function processPropertiesBucket($data) {
        $reader         = new Mobi_Mtld_DA_Carrier_ByteReader($data);
        $numItems       = $reader->getShort();
        $properties     = array();
        $propertyNames  = &$this->propertyNames;
        $propertyValues = &$this->propertyValues;

        for ($i=0; $i<$numItems; $i++) {
            $props = new Mobi_Mtld_DA_Properties();
            $numPropVals = $reader->getShort();

            for ($s=0; $s<$numPropVals; $s++) {
                $propId = $reader->getInt();
                $valId  = $reader->getInt();

                if (isset($propertyNames[$propId])) {
                    $propName = $propertyNames[$propId];
                    $props->put(
                        $propName->getName(),
                        isset($propertyValues[$valId])? $propertyValues[$valId]: null
                    );
                }
            }
            $properties[$i] = $props;
        }

        $this->properties = $properties;
    }

    /**
     * Load the data for the IPv4 Tree bucket. This bucket has the following
     * structure:
     *<pre>
     * These 3 ints repeat for the entire bucket:
     *     <repeating>
     *        4B properties ID value
     *        4B Left value
     *        4B Right value
     *     </repeating>
     *</pre>
     */
    private function processIpv4TreeBucket($data) {
        $reader         = new Mobi_Mtld_DA_Carrier_ByteReader($data);
        $size           = strlen($data) / 12; // each of left/right/value is 4 bytes...
        $treeLefts      = array();
        $treeRights     = array();
        $treeProperties = array();
        $properties     = &$this->properties;

        for ($i=0; $i<$size; $i++) {
            $propsId        = $reader->getInt();
            $treeLefts[$i]  = $reader->getInt();
            $treeRights[$i] = $reader->getInt();
            // lookup the properties object by its ID
            $treeProperties[$i] =
                $propsId === self::NO_VALUE? null:
                    (isset($properties[$propsId])? $properties[$propsId]: null);
        }

        $this->treeLefts      = $treeLefts;
        $this->treeRights     = $treeRights;
        $this->treeProperties = $treeProperties;
    }

    /**
     * Read the appropriate property from the ByteReader depending on the data 
     * type. All of the primitive types are fixed length. In addition there are
     * five fixed length UTF8 string values and other special types for strings
     * that are less than certain lengths.
     * 
     * @param int dataType byte
     * @param Mobi_Mtld_DA_Carrier_ByteReader reader ByteReader
     * @return mixed Property value
     */
    private function getPropertyValue($dataType, $reader) {
        $value = null;

        if ($dataType === Mobi_Mtld_DA_DataType::BOOLEAN) {
            $value = $reader->getBoolean();
        } elseif ($dataType === Mobi_Mtld_DA_DataType::BYTE) {
            $value = $reader->getByte();
        } elseif ($dataType === Mobi_Mtld_DA_DataType::SHORT) {
            $value = $reader->getShort();
        } elseif ($dataType === Mobi_Mtld_DA_DataType::INTEGER) {
            $value = $reader->getInt();
        } elseif ($dataType === Mobi_Mtld_DA_DataType::LONG) {
            $value = $reader->getLong();
        } elseif ($dataType === Mobi_Mtld_DA_DataType::FLOAT) {
            $value = $reader->getFloat();
        } elseif ($dataType === Mobi_Mtld_DA_DataType::DOUBLE) {
            $value = $reader->getDouble();
        } elseif ($dataType === Mobi_Mtld_DA_Carrier_CarrierDataType::STRING_1_BYTE_FIXED) {
            $value = $reader->getStringUtf8(1);
        } elseif ($dataType === Mobi_Mtld_DA_Carrier_CarrierDataType::STRING_2_BYTE_FIXED) {
            $value = $reader->getStringUtf8(2);
        } elseif ($dataType === Mobi_Mtld_DA_Carrier_CarrierDataType::STRING_3_BYTE_FIXED) {
            $value = $reader->getStringUtf8(3);
        } elseif ($dataType === Mobi_Mtld_DA_Carrier_CarrierDataType::STRING_4_BYTE_FIXED) {
            $value = $reader->getStringUtf8(4);
        } elseif ($dataType === Mobi_Mtld_DA_Carrier_CarrierDataType::STRING_5_BYTE_FIXED) {
            $value = $reader->getStringUtf8(5);
        } elseif ($dataType === Mobi_Mtld_DA_Carrier_CarrierDataType::STRING_LEN_BYTE) {
            $value = $reader->getStringUtf8($reader->getByte());
        } elseif ($dataType === Mobi_Mtld_DA_Carrier_CarrierDataType::STRING_LEN_SHORT) {
            $value = $reader->getStringUtf8($reader->getShort());
        } elseif ($dataType === Mobi_Mtld_DA_Carrier_CarrierDataType::STRING_LEN_INT) {
            $value = $reader->getStringUtf8($reader->getInt());
        } elseif ($dataType === Mobi_Mtld_DA_DataType::STRING) {
            $value = $reader->getStringUtf8($reader->getShort());
        } else {
            $reader->skip($reader->getShort());
        }

        return $value;
    }
}
