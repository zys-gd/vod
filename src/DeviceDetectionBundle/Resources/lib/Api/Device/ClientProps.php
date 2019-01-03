<?php
/*
 * package Mobi\Mtld\DA\Device
 * Copyright (c) 2008-2015 by Afilias Technologies Limited (dotMobi). All rights reserved.
 */

//namespace Mobi_Mtld_DA_Device;
require_once Mobi_Mtld_DA_DEVICE_API_PATH.'PostWalkRules.php';

/**
 * This class is used by the main API class and should not be used directly.
 *
 * @package Mobi\Mtld\DA\Device
 * @author Afilias Technologies Ltd
 * @copyright Copyright (c) 2008-2015 by Afilias Technologies Limited (dotMobi). All rights reserved.
 */
class Mobi_Mtld_DA_Device_ClientProps extends Mobi_Mtld_DA_Device_PostWalkRules {

    const CP_RULES = 'cpr';

    public function __construct($tree) {
        return parent::__construct($tree, self::CP_RULES);
    }

    /**
     * Merge the tree walk properties with the client side properties and run any
     * additional rules based on the client side and tree walk properties. The rules
     * can define replacement or additional values for properties and can also provide
     * a new User-Agent to be used for a second tree walk. This is typically a fake 
     * User-Agent mapped to a device that cannot normally be detected such as the various
     * iPhone models.
     *
     * @param string $clientSide
     */
    public function putProperties($clientSide) {
        // "merge with detected properties" and "get" the props from the client side cookie
        $clientSide = $this->_parseClientSideProperties($clientSide);

        // use the merged properties to look up additional rules

        // STEP 1: try and find the rules to run on the UA
        $rulesToRun = $this->_getRulesToRun();

        // STEP 2: do second tree walk if necessary and replace/create any new 
        // values based on the rules
        if ($rulesToRun) {
            list($userAgent, $ruleSet) = $rulesToRun;
            if ($userAgent !== null) {
                // use the UA for a second tree walk - note the last param is 
                // false as we know the UA won't have any dynamic properties

                $this->treeProvider->putTreeWalkProperties($userAgent);

                // merge origProperties in to get any parent properties such as the dynamic properties
                // 2nd tree walk > first tree walk

                // the client properties still take priority so merge them in again
                // client props > tree walks

                foreach ($clientSide as $propTypeName => $propValue) {
                    $this->treeProvider->properties->put(
                        substr($propTypeName, 1),
                        new Mobi_Mtld_DA_Property($propValue, $propTypeName[0])
                    );
                }
            }

            // overlay the new properties [{PROPERTY_VALUE:id, PROPERTY:id}]
            foreach ($ruleSet as $propIdValId) {
                $propTypeName = $this->treeProvider->tree['p'][$propIdValId['p']];
                $this->treeProvider->properties->put(
                    substr($propTypeName, 1),
                    new Mobi_Mtld_DA_Property(
                        $this->treeProvider->lookupValue?
                            $this->treeProvider->tree['v'][$propIdValId['v']]: $propIdValId['v'],
                        $propTypeName[0]
                    )
                );
            }
        }
    }

    /**
     * Parse the client side properties string into Map {key: value,} Sets to the 
     * tree.properties and returns the properties as Map (it is needed)
     * 
     * The clientSide is of the form:
     * bjs.webGl:1|bjs.geoLocation:1|sdeviceAspectRatio:16/10|iusableDisplayHeight:1050
     * Each key:val part of clientSide is checked for sanity, if it looks not fine then
     * that key:val will be ignored.
     * 
     * The first character of the property name is the type of the value.
     * 
     * @param clientSide The client side properties string (from cookie)
     * @return {key: value,}
     */
    private function _parseClientSideProperties($clientSide) {
        $props = array();

        if ($clientSide) {
            foreach (explode('|', trim($clientSide, '"')) as $nameValuePair) {
                list($propTypeName, $propValue) = explode(':', $nameValuePair, 2);
                $type = $propTypeName[0];
                // if name and type are valid
                if (ctype_alnum(str_replace('.', '', $propTypeName))
                    && ($type === 'b' || $type === 'i' || $type === 's' || $type === 'd')) {
                    $props[$propTypeName] = filter_var(trim($propValue, '"'), FILTER_SANITIZE_SPECIAL_CHARS);
                    $this->treeProvider->properties->put(
                        substr($propTypeName, 1),
                        new Mobi_Mtld_DA_Property($propValue, $type)
                    );
                }
            }
        }
        return $props;
    }

