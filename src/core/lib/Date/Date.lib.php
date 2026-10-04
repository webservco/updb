<?php
/**
 * $Id: Date.lib.php 168 2014-04-26 07:28:51Z radu $
 *
 * Date library.
 *
 * @author     Radu Maier <radu@webserv.co>
 * @copyright  Copyright (c) 2008 - 2014, Radu Maier
 * @license    http://www.opensource.org/licenses/bsd-license.php
 * @package    WEb
 * @subpackage WebDate
 * @version    $Revision: 168 $
 * @since      Version 5
 * @access     public
 * @filesource
 */

/**
 * Security check.
 */
if (!defined('_WEB')) {
	/**
	 * Stop script execution.
	 */
	exit();
}

/**
 * Date
 *
 * Date library.
 *
 * @author     Radu Maier <radu@webserv.co>
 * @package    WEb
 * @subpackage WebDate
 *
 */
class Date extends Library {
	/**
	 * Stores the request array.
	 *
	 * @var array
	 */
	private $_mRequest;

	/**
	 * Implement GetInstance() method.
	 *
	 * This is required for PHP < 5.3 compatibility (no late static binding)
	 *
	 * @param string $className class name to instantiate
	 *
	 * @return object
	 */
	public static function GetInstance($className = __CLASS__) {
		/**
		 * Call parent method with current class name as parameter.
		 */
		return parent::_GetInstance($className);
	}

	/**
	 * Set time zone.
	 *
	 * @param string $timezone
	 *
	 * @return boolean
	 */
	public function SetTimeZone($timezone) {

		date_default_timezone_set($timezone);

		return TRUE;
	}
	
	/**
	 * Get time zone.
	 * 
	 * @return string
	 */
	public function GetTimeZone() {
		
		return date_default_timezone_get();
	}
}
?>