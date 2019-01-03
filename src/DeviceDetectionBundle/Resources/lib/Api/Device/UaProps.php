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
class Mobi_Mtld_DA_Device_UaProps extends Mobi_Mtld_DA_Device_PostWalkRules {
    // tree node ids
    const UA_RULES = 'uar';

    private $regexes;

    public function __construct($tree) {
        parent::__construct($tree, self::UA_RULES);
        // process the regexes - we need to override the default ones with any API
        // specific regexes and compile them all
        $this->_initProcessRegexes();
    }

    /**
     * Merge the tree walk properties with the User-Agent string properties using
     * the User-Agent rules
     *
     * @param string $userAgent  The User-Agent to find properties for
     * @param array  $props2Vals The results of the tree walk, map of property id to value id
     * @param array  $sought     A set of properties to return values for
     */
    public function putProperties($userAgent, $props2Vals, $sought) {
        // first check list of items that skip rules - these are typically non-mobile
        // boolean properties such as isBrowser, isBot etc
        if (self::_skipUaRules($props2Vals)) {
            return;
        }

        // now find the rules to run on the UA. This is a two step process.
        // Step 1 identifies the UA type and finds as list of rules to run.
        // Step 2 uses the list of rules to find properties in a UA

        // STEP 1: try and find the rules to run on the UA
        $rulesToRun = $this->_getUaPropertyRules($userAgent, $props2Vals);
        
        // STEP 2: try and extract properties using the rules
        if ($rulesToRun) {
            $this->_extractProperties($rulesToRun, $userAgent, $sought);
        }
    }

