<?php
/*
 *  Copyright (c) 2008-2015 by Afilias Technologies Limited (dotMobi). All rights reserved.
 */

//namespace Mobi_Mtld_DA;

/**
 * Contains a property name and the expected data type of values associated with
 * this name.
 *
 * @copyright Copyright (c) 2008-2015 by Afilias Technologies Limited (dotMobi). All rights reserved.
 * @author Afilias Technologies Ltd
 */
class Mobi_Mtld_DA_PropertyName {

    private $name;
    private $dataTypeId;

    /**
     * Create a new PropertyName with a name and the expected data type of
     * value assigned to it.
     * @param string name       The name
     * @param byte   dataTypeId The data type
     */
    public function __construct($name, $dataTypeId) {
        $this->name       = $name;
        $this->dataTypeId = $dataTypeId;
    }

    /**
     * Get the name of this PropertyName.
     * @return string The name of the PropertyName
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Get the data type ID for values associated with this Property name.
     * @return int The ID of the data type
     */
    public function getDataTypeId() {
        return $this->dataTypeId;
    }

    /**
     * Get the data type name for values associated with this Property name.
     * @return string The name of the data type
     */
    public function getDataType() {
        return Mobi_Mtld_DA_DataType::getName($this->dataTypeId);
    }

    /**
     * Returns a string to present this object.
     * @return string (data type)
     */
    public function toString() {
        return '(' . $this->getDataType() . ')';
    }

    /**
     * Returns a string to present this object.
     * @return (data type)
     */
    public function __toString() {
        return $this->toString();
    }

    /**
     * Compare two instances of this class.
     * If both have equal values and data type then returns true.
     * @param Mobi_Mtld_DA_PropertyName obj object to be compared against this
     * @return bool true=equals
     */
    public function equals($obj) {
        return $obj              !== null
            && get_class($obj)   === 'Mobi_Mtld_DA_PropertyName'
            && $this->dataTypeId === $obj->getDataTypeId()
            && $this->name       === $obj->getName();
    }
}
