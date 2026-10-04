<?php
/**
 * $Id: Log.lib.php 226 2017-01-07 11:51:55Z radu $
 *
 * Logging library.
 *
 * @author     Radu Maier <radu@webserv.co>
 * @copyright  Copyright (c) 2008 - 2014, Radu Maier
 * @license    http://www.opensource.org/licenses/bsd-license.php
 * @package    WEb
 * @subpackage WebLog
 * @version    $Revision: 226 $
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
 * Log
 *
 * Logging library.
 *
 * @author     Radu Maier <radu@webserv.co>
 * @package    WEb
 * @subpackage WebLog
 *
 */
class Log extends Library {
	/**
	 * Stores log files path.
	 *
	 * @var string
	 */
	private $_mLogPath;

	/**
	 * Stores default log type.
	 *
	 * @var string
	 */
	private $_mLogType = 'app';

	/**
	 * Stores log level.
	 *
	 * Defaults to 3 (warning)
	 *
	 * @var int
	 */
	private $_mLogLevel = 4;

	private $_mLogLevels = array('debug' => 0, 'info' => 1, 'notice' => 2, 'warning' => 3, 'error' => 4, 'crit' => 5, 'alert' => 6, 'emerg' => 7,);

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
	 * Set log type.
	 *
	 * @param string $logType
	 *
	 * @return bool
	 */
	public function SetType($logType) {
		/**
		 * @var string
		 */
		$this->_mLogType = $logType;

		return TRUE;
	}

	/**
	 * Set log files path.
	 *
	 * @param string $logPath
	 *
	 * @return bool
	 */
	public function SetPath($logPath) {
		/**
		 * @var string
		 */
		$this->_mLogPath = $logPath;

		return TRUE;
	}

	/**
	 * Set logging level code by level name.
	 *
	 * Return level code on success, false on failure.
	 *
	 * @param string $logLevelName
	 *
	 * @return mixed
	 */
	public function SetLevel($levelName) {
		if (empty($levelName)) {
			return FALSE;
		}

		if (array_key_exists($levelName, $this->_mLogLevels)) {
			$this->_mLogLevel = $this->_mLogLevels[$levelName];

			return $this->_mLogLevel;
		}

		return FALSE;
	}

	/**
	 * Get full log file path.
	 *
	 * If log file does not exist, it attempts to create it.
	 *
	 * Returns FALSE if log file is not writable
	 *
	 * @param string $logType
	 * @param string $logPath
	 *
	 * @return mixed
	 */
	public function GetFile($logType = NULL, $logPath = NULL) {
		/**
		 * @var string
		 */
		$log_type = ($logType) ? $logType : $this->_mLogType;

		/**
		 * @var string
		 */
		$log_path = ($logPath) ? $logPath : $this->_mLogPath;

		/**
		 * Check if log path is empty.
		 */
		if (empty($log_path)) {
			return FALSE;
		}

		/**
		 * @var string
		 */
		$log_file = $log_path . $log_type . Core::Config()->Get('log.extension');

		/**
		 * Check if log file exists.
		*/
		if (!file_exists($log_file)) {
			/**
			 * Check if log directory is writable.
			 */
			if (!is_writable($log_path)) {
				/**
				 * Trigger fatal error.
				 */
				die('Fatal: Log directory ' . $log_path . ' not writable.');
			}

			/**
			 * Create log file.
			 */
			file_put_contents($log_file, NULL);
		}

		/**
		 * Check if log file is writable.
		 */
		if (!is_writable($log_file)) {
			return FALSE;
		}

		return $log_file;
	}

	/**
	 * Truncate log file.
	 *
	 * @param string $logType
	 * @param string $logPath
	 *
	 * @return bool
	 */
	public function Reset($logType = NULL, $logPath = NULL) {
		/**
		 * Get full log file path.
		 *
		 * @var string
		 */
		$log_file = $this->GetFile($logType, $logPath);

		/**
		 * Stop if log file is not writable.
		*/
		if (FALSE === $log_file) {
			return FALSE;
		}

		/**
		 * Reset log file.
		 *
		 * Deprecated:
		 * $log_handle = fopen($log_file, "wb");
		 * fclose($log_handle);
		 */
		file_put_contents($log_file, NULL);

		return TRUE;
	}

	/**
	 * Write log.
	 *
	 * @param string $logMessage
	 * @param string $logLevel
	 * @param string $logType
	 * @param string $logPath
	 *
	 * @return boolean
	 */
	public function Write($logMessage, $logLevel = NULL, $logType = NULL, $logPath = NULL) {
		/**
		 * @var string
		 */
		$log_level = ($logLevel) ? $logLevel : array_search($this->_mLogLevel, $this->_mLogLevels);

		/**
		 * @var string
		*/
		$log_type = ($logType) ? $logType : $this->_mLogType;

		/**
		 * Get full log file path.
		 *
		 * @var string
		 */
		$log_file = $this->GetFile($log_type, $logPath);

		/**
		 * Stop if log file is not writable, or logging level too low.
		*/
		if ((FALSE === $log_file) || (!array_key_exists($log_level, $this->_mLogLevels)) || ($this->_mLogLevels[$log_level] < $this->_mLogLevel)) {
			return FALSE;
		}

		/**
		 * @var string
		 */
		$timestamp = date(Core::Config()->Get('log.dateformat'));

		/**
		 * @var string
		*/
		$ip = (Core::IsCli()) ? gethostbyname(php_uname('n')) . ' (CLI)' : $_SERVER['REMOTE_ADDR'];

		/**
		 * @var string
		 */
		$log_entry = '[' . $timestamp . '] [' . $ip . '] [' . $log_level . '] ' . $logMessage . PHP_EOL;

		/**
		 * @var mixed
		 */
		$log_handle = fopen($log_file, "ab");

		fwrite($log_handle, $log_entry);

		fclose($log_handle);

		return TRUE;
	}

	/**
	 * Debug.
	 *
	 * Write custom message and data to log.
	 *
	 * @param string $logMessage
	 * @param mixed $logData
	 * @param string $logLevel
	 * @param string $logType
	 *
	 * @return boolean
	 */
	public function Debug($logMessage, $logData, $logLevel = 'debug', $logType = NULL) {

		$this->Write($logMessage, $logLevel, $logType);

		if (!empty($logData)) {

			$this->Write(var_export($logData, TRUE), $logLevel, $logType);
		}

		return TRUE;
	}
}
?>