    /**
     * Check list of items that skip rules - these are typically non-mobile boolean
     * properties such as isBrowser, isBot, isCrawler etc
     *
     * @param array $idProperties The results of the tree walk, map of property id to value id
     * @return TRUE if the UA rules are to be skipped, FALSE if they are to be run
     */
    private function _skipUaRules($props2Vals) {
        foreach ($this->treeProvider->tree[$this->branch]['sk'] as $propId) {
            // property is detected
            if (isset($props2Vals[$propId])) {
                // check if property value is true
                if (!$this->treeProvider->lookupValue) {
                    if ($props2Vals[$propId]) {
                        return true;
                    }
                } elseif ($this->treeProvider->tree['v'][$props2Vals[$propId]]) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Try and find a set of property extraction rules to run on the User-Agent. This
     * is done in two ways.
     *
     * The first way uses properties found from the tree walk to identify the
     * User-Agent type. If there are still multiple UA types then refining regexes
     * can be run.
     *
     * If the above approach fails to find a match then fall back to the second way
     * which uses a more brute regex search approach.
     *
     * Once the UA type is known the correct set of property extraction rules can
     * be returned.
     *
     * @param string $userAgent The User-Agent to find properties for
     * @param array $props2Vals The results of the tree walk, map of property id to value id
     * @return An array of rules to run against the User-Agent or NULL if no rules are found
     */
    private function _getUaPropertyRules($userAgent, $props2Vals) {
        // Method one - use properties from tree walk to speed up rule search
        $rulesToRun = $this->_findRulesByProperties($userAgent, $props2Vals);

        // No match found using the properties so now we loop over all rule groups
        // again and try to use a more brute force attempt to find the rules to run
        // on this user-agent.
        $tempRules = $this->_findRulesByRegex($userAgent);
        if ($tempRules) {
            return array_merge($rulesToRun, $tempRules);
        }

        return $rulesToRun;
    }

    /**
     * Try and find User-Agent type and thus the rules to run by using the properties
     * returned from the tree walk. All the properties defined in the property matcher
     * set must match. If a match is found then the rules can be returned.
     *
     * @param string $userAgent The User-Agent to find properties for
     * @param array $props2Vals The results of the tree walk, map of property id to value id
     * @return An array of rules to run against the User-Agent or NULL if no rules are found
     */
    private function _findRulesByProperties($userAgent, $props2Vals) {
        $rulesToRunA = array();

        foreach ($this->treeProvider->tree[$this->branch]['rg'] as $group) {
            // check if we have the property match list
            if (isset($group['p'])) {
                // try matching defined properties so we know what rules to run. If there
                // is a match then we can return the rules to run. In some cases we need to
                // refine the match found by running some refining regexes
                $propMatch = self::_checkPropertiesMatch($group['p'], $props2Vals);

                if ($propMatch) {
                    if (isset($group['t'][1])) {
                        $rulesToRun = $this->_findRulesToRunByRegex($userAgent, $group['t'], 'f');
                    } else {
                        $rulesToRun = $group['t'][0]['r'];
                    }
                    
                    if ($rulesToRun) {
                        $rulesToRunA = array_merge($rulesToRunA, $rulesToRun);
                    }
                }
            }
        }
        
        return $rulesToRunA;
    }

    /**
     * This functions checks all the properties in the property matcher branch of
     * this rule group. This branch contains a list of properties and their values.
     * All must match for this function to return true.
     *
     * In reality the properties and values are indexes to the main property and
     * value arrays.
     *
     * @param array $propList The list of properties to check for matches
     * @param array $props2Vals The results of the tree walk, map of property id to value id
     * @return TRUE if ALL properties match, false otherwise
     */
    static private function _checkPropertiesMatch(array $propList, $props2Vals) {
        $propMatch = false;
        
        // loop over propList and try and match ALL properties
        foreach ($propList as $propId => $expectedValueId) {
            // get the value found via the tree walk
            if (isset($props2Vals[$propId])) {
                // we can speed things up a little by just comparing the IDs!
                if ($props2Vals[$propId] == $expectedValueId) {
                    $propMatch = true; // no break here as we want to check all properties
                } else {
                    // there was code here to check actual values if the IDs did not match
                    // but is was unnecessary. If the JSON generator is working correctly then 
                    // just the ID check is sufficient.
                    return false;
                }
            } else {
                return false;
            }
        }

        return $propMatch;
    }

    /**
     * Loop over a set of refining rules to try and determine the User-Agent type
     * and so find the rules to run on it.
     *
     * @param string $userAgent The User-Agent to find properties for
     * @param array $ruleSet The ruleset that contains the search regex id, refine regex id and the magical rulesToRun
     * @param string $type The type of rule to run either Refine or Search
     * @return An array of rules to run against the User-Agent or NULL if no rules are found
     */
    private function _findRulesToRunByRegex($userAgent, array $ruleSet, $type) {
        $rulesToRun = null;

        // we want these to run in the order they appear. For some reason the Json
        // class uses a Hashmap to represent an array of items so we have to loop
        // based on the index of the HashMap
        foreach ($ruleSet as $set) {

            // get refine / search id to run
            if (isset($set[$type])) {
                // now look up the pattern...
                if (preg_match($this->regexes[$set[$type]], $userAgent)) {
                    return $set['r']; // now get the rules to run!
                }
            }
        }

        return $rulesToRun;
    }

    /**
     * Search for the rules to run by checking the User-Agent with a regex. If there
     * is a match the rule list is returned.
     *
     * @param string $userAgent The User-Agent to find properties for
     * @return An array of rules to run against the User-Agent or NULL if no rules are found
     */
    private function _findRulesByRegex($userAgent) {

        foreach ($this->treeProvider->tree[$this->branch]['rg'] as $group) {
            $rulesToRun = $this->_findRulesToRunByRegex($userAgent, $group['t'], 's');
            if ($rulesToRun) {
                return $rulesToRun;
            }
        }

        return null;
    }

    /**
     * Find all the properties that are used for matching. This is needed in case
     * the Api.getProperty() function is called as we need these properties for
     * the User-Agent extraction rules to work correctly.
     *
     * @param array $group The rule group that can contain a property matcher
     */
    protected function _initGetMatcherPropertyIds($group) {
        // the properties matcher may not exist....
        if (isset($group['p'])) {
            foreach ($group['p'] as $propId => $propVal) {
                $this->propMatcherIdsInUse[$propId] = 1;
            }
        }
    }

    /**
     * Prepare the rule set by extracting it from the current group and counting
     * the items in the group. This is done to avoid counting the items on every
     * request.
     * 
     * @param array $group The current parent group.
     * @return array A list of all rule sets
     */
    protected function _initRuleSets($group) {
        return $group['t'];
    }

    /**
     * Process the regexes by overriding any default ones with API specific regexes.
     */
    private function _initProcessRegexes() {
        // process regexes...
        $this->regexes = 
            isset($this->treeProvider->tree[$this->branch]['reg'][Mobi_Mtld_DA_Device_Tree::API_ID])?
                $this->regexes = $this->treeProvider->tree[$this->branch]['reg'][Mobi_Mtld_DA_Device_Tree::API_ID]:
                $this->regexes = $this->treeProvider->tree[$this->branch]['reg']['d'];
    }

    /**
     * This function loops over all the rules in rulesToRun and returns any properties
     * that match. The properties returned can be typed or strings.
     *
     * @param array $rulesToRun The rules to run against the User-Agent to find the properties
     * @param string $userAgent The User-Agent to find properties for
     * @param array $sought A set of properties to return values for
     */
    private function _extractProperties(array $rulesToRun, $userAgent, $sought) {
        // Loop over the rules array, each object in the array can contain 4 items:
        // propertyid, propertyvalue, regexid and regexmatchposition
        foreach ($rulesToRun as $ruleDetails) {
            $rulePropId = $ruleDetails['p'];

            // check if we are looking for a specific property, if so and the
            // current rule property id is not it then continue
            if ($sought === null || isset($sought[$rulePropId])) {
                $propName = $this->treeProvider->tree['p'][$rulePropId];
                // do we have a property we can set without running the regex rule?
                if (isset($ruleDetails['v'])) {
                    // we have an ID to the value...
                    $this->treeProvider->properties->put(
                        substr($propName, 1),
                        new Mobi_Mtld_DA_Property(
                            $this->treeProvider->lookupValue?
                                $this->treeProvider->tree['v'][$ruleDetails['v']]: $ruleDetails['v'],
                            $propName[0]
                        )
                    );
                } else {
                    // otherwise apply the rule to extract the property from the UA
                    $regexId = $ruleDetails['r'];
                    $regex   = $this->regexes[$regexId];

                    // match the rule and extract the results

                    if (preg_match($regex, $userAgent, $matches)) {
                        $matchPos = $ruleDetails['m'];
                        if (!empty($matches[$matchPos])) {
                            $this->treeProvider->properties->put(
                                substr($propName, 1),
                                new Mobi_Mtld_DA_Property($matches[$matchPos], $propName[0])
                            );
                        }
                    }
                }
            }
        }
    }
}
