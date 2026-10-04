<?php
/**
 * $Id: Useragent.lib.php 168 2014-04-26 07:28:51Z radu $
 *
 * User Agent parsing library.
 *
 * @author     Radu Maier <radu@webserv.co>
 * @copyright  Copyright (c) 2008 - 2014, Radu Maier
 * @license    http://www.opensource.org/licenses/bsd-license.php
 * @package    WEb
 * @subpackage WebUseragent
 * @version    $Revision: 168 $
 * @since      Version 1
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
 * Useragent
 * User agent parsing library.
 * 
 * @author Radu Maier <radu@webserv.co>
 * @package WEb
 * @subpackage WebUseragent
 */
class Useragent extends Library {

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
	 * Get user agent string.
	 * 
	 * @return string
	 */
	public function GetUserAgent() {

		if (Core::IsCli()) {
			
			return 'CLI';
		}
		
		return !empty($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : NULL;
	}

	/**
	 * Get OS type.
	 * android, ios, other
	 * 
	 * @param string $userAgent        	
	 *
	 * @return string
	 */
	public function GetOsType($userAgent = NULL) {

		$user_agent = !empty($userAgent) ? $userAgent : $this->GetUserAgent();
		
		if ($this->IsOsAndroid($user_agent)) {
			return 'android';
		}
		
		if ($this->IsOsIos($user_agent)) {
			return 'ios';
		}
		
		return 'other';
	}

	/**
	 * Get device type.
	 * tablet, phone, other
	 * 
	 * @param string $userAgent        	
	 *
	 * @return string
	 */
	public function GetDeviceType($userAgent = NULL) {

		$user_agent = !empty($userAgent) ? $userAgent : $this->GetUserAgent();
		
		if ($this->IsDeviceTablet($user_agent)) {
			
			return 'tablet';
		}
		
		if ($this->IsDevicePhone($user_agent)) {
			
			return 'phone';
		}
		
		return 'other';
	}

	/**
	 * iOS check.
	 * Can be both mobile and desktop
	 * 
	 * @param string $userAgent        	
	 *
	 * @return boolean
	 */
	public function IsOsIos($userAgent = NULL) {

		$user_agent = !empty($userAgent) ? $userAgent : $this->GetUserAgent();
		
		$ios_strings = array(
			'iPhone',
			'iPod',
			'iPad',
			'Darwin'
		);
		
		foreach ($ios_strings as $ios_string) {
			
			if (strpos($user_agent, $ios_string) !== FALSE) {
				return TRUE;
			}
		
		}
		
		return FALSE;
	}

	/**
	 * Android check.
	 * 
	 * @param string $userAgent        	
	 *
	 * @return boolean
	 */
	public function IsOsAndroid($userAgent = NULL) {

		$user_agent = !empty($userAgent) ? $userAgent : $this->GetUserAgent();
		
		return strpos(strtolower($user_agent), 'android') !== FALSE;
	}

	/**
	 * Device or desktop check.
	 * 
	 * @param string $userAgent        	
	 *
	 * @return boolean
	 */
	public function IsDevice($userAgent = NULL) {

		$user_agent = !empty($userAgent) ? $userAgent : $this->GetUserAgent();
		
		if ($this->IsDeviceTablet($user_agent)) {
			return TRUE;
		}
		
		if ($this->IsDevicePhone($user_agent)) {
			return TRUE;
		}
		
		return FALSE;
	}

	/**
	 * Phone check.
	 * 
	 * @param string $userAgent        	
	 *
	 * @return boolean
	 */
	public function IsDevicePhone($userAgent = NULL) {

		$user_agent = !empty($userAgent) ? $userAgent : $this->GetUserAgent();
		
		if ($this->IsDeviceTablet($user_agent)) {
			
			return FALSE;
		}
		
		if ($this->IsDevicePhoneIos($user_agent)) {
			
			return TRUE;
		}
		
		if ($this->IsOsAndroid($user_agent)) {
			
			return TRUE;
		}
		
		if ($this->IsDevicePhoneWindows($user_agent)) {
			
			return TRUE;
		}
	}

	/**
	 * iPhone check.
	 * 
	 * @param string $userAgent        	
	 *
	 * @return boolean
	 */
	public function IsDevicePhoneIos($userAgent = NULL) {

		$user_agent = !empty($userAgent) ? $userAgent : $this->GetUserAgent();
		
		return strpos(strtolower($user_agent), 'iphone') !== FALSE;
	}

	/**
	 * Android phone check.
	 * 
	 * @param string $userAgent        	
	 *
	 * @return boolean
	 */
	public function IsDevicePhoneAndroid($userAgent = NULL) {

		$user_agent = !empty($userAgent) ? $userAgent : $this->GetUserAgent();
		
		if ($this->IsOsAndroid($user_agent)) {
			
			return $this->IsDeviceTabletAndroid($user_agent) ? FALSE : TRUE;
		}
		
		return FALSE;
	}

	/**
	 * Windows phone check.
	 * 
	 * @param string $userAgent        	
	 *
	 * @return boolean
	 */
	public function IsDevicePhoneWindows($userAgent = NULL) {

		$user_agent = !empty($userAgent) ? $userAgent : $this->GetUserAgent();
		
		return strpos(strtolower($user_agent), 'phone') !== FALSE;
	}

	/**
	 * Tablet check.
	 * 
	 * @param string $userAgent        	
	 *
	 * @return boolean
	 */
	public function IsDeviceTablet($userAgent = NULL) {

		$user_agent = !empty($userAgent) ? $userAgent : $this->GetUserAgent();
		
		if ($this->IsDeviceTabletAndroid($user_agent)) {
			
			return TRUE;
		}
		
		if ($this->IsDeviceTabletIos($user_agent)) {
			
			return TRUE;
		}
		
		return FALSE;
	}

	/**
	 * iPad check.
	 * 
	 * @param string $userAgent        	
	 *
	 * @return boolean
	 */
	public function IsDeviceTabletIos($userAgent = NULL) {

		$user_agent = !empty($userAgent) ? $userAgent : $this->GetUserAgent();
		
		return strpos(strtolower($user_agent), 'ipad') !== FALSE;
	}

	/**
	 * Android tablet check.
	 * 
	 * @param string $userAgent        	
	 *
	 * @return boolean
	 */
	public function IsDeviceTabletAndroid($userAgent = NULL) {

		$user_agent = !empty($userAgent) ? $userAgent : $this->GetUserAgent();
		
		if ($this->IsOsAndroid($user_agent)) {
			
			return strpos(strtolower($user_agent), 'mobile') === FALSE ? TRUE : FALSE;
		}
		
		return FALSE;
	}

	/**
	 * Get custom web app version sent from mobile devices.
	 * Custom user agent string must end with "WebApp/VERSION".
	 * 
	 * @param string $userAgent        	
	 *
	 * @return boolean
	 */
	public function GetWebAppVersion($userAgent = NULL) {

		$user_agent = !empty($userAgent) ? $userAgent : $this->GetUserAgent();
		
		$data = array();
		
		$pattern = "#WebApp(\s+|/\s*)([^)(;\s]+)#i";
		
		preg_match($pattern, $user_agent, $data);
		
		return (!empty($data[2])) ? $data[2] : FALSE;
	}

	/**
	 * Get internet explorer version.
	 * Returns 0 if other browser.
	 * 
	 * @param string $userAgent        	
	 * @return number
	 */
	public function GetIeVersion($userAgent = NULL) {

		$user_agent = !empty($userAgent) ? $userAgent : $this->GetUserAgent();
		
		$data = array();
		
		$pattern = "#MSIE ([0-9]{1,}[\.0-9]{0,})#i";
		
		preg_match($pattern, $user_agent, $data);
		
		return (!empty($data[1])) ? $data[1] + 0 : 0;
	}
}
?>