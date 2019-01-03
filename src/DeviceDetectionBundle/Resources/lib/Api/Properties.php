<?php
/*
 *  Copyright (c) 2008-2015 by Afilias Technologies Limited (dotMobi). All rights reserved.
 */

//namespace Mobi_Mtld_DA;

require_once dirname(__FILE__).'/Property.php';

/**
 * Contains a map of names to Property objects. An instance of this class
 * is returned by getProperties().
 * 
 * @copyright Copyright (c) 2008-2015 by Afilias Technologies Limited (dotMobi). All rights reserved.
 * @author Afilias Technologies Ltd
 */
class Mobi_Mtld_DA_Properties implements IteratorAggregate {

    private $properties = array();

    public function getIterator() {
        return new ArrayIterator($this->properties);
    }

    /**
     * Associates the specified Property with the specified name. If a Property
     * for this name currently exists it will be replaced by the new value.
     * 
     * @param string   name key with which the specified value is to be associated.
     * @param Property property  Property instance - value to be associated with the specified name.
     */
    public function put($name, $property) {
        if ($name) {
            $this->properties[$name] = $property;
        }
    }

    /**
     * Returns the Property to which the specified name is mapped to, or null if
     * there is no mapping for this name.
     * 
     * @param string name the name of the key whose associated Property is to be returned.
     * @return Property The value to which the specified name is mapped to, or null if the map contains no mapping for this name.
     */
    public function get($name) {
        return isset($this->properties[$name])? $this->properties[$name]: null;
    }

    /**
     * Returns true if the Properties contains a mapping for the specified name.
     *
     * @param string name The name of the key whose presence is to be tested
     * @return bool true if a Property exists for the specified key.
     */
    public function containsKey($name) {
        return array_key_exists($name, $this->properties);
    }

    /**
     * Returns true if there are no properties.
     * @return bool true=if no properties exist.
     */
    public function isEmpty() {
        return $this->properties? false: true;
    }

    /**
     * Returns the number of Property objects in this Properties Map.
     * @return int the number of mappings in this map.
     */
    public function size() {
        return count($this->properties);
    }

    /**
     * Get the property names of the property set.
     * @return array Property names
     */
    public function keySet() {
        return array_keys($this->properties);
    }

    /**
     * Serialize properties to JSON.
     * @return string A serialized string of the properties.
     */
    public function toString() {
        $props = array();
        foreach ($this->properties as $name => $property) {
            $props[$name] = $property->value();
        }
        return json_encode($props);
    }

    /**
     * Serialize properties to JSON.
     * @return string A serialized string of the properties.
     */
    public function __toString() {
        return $this->toString();
    }

    /**
     * Check if a property has a specific value.
     *
     * @param string name  Property name
     * @param mixed  value Value to be checked against property's value
     * @return bool true=the property value matched the expected value
     */
    public function contains($name, $value) {
        if (isset($this->properties[$name])) {
            if ($this->properties[$name]->value() === $value) {
                return true;
            }
            if ($this->properties[$name]->getDataTypeId() === Mobi_Mtld_DA_DataType::BOOLEAN) {
                if ($value === true || $value === 1 || $value === '1' || $value === 'true') {
                    return $this->properties[$name]->asBoolean() === true;
                }
                if ($value === false || $value === 0 || $value === '0' || $value === 'false') {
                    return $this->properties[$name]->asBoolean() === false;
                }
                return false;
            }
            if ($this->properties[$name]->getDataTypeId() === Mobi_Mtld_DA_DataType::INTEGER) {
                return $this->properties[$name]->asInteger() === (int)$value;
            }
        }

        return false;
    }

    /**
     * Returns true if the Properties contains a mapping for the specified name.
     *
     * @param string name The name of the key whose presence is to be tested
     * @return bool true if a Property exists for the specified key.
     */
    public function __isset($name) {
        return $this->containsKey($name);
    }

    /**
     * Returns the property value, the data type of the value is set proper data
     * type as defined for the property. If property does not exist null will be
     * returned
     * 
     * @param string name the name of the property
     * @return mixed The property typed value or null
     */
    public function __get($name) {
        $property = $this->get($name);
        if ($property instanceof Mobi_Mtld_DA_Property) {
            $dataTypeId = $property->getDataTypeId();
            if ($dataTypeId === Mobi_Mtld_DA_DataType::BOOLEAN) {
                return $property->asBoolean();
            } elseif ($dataTypeId === Mobi_Mtld_DA_DataType::INTEGER) {
                return $property->asInteger();
            }            
            return $property->asString();
        }
        return null;
    }
}
