<?php
/**
 * $Id: Arrays.lib.php 200 2015-07-08 10:46:01Z radu $
 *
 * Arrays library.
 *
 * @author     Radu Maier <radu@webserv.co>
 * @copyright  Copyright (c) 2008 - 2014, Radu Maier
 * @license    http://www.opensource.org/licenses/bsd-license.php
 * @package    WEb
 * @subpackage WebArrays
 * @version    $Revision: 200 $
 * @since      Version 1
 * @access     public
 * @filesource
 */

/**
 * Security check.
 */
if (! defined('_WEB')) {
	/**
	 * Stop script execution.
	 */
	exit();
}

/**
 * Arrays
 * Arrays library.
 *
 * @author Radu Maier <radu@webserv.co>
 * @package WEb
 * @subpackage WebArrays
 */
class Arrays extends Library {

	/**
	 * Implement GetInstance() method.
	 * This is required for PHP < 5.3 compatibility (no late static binding)
	 *
	 * @param string $className
	 *        	class name to instantiate
	 * @return object
	 */
	public static function GetInstance($className = __CLASS__) {

		/**
		 * Call parent method with current class name as parameter.
		 */
		return parent::_GetInstance($className);
	}

	/**
	 * Search for an item in the first level of a multi-dimensional array.
	 * Returns corresponding key.
	 *
	 * @param mixed $searchKey        	
	 * @param mixed $searchValue        	
	 * @param mixed $haystack        	
	 * @return mixed
	 */
	public function SearchKeyValue($searchKey, $searchValue, $haystack) {

		foreach ($haystack as $key => $value) {
			
			if (isset($value[$searchKey]) && $value[$searchKey] == $searchValue) {
				
				return $key;
			}
		}
		
		return FALSE;
	}

	/**
	 * Get previous key in array based on another key.
	 *
	 * @param mixed $searchKey        	
	 * @param array $array        	
	 *
	 * @return mixed
	 */
	public function GetPreviousKey($searchKey, $array) {

		$keys = array_keys($array);
		$previous_index = array_search($searchKey, $keys) - 1;
		
		return isset($keys[$previous_index]) ? $keys[$previous_index] : FALSE;
	}

	/**
	 * Get next key in array based on another key.
	 *
	 * @param array $array        	
	 * @param mixed $searchKey        	
	 *
	 * @return mixed
	 */
	public function GetNextKey($array, $searchKey) {

		$keys = array_keys($array);
		$next_index = array_search($searchKey, $keys) + 1;
		
		return isset($keys[$next_index]) ? $keys[$next_index] : FALSE;
	}

	/**
	 * Get position of key in an array.
	 *
	 * @param mixed $searchKey        	
	 * @param array $array        	
	 *
	 * @return mixed
	 */
	public function GetKeyPosition($searchKey, $array) {

		return array_search($searchKey, array_keys($array));
	}
}
?>