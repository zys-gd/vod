<?php
/*
 * package Mobi\Mtld\DA\Device
 * Copyright (c) 2008-2015 by Afilias Technologies Limited (dotMobi). All rights reserved.
 */

//namespace Mobi_Mtld_DA_Device;

/**
 * This class is used by the main API class and should not be used directly.
 * 
 * @package Mobi\Mtld\DA\Device
 * @author Afilias Technologies Ltd
 * @copyright Copyright (c) 2008-2015 by Afilias Technologies Limited (dotMobi). All rights reserved.
 */
abstract class Mobi_Mtld_DA_Device_PostWalkRules {

	protected $treeProvider;

    // stores only the branch name
	protected $branch;
	protected $propMatcherIdsInUse = array();
	protected $rulePropIdsInUse    = array();

	public function __construct($treeProvider, $type) {
		$this->treeProvider = $treeProvider;
		$this->branch       = $type;
		$this->_init();
	}

	/**
     * Initiliase some data structures to avoid doing it during requests
	 */
	private function _init() {
		foreach ($this->treeProvider->tree[$this->branch]['rg'] as $group) {
			// We want to keep a list of all the properties that are used because when
			// a user calls getProperty we need to fetch additional properties other than
			// the property they want to optimize the User-Agent string rules.
			$this->_initGetMatcherPropertyIds($group);
			$sets = $this->_initRuleSets($group);
			// also keep a list of all the property IDs that can be output
			$this->_initGetRulePropertyIds($sets);
		}
	}

	/**
	 * Find all the properties that are used in the final rules. This is needed to
	 * optimise the Api.getProperty() function.
	 *
	 * @param array $sets The rule set from the main rule group
	 */
	private function _initGetRulePropertyIds(array $sets) {
		// loop over all items in the rule set and find all the property ids
		// used in the rules
		foreach ($sets as $items) {
			// now loop over the actual rule array
			foreach ($items['r'] as $ruleDetails) {
				$propId = $ruleDetails['p'];
				$this->rulePropIdsInUse[$propId] = 1;
			}
		}
	}
	
	/**
	 * Find all the properties that are used for matching. This is needed in case
	 * the Api.getProperty() function is called as we need these properties for
	 * the rules to work correctly
	 * 
	 * @param array $group The rule group that can contain a property matcher
	 */
	protected abstract function _initGetMatcherPropertyIds($group);

	/**
	 * Prepare the rule set
	 * 
	 * @param array $group The current parent group.
	 * @return A list of all rule sets
	 */
	protected abstract function _initRuleSets($group);

	/**
	 * Check if the property is used in the rules and so can be found from them.
	 * This is used in Api.getProperty() to avoid calling the methods in the class
	 * if the property that is being looked for cannot be found here.
	 * 
	 * @param integer $propertyId The ID of the property that is sought
	 * @return TRUE if the propertyId is used, FALSE otherwise
	 */
	public function propIsOutput($propertyId) {
		return isset($this->rulePropIdsInUse[$propertyId]);
	}

	/**
	 * Get a list of all the required properties that are needed for this class
	 * to properly run its rules.
	 * 
	 * @return The list of required properties.
	 */
	public function getRequiredProperties() {
		return $this->propMatcherIdsInUse;
	}
}
