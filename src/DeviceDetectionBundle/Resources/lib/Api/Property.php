<?php
/*
 *  Copyright (c) 2008-2015 by Afilias Technologies Limited (dotMobi). All rights reserved.
 */

//namespace Mobi_Mtld_DA;

require_once dirname(__FILE__).'/DataType.php';

/**
 * Contains a property value. The value can be fetched as a generic Object or
 * one of the convenience asXXXX methods can be used to get the value in a 
 * specific type.
 * 
 * @copyright Copyright (c) 2008-2015 by Afilias Technologies Limited (dotMobi). All rights reserved.
 * @author Afilias Technologies Ltd
 */
class Mobi_Mtld_DA_Property {

    private static $NOT_CONVERTIBLE_TO_BOOLEAN = 'Property is not convertible to a boolean.';
    private static $NOT_CONVERTIBLE_TO_INT     = 'Property is not convertible to an int.';
    private $value;
    private $dataTypeId;

    /**
     * Create a new Property with a value and data type.
     * @param mixed value The value to store
     * @param byte  dataTypeId The type  of the value to store
     */
    public function __construct($value, $dataTypeId) {
        $this->value = $value;
        if ($dataTypeId === 'b') {
            $this->dataTypeId = Mobi_Mtld_DA_DataType::BOOLEAN;
        } elseif ($dataTypeId === 'i') {
            $this->dataTypeId = Mobi_Mtld_DA_DataType::INTEGER;
        } elseif ($dataTypeId === 's') {
            $this->dataTypeId = Mobi_Mtld_DA_DataType::STRING;
        } elseif ($dataTypeId === 'd') {
            $this->dataTypeId = Mobi_Mtld_DA_DataType::DOUBLE;
        } else {
            $this->dataTypeId = $dataTypeId;
        }
    }

    /**
     * Get the type of this property.
     * See Mobi_Mtld_DA_DataType for a full list of data types.
     * @return byte The data type ID of this property.
     */
    public function getDataTypeId() {
        return $this->dataTypeId;
    }

    /**
     * Get the type name of this property.
     * See Mobi_Mtld_DA_DataType for a full list of data types.
     * @return string The data type name of this property.
     */
    public function getDataType() {
        return Mobi_Mtld_DA_DataType::getName($this->dataTypeId);
    }

    /**
     * Get the value of this property. This needs to be cast to the appropriate
     * type such to be used. E.g. value = (int)$property->value();
     * @return The raw property value.
     */
    public function value() {
        return $this->value;
    }

    /**
     * Get the value of the property as a boolean.
     * @return The boolean value of the property.
     * @throws Mobi_Mtld_DA_Exception_IncorrectPropertyTypeException
     *         Thrown if the type of the value is not a Mobi_Mtld_DA_DataType::BOOLEAN
     */
    public function asBoolean() {
        if ($this->dataTypeId !== Mobi_Mtld_DA_DataType::BOOLEAN) {
            require_once dirname(__FILE__).'/Exception/IncorrectPropertyTypeException.php';
            throw new Mobi_Mtld_DA_Exception_IncorrectPropertyTypeException(
                self::$NOT_CONVERTIBLE_TO_BOOLEAN
            );
        }
        return (bool)$this->value;
    }

    /**
     * Get the value of the property as an integer.
     * @return The integer value of the property. 
     * @throws Mobi_Mtld_DA_Exception_IncorrectPropertyTypeException Thrown if
     *         the type of the value is not compatible with a Mobi_Mtld_DA_DataType::INTEGER
     */
    public function asInteger() {
        $type = $this->dataTypeId;
        if (   $type === Mobi_Mtld_DA_DataType::BYTE
            || $type === Mobi_Mtld_DA_DataType::SHORT
            || $type === Mobi_Mtld_DA_DataType::INTEGER) {
                return (integer)$this->value;
        }
        require_once dirname(__FILE__).'/Exception/IncorrectPropertyTypeException.php';
        throw new Mobi_Mtld_DA_Exception_IncorrectPropertyTypeException(
            self::$NOT_CONVERTIBLE_TO_INT
        );
    }

    /**
     * Gets a set of possible values for this property. This is typically only
     * used when it is known that a given property name can have multiple
     * possible values. All items in the set will have the same data type.
     * 
     * @return A set of values.
     */
    public function asSet() {
        return (array)$this->value;
    }

    /**
     * Get the value of the property as string. Alias for asString(). If a 
     * property has multiple possible values then the values are concatenated
     * with a comma.
     * @return The string value of the property.
     */
    public function asString() {
        return $this->toString();
    }

    /**
     * Get the value of the property as string. Alias for asString(). If a 
     * property has multiple possible values then the values are concatenated
     * with a comma.
     * @return The string value of the property
     */
    public function __toString() {
        return $this->toString();
    }

    /**
     * Get the value of the property as string. Alias for asString(). If a 
     * property has multiple possible values then the values are concatenated
     * with a comma.
     * @return The string value of the property
     */
    public function toString() {
        return is_array($this->value)? implode(',', $this->value): (string)$this->value;
    }

    /**
     * Compare two instances of this class.
     * If both have equal values and data type then returns true.
     * @param $obj object to be compared against
     * @return bool Compare result
     */
    public function equals($obj) {
        return $obj              !== null
            && get_class($obj)   === 'Property'
            && $this->dataTypeId === $obj->getDataType()
            && $this->value      === $obj->value();
    }
}