    /**
     * Find all the properties that are used for matching. This is needed in case
     * the Api.getProperty() function is called as we need these properties for
     * the rules to work correctly
     * 
     * @param array $groups The rule group that can contain a property matcher
     */
    protected function _initGetMatcherPropertyIds($group) {
        if (isset($group['p'])) {
            foreach ($group['p'] as $propertyMatcher) {
                $this->propMatcherIdsInUse[$propertyMatcher['p']] = 1;
            }
        }
    }

    /**
     * 
     */
    private function _getRulesToRun() {
        foreach ($this->treeProvider->tree[$this->branch]['rg'] as $group) {
            $propertyMatchers = $group['p'];

            // try matching defined properties so we know what rules to run. If there
            // is a match then we can return the rules to run.
            $propMatch = $this->_checkPropertiesMatch($propertyMatchers);

            if ($propMatch) {
                $userAgent = isset($group['ua'])? $group['ua']: null;
                $ruleSet   = $group['r'];
                return array($userAgent, $ruleSet);
            }
        }
        return null;
    }
   
    /**
     * Prepare the rule set by extracting it from the current group and wrapping
     * it in an array. This is done to remain compatible with initGetRulePropertyIds()
     * 
     * @param array $group The current parent group.
     * @return A list of all rule sets
     */
    protected function _initRuleSets($group) {
        // wrap the single rule set in an array list.
        return array(array('r' => $group['r']));
    }

    /**
     * This functions checks all the properties in the property matcher branch of
     * this rule group. This branch contains a list of properties, their values
     * and an operator to use for comparison. All must match for this function to 
     * return true.
     *
     * In reality the properties and values are indexes to the main property and
     * value arrays.
     */
    private function _checkPropertiesMatch($propertyMatchers) {
        $propMatch = false;

        // loop over propList and try and match ALL properties
        foreach ($propertyMatchers as $matcherDetails) {
            $propId       = $matcherDetails['p'];
            $propTypeName = $this->treeProvider->tree['p'][$propId];
            $propName     = substr($propTypeName, 1);
            // compare the detected value to the expected value
            if ($this->treeProvider->properties->containsKey($propName)) {
                $detectedValue = $this->treeProvider->properties->get($propName);
                // get the expected value
                $propValId     = $matcherDetails['v'];
                $expectedValue = $this->treeProvider->lookupValue?
                    $this->treeProvider->tree['v'][$propValId]: $propValId;
                $operator      = $matcherDetails['o'];
                $propMatch     = $this->_compareValues(
                    $detectedValue->value(),
                    $expectedValue,
                    $operator,
                    $propTypeName
                );
                if (!$propMatch) {
                    return false;
                }

            } else {
                return false;
            }
        }
        return $propMatch;
    }

    /**
     * Compare two values that can be one of String, Boolean or Integer using the
     * passed in operator.
     * 
     * @param string $detectedValue
     * @param string $expectedValue
     * @param string $operator
     * @param string $typePropName
     * @return boolean
     */
    private function _compareValues($detectedValue, $expectedValue, $operator, $propTypeName) {
        switch ($propTypeName[0]) {
            case 's':
            case 'b':
                if ($operator === '=') {
                    return $detectedValue == $expectedValue;
                }
                if ($operator === '!=') {
                    return $detectedValue != $expectedValue;
                }
            case 'i':
                $dVal = (int)$detectedValue;
                $eVal = (int)$expectedValue;

                if ($dVal == $eVal && ($operator === '='
                        || $operator === '<='
                        || $operator === '>=')) {
                    return true;
                }
                if ($dVal > $eVal && ($operator == '>'
                        || $operator === '>=')) {
                    return true;
                }
                if ($dVal < $eVal && ($operator === '<'
                        || $operator === '<=')) {
                    return true;
                }
                if ($dVal != $eVal && $operator === '!=') {
                    return true;
                }
        }
        return false;
    }
}
