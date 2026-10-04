<?php
/**
 * $Id: Database.lib.php 168 2014-04-26 07:28:51Z radu $
 *
 * Main database library.
 *
 * All custom database libraries will extend this library.
 *
 * @author     Radu Maier <radu@webserv.co>
 * @copyright  Copyright (c) 2008 - 2014, Radu Maier
 * @license    http://www.opensource.org/licenses/bsd-license.php
 * @package    WEb
 * @subpackage WebDatabase
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
 * Database
 *
 * Database library.
 *
 * @author     Radu Maier <radu@webserv.co>
 * @package    WEb
 * @subpackage WebDatabase
 *
 */
class Database extends Library {
	/**
	 * Stores the database link identifier.
	 *
	 * @var mixed
	 */
	protected $_mDbLink;

	/**
	 * Database host.
	 *
	 * @var string
	 */
	protected $_mDbHost;

	/**
	 * Database port.
	 *
	 * @var string
	 */
	protected $_mDbPort;

	/**
	 * Database user.
	 *
	 * @var string
	 */
	protected $_mDbUser;

	/**
	 * Database password.
	 *
	 * @var string
	 */
	protected $_mDbPassword;

	/**
	 * Database name.
	 *
	 * @var string
	 */
	protected $_mDbDatabase;

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
	 * Set configuration data.
	 *
	 * @return bool
	 */
	protected function _SetData() {
		/**
		 * @var array
		 */
		$data = Core::Config()->Get('database');

		/**
		 * @var string
		 */
		$this->_mDbHost = $data['host'];

		/**
		 * @var string
		 */
		$this->_mDbPort = $data['port'];

		/**
		 * @var string
		 */
		$this->_mDbUser = $data['user'];

		/**
		 * @var string
		 */
		$this->_mDbPassword = $data['password'];

		/**
		 * @var string
		 */
		$this->_mDbDatabase = $data['database'];

		return TRUE;
	}

	/**
	 * Check if database connection is already opened.
	 *
	 * Open it if necessary.
	 *
	 * @return bool
	 */
	public function Connect() {
		if (NULL == $this->_mDbLink) {
			return $this->_GetLink();
		}

		return TRUE;
	}

}
?>