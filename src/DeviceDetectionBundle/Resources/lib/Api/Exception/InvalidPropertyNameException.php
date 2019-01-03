<?php
/*
 *  Copyright (c) 2008-2015 by Afilias Technologies Limited (dotMobi). All rights reserved.
 */

//namespace Mobi_Mtld_DA_Exception;

/**
 * The Mobi_Mtld_DA_Exception_InvalidPropertyNameException is thrown when an
 * attempt is made to get a property using a property name that does not exist.
 * This typically happens if the property name is misspelled. A full list of
 * possible property names can be found using the getPropertyNames() method of
 * CarrierApi.
 * 
 * This is a RuntimeException and does not have to be checked for if the correct
 * property names are used to lookup properties.
 * 
 * @copyright Copyright (c) 2008-2015 by Afilias Technologies Limited (dotMobi). All rights reserved.
 * @author Afilias Technologies Ltd
 */
class Mobi_Mtld_DA_Exception_InvalidPropertyNameException extends RuntimeException { }
