<?php
/**
 * $Id: Session.lib.php 188 2015-02-14 18:49:01Z radu $
 *
 * Session library.
 *
 * @author     Radu Maier <radu@webserv.co>
 * @copyright  Copyright (c) 2008 - 2014, Radu Maier
 * @license    http://www.opensource.org/licenses/bsd-license.php
 * @package    WEb
 * @subpackage WebSession
 * @version    $Revision: 188 $
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
 * Session
 *
 * Session library.
 *
 * @author Radu Maier <radu@webserv.co>
 * @package WEb
 * @subpackage WebSession
 *            
 */
class Session extends Library {

	/**
	 * Library properties.
	 *
	 * @var array
	 */
	private $_mProp = array ();

	/**
	 * Library tables.
	 *
	 * @var array
	 */
	protected $_mTable = array ();

	/**
	 * Key prefix (use 'bin.' for binary key identification)
	 */
	protected $_mKeyPrefix = NULL;

	/**
	 * Implement GetInstance() method.
	 *
	 * This is required for PHP < 5.3 compatibility (no late static binding)
	 *
	 * @param string $className
	 *        	class name to instantiate
	 *        	
	 * @return object
	 */
	public static function GetInstance($className = __CLASS__) {

		/**
		 * Call parent method with current class name as parameter.
		 */
		return parent::_GetInstance($className);
	}

	private function _Init() {

		$this->_mKeyPrefix = (1 == Core::Config()->Get('app.database.binkey')) ? 'bin.' : NULL;
		
		if (empty($this->_mTable)) {
			
			$this->_mTable['persistent'] = "{$this->_mAppPrefix}acl_persistent";
		}
	}

	/**
	 * Start session.
	 *
	 * @return bool
	 */
	public function Start() {

		$this->_Init();
		
		$this->_mProp = array (
			'expire' => Core::Config()->Get('session.expire'),
			'lifetime' => Core::Config()->Get('session.lifetime'),
			'path' => Core::Config()->Get('session.path'),
			'domain' => Core::Config()->Get('session.domain'),
			'secure' => (1 == Core::Config()->Get('session.secure')) ? TRUE : FALSE,
			'httponly' => (1 == Core::Config()->Get('session.httponly')) ? TRUE : FALSE 
		);
		
		/**
		 * Set cache limiter.
		 */
		session_cache_limiter('public, must-revalidate');
		
		/**
		 * Set cache expire (minutes).
		 */
		session_cache_expire($this->_mProp['expire'] / 60);
		
		/**
		 * Set garbage collector timeout (seconds).
		 */
		ini_set('session.gc_maxlifetime', $this->_mProp['expire']);
		
		/**
		 * Sett application temp directory.
		 *
		 * @var string
		 */
		$temp_path = Core::Config()->Get('path.app') . Core::Config()->Get('dir.app.tmp') . '/';
		
		/**
		 * Set custom session path to application temp directory.
		 */
		ini_set('session.save_path', $temp_path);
		
		/**
		 * Set custom session path to application temp directory.
		 */
		session_save_path($temp_path);
		
		/**
		 * Make sure garbage collector visits us.
		 */
		ini_set('session.gc_probability', 1);
		
		$lifetime_days = $this->_mProp['lifetime'];
		
		$expire = time() + 60 * 60 * 24 * $lifetime_days;
		
		session_set_cookie_params($expire, $this->_mProp['path'], $this->_mProp['domain'], $this->_mProp['secure'], $this->_mProp['httponly']);
		
		session_name('WEb7');
		
		/**
		 * Start session.
		 */
		session_start();
		
		/**
		 * Header entity tag.
		 */
		header('ETag: PUB' . time());
		
		/**
		 * Header last modified tag.
		 */
		header('Last-Modified: ' . gmdate("D, d M Y H:i:s", time() - 10) . ' GMT');
		
		/**
		 * Header expires tag.
		 */
		header('Expires: ' . gmdate("D, d M Y H:i:s", time() + 5) . ' GMT');
		
		/**
		 * Header pragma tag.
		 */
		header('Pragma: no-cache');
		
		/**
		 * Header cache control tag.
		 */
		header('Cache-Control: max-age=1, s-maxage=1, no-cache, must-revalidate');
		
		Core::Log()->Write(__('Session library: Started new session.', 'WEb'), 'debug');
		
		if ((1 == Core::Config()->Get('session.token')) && empty($_SESSION['token'])) {
			
			Core::Log()->Write(__('Session library: Setting token', 'WEb'), 'debug');
			
			/**
			 *
			 * @var string
			 */
			$salt = Core::Security()->GetSalt('session');
			
			/**
			 *
			 * @var array
			 */
			$_SESSION['token'] = Core::Security()->GetToken($salt);
		}
		
		/**
		 * Return result.
		 */
		return TRUE;
	}

	/**
	 * Check persistent session.
	 *
	 * @return mixed
	 */
	public function CheckPersistentSession() {

		$this->_Init();
		
		if (empty($this->_mProp)) {
			
			$this->Start();
		}
		
		/**
		 *
		 * @var string
		 */
		$cookie_name = $this->_GetCookieName();
		
		if (empty($_COOKIE[$cookie_name])) {
			return FALSE;
		}
		
		/**
		 *
		 * @var array
		 */
		$cookie_value = str_split($_COOKIE[$cookie_name], 60);
		
		/**
		 *
		 * @var array
		 */
		$options['conditions'][] = array (
			'persistent_token',
			$cookie_value[0] 
		);
		
		/**
		 *
		 * @var array
		 */
		$options['conditions'][] = array (
			'persistent_expire',
			date('Y-m-d H:i:s'),
			'gt' 
		);
		
		/**
		 *
		 * @var array
		 */
		$persistence_data = Core::Database()->RetrieveOne(array (
			$this->_mKeyPrefix . 'user_id',
			'persistent_expire' 
		), $this->_mTable['persistent'], $options);
		
		if (empty($persistence_data)) {
			return FALSE;
		}
		
		/**
		 *
		 * @var string
		 */
		$user_id = $persistence_data[$this->_mKeyPrefix . 'user_id'];
		
		/**
		 *
		 * @var string
		 */
		$cookie_expire = date('Y-m-d H:i:s', strrev($cookie_value[1]));
		
		if ($cookie_expire != $persistence_data['persistent_expire']) {
			return FALSE;
		}
		
		return array (
			$user_id,
			$cookie_value[0],
			strtotime($persistence_data['persistent_expire']) 
		);
	}

