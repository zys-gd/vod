<?php
/*
 * Copyright (c) 2008-2015 by Afilias Technologies Limited (dotMobi). All rights reserved.
 */

//namespace Mobi_Mtld_DA_Carrier;

/**
 * The types of bucket and their associated IDs. The IDs are important and 
 * should **NOT** be changed. The API relies on these IDs remaining the same.
 * 
 * @copyright Copyright (c) 2008-2015 by Afilias Technologies Limited (dotMobi). All rights reserved.
 * @author Afilias Technologies Ltd
 */
class Mobi_Mtld_DA_Carrier_BucketType {
    /** Bucket type */
	const PROPERTY_NAMES  = 0;
    /** Bucket type */
	const PROPERTY_VALUES = 1;
    /** Bucket type */
	const PROPERTIES      = 2;
    /** Bucket type */
	const IPV4_TREE       = 3;

    /** List of bucket types. */	
	public static $ALL_TYPES = array(
        self::IPV4_TREE,
        self::PROPERTIES,
        self::PROPERTY_NAMES,
        self::PROPERTY_VALUES,
	);

    /**
     * Check if id is a valid bucket type or not.
     * @param byte id bucket id
     * @return bool If id is a valid bucket type.
     */	
	public static function isValidId($id) {
		return isset(self::$ALL_TYPES[$id]);
	}
}
