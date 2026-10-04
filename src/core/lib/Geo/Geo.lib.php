<?php
/**
 * $Id: Geo.lib.php 227 2017-03-28 07:40:37Z radu $
 *
 * Geolocation library.
 *
 * @author     Radu Maier <radu@webserv.co>
 * @copyright  Copyright (c) 2008 - 2014, Radu Maier
 * @license    http://www.opensource.org/licenses/bsd-license.php
 * @package    WEb
 * @subpackage WebGeo
 * @version    $Revision: 227 $
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
 * Geo
 * Geolocation library.
 *
 * @author Radu Maier <radu@webserv.co>
 * @package WEb
 * @subpackage WebGeo
 */
class Geo extends Library {

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
	 * Get IP address.
	 * Uses the $_SERVER superglobal
	 *
	 * @return string
	 */
	public function GetIp() {

		/**
		 *
		 * @var array
		 */
		$check = array(
				'CLIENT_IP',
				'HTTP_CLIENT_IP',
				
				'HTTP_X_FORWARDED',
				'HTTP_X_FORWARDED_FOR',
				'HTTP_X_FORWARDED_FOR_IP',
				
				'HTTP_FORWARDED',
				'HTTP_FORWARDED_FOR',
				'HTTP_FORWARDED_FOR_IP',
				
				'FORWARDED',
				'FORWARDED_FOR',
				'FORWARDED_FOR_IP',
				
				'X_FORWARDED',
				'X_FORWARDED_FOR',
				
				'HTTP_COMING_FROM',
				'HTTP_X_COMING_FROM',
				
				'VIA',
				'HTTP_VIA',
				
				'HTTP_PC_REMOTE_ADDR',
				
				'HTTP_X_IMFORWARDS',
				
				'HTTP_PROXY',
				'HTTP_PROXY_CONNECTION',
				
				'HTTP_XPROXY',
				'HTTP_XROXY_CONNECTION',
				
				'HTTP_PRAGMA',
				'HTTP_XONNECTION',
				'HTTP_CACHE_INFO',
				
				'ZHTTP_CACHE_CONTROL' 
		);
		
		if (Core::IsCli()) {
			
			return gethostbyname(php_uname('n')) . ' (CLI)';
		}
		
		foreach ($check as $var) {
			if (!empty($_SERVER[$var])) {
				/**
				 *
				 * @var string
				 */
				$ip = Core::String()->GetFirstItem($_SERVER[$var], ',');
				
				if ($this->IsValidIp($ip)) {
					return $ip;
				}
			}
		}
		
		return !empty($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : NULL;
	}

	/**
	 * Chech if IP address is valid.
	 *
	 * @param string $ip        	
	 * @param bool $checkWan        	
	 *
	 * @return bool
	 */
	public function IsValidIp($ip = NULL, $checkWan = TRUE) {

		if (empty($ip)) {
			return FALSE;
		}
		
		$ip_num = ip2long($ip);
		
		if (($ip_num !== -1) && (FALSE !== $ip_num) && (long2ip($ip_num) === $ip)) {
			if (FALSE === $checkWan) {
				return TRUE;
			}
			
			if (($ip_num < 167772160 || $ip_num > 184549375) && 			// Not in 10.0.0.0/8
			($ip_num < -1408237568 || $ip_num > -1407188993) && 			// Not in 172.16.0.0/12
			($ip_num < -1062731776 || $ip_num > -1062666241)) 			// Not in 192.168.0.0/16
			{
				return TRUE;
			}
		}
		
		return FALSE;
	}

	/**
	 * Get the list of countries from the database.
	 *
	 * @param mixed $status        	
	 *
	 * @return array
	 */
	public function GetCountries($status = NULL) {

		$data = array();
		
		$q_status = isset($status) ? " AND country_status = '{$status}'" : NULL;
		
		$q = "SELECT country_code, country_name
				FROM {$this->_mAppPrefix}geo_countries WHERE 1
				{$q_status} ORDER BY country_name";
		
		$rs = Core::Database()->Query($q);
		
		$t = $rs->num_rows;
		
		if (0 < $t) {
			
			while ($r = $rs->fetch_assoc()) {
				
				$data[$r['country_code']] = $r['country_name'];
			}
		}
		
		return $data;
	}

	public function GetCountryCode($countryName) {

		$q = "SELECT country_code FROM {$this->_mAppPrefix}geo_countries WHERE country_name = '{$countryName}' LIMIT 1";
		
		$rs = Core::Database()->Query($q);
		
		if (1 == $rs->num_rows) {
			
			$r = $rs->fetch_assoc();
			
			return $r['country_code'];
		}
		
		return FALSE;
	}

	/**
	 * Get the list of departments from the databse.
	 *
	 * @param string $countryCode        	
	 * @return array
	 */
	public function GetDepartments($countryCode) {

		$data = array();
		
		$q = "SELECT department_code, department_name FROM {$this->_mAppPrefix}geo_departments WHERE country_code = '{$countryCode}' ORDER BY department_name";
		
		$rs = Core::Database()->Query($q);
		
		$t = $rs->num_rows;
		
		if (0 < $t) {
			
			while ($r = $rs->fetch_assoc()) {
				
				$data[$r['department_code']] = $r['department_name'];
			}
		}
		
		return $data;
	}

	/**
	 * Get the list of languages from the database.
	 *
	 * @param mixed $status        	
	 *
	 * @return array
	 */
	public function GetLanguages($status = NULL) {

		$data = array();
		
		$q_status = isset($status) ? " AND language_status = '{$status}'" : NULL;
		
		$q = "SELECT language_code, language_name
		FROM {$this->_mAppPrefix}geo_languages WHERE 1
		{$q_status} ORDER BY language_name";
		
		$rs = Core::Database()->Query($q);
		
		$t = $rs->num_rows;
		
		if (0 < $t) {
			
			while ($r = $rs->fetch_assoc()) {
				
				$data[$r['language_code']] = $r['language_name'];
			}
		}
		
		return $data;
	}

	public function GetCoordinates($address, $apiKey = NULL) {

		$url = 'https://maps.google.com/maps/api/geocode/json?sensor=false&address=' . str_replace(' ', '+', $address);
		
		if (!empty($apiKey)) {
			
			$url .= "&key={$apiKey}";
		}
		
		$response = file_get_contents($url);
		
		if (!empty($response)) {
			
			$json = json_decode($response, TRUE);
			
			if (!empty($json['results'][0]['geometry']['location']['lat']) && !empty($json['results'][0]['geometry']['location']['lng'])) {
				
				return array(
						$json['results'][0]['geometry']['location']['lat'],
						$json['results'][0]['geometry']['location']['lng'] 
				);
			}
		}
		
		return FALSE;
	}
}
?>