	/**
	 * Add persistent session.
	 *
	 * @param string $userId        	
	 * @param string $persistentToken        	
	 * @param int $persistentExpire        	
	 *
	 * @return bool
	 */
	public function AddPersistentSession($userId, $persistentToken, $persistentExpire = NULL) {

		$this->_Init();
		
		if (empty($userId)) {
			
			return FALSE;
		}
		
		if (empty($this->_mProp)) {
			$this->Start();
		}
		
		if (empty($persistentExpire)) {
			/**
			 *
			 * @var int
			 */
			$lifetime_days = $this->_mProp['lifetime'];
			
			/**
			 *
			 * @var int
			 */
			$persistentExpire = time() + 60 * 60 * 24 * $lifetime_days;
		}
		
		/**
		 *
		 * @var array
		 */
		$data = array (
			$this->_mKeyPrefix . 'user_id' => $userId,
			'persistent_token' => $persistentToken,
			'persistent_expire' => date('Y-m-d H:i:s', $persistentExpire) 
		);
		
		if (FALSE !== Core::Database()->Create($data, $this->_mTable['persistent'])) {
			/**
			 *
			 * @var string
			 */
			$cookie_name = $this->_GetCookieName();
			
			/**
			 *
			 * @var string
			 */
			$cookie_value = $persistentToken . strrev($persistentExpire);
			
			/**
			 * Set cookie
			 */
			setcookie($cookie_name, $cookie_value, $persistentExpire, $this->_mProp['path'], $this->_mProp['domain'], $this->_mProp['secure'], $this->_mProp['httponly']);
			
			return TRUE;
		}
		
		return FALSE;
	}

	/**
	 * Remove persistent session(s).
	 *
	 * If a persistent token is given,
	 * the corresponding cookie is also reset
	 *
	 * If no persistent token is given,
	 * all persistentsessions are removed.
	 *
	 * @param string $userId        	
	 * @param string $persistentToken        	
	 * @param bool $onlyOld        	
	 *
	 * @return bool
	 */
	public function RemovePersistentSession($userId = NULL, $persistentToken = NULL, $onlyOld = FALSE) {

		$this->_Init();
		
		if (empty($this->_mProp)) {
			
			$this->Start();
		}
		
		/**
		 *
		 * @var array
		 */
		$options = array ();
		
		if (! empty($userId)) {
			/**
			 *
			 * @var array
			 */
			$options['conditions'][] = array (
				$this->_mKeyPrefix . 'user_id',
				$userId 
			);
		}
		
		if (! empty($persistentToken)) {
			/**
			 *
			 * @var array
			 */
			$options['conditions'][] = array (
				'persistent_token',
				$persistentToken 
			);
		}
		
		if (FALSE !== $onlyOld) {
			/**
			 *
			 * @var array
			 */
			$options['conditions'][] = array (
				'persistent_expire',
				date('Y-m-d H:i:s'),
				'lt' 
			);
		}
		
		Core::Database()->Delete($this->_mTable['persistent'], $options);
		
		if (! empty($persistentToken)) {
			/**
			 * Remove persistence cookie.
			 */
			$this->RemoveCookie('persistence');
		}
		
		return TRUE;
	}

	/**
	 * Remove cookie
	 *
	 * @param string $cookieType        	
	 *
	 * @return bool
	 */
	public function RemoveCookie($cookieType = '.session') {

		switch ($cookieType) {
			case '.session' :
				/**
				 *
				 * @var string
				 */
				$cookie_name = session_name();
				break;
			case '.persistence' :
				/**
				 *
				 * @var string
				 */
				$cookie_name = $this->_GetCookieName();
				break;
			default :
				/**
				 *
				 * @var string
				 */
				$cookie_name = $cookieType;
				break;
		}
		
		/**
		 * Set dummy cookie with expiration date in the past
		 */
		setcookie($cookie_name, '', time() - 3600, $this->_mProp['path'], $this->_mProp['domain'], $this->_mProp['secure'], $this->_mProp['httponly']);
		
		/**
		 * Destroy cookie.
		 */
		unset($_COOKIE[$cookie_name]);
		
		return TRUE;
	}

	/**
	 * Get Cookie name.
	 *
	 * @return string
	 */
	private function _GetCookieName() {

		/**
		 *
		 * @var string
		 */
		$cookie_salt = Core::Security()->GetSalt(Core::Config()->Get('path.app'));
		
		return Core::Security()->GetToken($cookie_salt, Core::Config()->Get('url.www'));
	}

	/**
	 * Set cookie.
	 *
	 * @param string $name        	
	 * @param string $value        	
	 */
	public function SetCookie($name, $value = '') {

		$lifetime_days = $this->_mProp['lifetime'];
		
		$expire = time() + 60 * 60 * 24 * $lifetime_days;
		
		setcookie($name, $value, $expire, $this->_mProp['path'], $this->_mProp['domain'], $this->_mProp['secure'], $this->_mProp['httponly']);
		
		return TRUE;
	}
}
?>