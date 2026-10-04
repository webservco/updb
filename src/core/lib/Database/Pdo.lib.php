<?php
/**
 * $Id: Pdo.lib.php 222 2016-10-24 09:26:09Z radu $
 *
 * PHP Data Objects library.
 *
 * @author     Radu Maier <radu@webserv.co>
 * @copyright  Copyright (c) 2008 - 2014, Radu Maier
 * @license    http://www.opensource.org/licenses/bsd-license.php
 * @package    WEb
 * @subpackage WebDatabase
 * @version    $Revision: 222 $
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
 * Database_Pdo
 * PHP Data Objects library.
 * 
 * @author Radu Maier <radu@webserv.co>
 * @package WEb
 * @subpackage WebDatabase
 */
class Database_Pdo extends Database implements Interface_Database {

	/**
	 * Setup database connection.
	 * 
	 * @return bool
	 */
	protected function _GetLink() {

		/**
		 * Set configuration data.
		 */
		$this->_SetData();
		
		/**
		 *
		 * @var string
		 */
		$db_type = Core::Config()->Get('database.type');
		
		if (FALSE !== strpos($db_type, '_')) {
			/**
			 *
			 * @var array
			 */
			$parts = explode('_', $db_type);
			
			/**
			 *
			 * @var string
			 */
			$db_type = strtolower($parts[0]);
		}
		
		/**
		 * Set MySQL default port if we have no custom port in configuration settings.
		 * 
		 * @var string
		 */
		$db_port = ('' != $this->_mDbPort) ? $this->_mDbPort : '3306';
		
		/**
		 * Set database link identifier.
		 * 
		 * @var resource
		 */
		try {
			$db_link = new PDO("{$db_type}:host={$this->_mDbHost};dbname={$this->_mDbDatabase};port={$this->_mDbPort}", $this->_mDbUser, $this->_mDbPassword, array(
				PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
			));
		} catch (PDOException $e) {
			throw new ErrorException($e->GetMessage(), 0, $e->GetCode(), $e->getFile(), $e->getLine());
			
			return FALSE;
		}
		
		/**
		 * Set database link identifier.
		 * 
		 * @var resource
		 */
		$this->_mDbLink = $db_link;
		
		/**
		 *
		 * @var string
		 */
		$db_message = sprintf(__('Database library: Database connection opened (driver: %s).', 'WEb'), Core::Config()->Get('db.type'));
		
		Core::Log()->Write($db_message, 'debug');
		
		return TRUE;
	}

	/**
	 * (non-PHPdoc)
	 * 
	 * @see Interface_Database::Disconnect()
	 */
	public function Disconnect() {

		$this->_mDbLink = NULL;
		
		return TRUE;
	}

	/**
	 * (non-PHPdoc)
	 * 
	 * @see Interface_Database::Escape()
	 */
	public function Escape($string) {

		/**
		 * Open database connection if not already opened.
		 */
		$this->Connect();
		
		return $this->_mDbLink->quote($string);
	}
	
	/**
     * (non-PHPdoc)
     *
     * @see Interface_Database::Escape()
     * @deprecated
     */
	public function EscapeString($string) {

		return $this->Escape($string);
	}

	/**
	 * (non-PHPdoc)
	 * 
	 * @see Interface_Database::GetVersion()
	 */
	public function GetVersion() {

		/**
		 * TODO implement method.
		 */
		throw new ErrorException(__('Database library: functionality not implemented', 'WEb'));
	}

	/**
	 * (non-PHPdoc)
	 * 
	 * @see Interface_Database::Query()
	 */
	public function Query($query) {

		/**
		 * TODO implement method.
		 */
		throw new ErrorException(__('Database library: functionality not implemented', 'WEb'));
	}

	/**
	 * (non-PHPdoc)
	 * 
	 * @see Interface_Database::GenerateId()
	 */
	public function GenerateId() {

		/**
		 * TODO implement method.
		 */
		throw new ErrorException(__('Database library: functionality not implemented', 'WEb'));
	}

	/**
	 * (non-PHPdoc)
	 * 
	 * @see Interface_Database::GetPrimaryKey()
	 */
	public function GetPrimaryKey($table) {

		/**
		 * TODO implement method.
		 */
		throw new ErrorException(__('Database library: functionality not implemented', 'WEb'));
	}

	/**
	 * (non-PHPdoc)
	 * 
	 * @see Interface_Database::Create()
	 */
	public function Create($data, $table) {

		/**
		 * TODO implement method.
		 */
		throw new ErrorException(__('Database library: functionality not implemented', 'WEb'));
	}

	/**
	 * (non-PHPdoc)
	 * 
	 * @see Interface_Database::Retrieve()
	 */
	public function Retrieve($data, $table, $options = array()) {

		/**
		 * TODO implement method.
		 */
		throw new ErrorException(__('Database library: functionality not implemented', 'WEb'));
	}

	/**
	 * (non-PHPdoc)
	 * 
	 * @see Interface_Database::Update()
	 */
	public function Update($data, $table, $options = array()) {

		/**
		 * TODO implement method.
		 */
		throw new ErrorException(__('Database library: functionality not implemented', 'WEb'));
	}

	/**
	 * (non-PHPdoc)
	 * 
	 * @see Interface_Database::Delete()
	 */
	public function Delete($table, $options = array()) {

		/**
		 * TODO implement method.
		 */
		throw new ErrorException(__('Database library: functionality not implemented', 'WEb'));
	}

	/**
	 * (non-PHPdoc)
	 * 
	 * @see Interface_Database::RetrieveOne()
	 */
	public function RetrieveOne($data, $table, $options = array()) {

		/**
		 * TODO implement method.
		 */
		throw new ErrorException(__('Database library: functionality not implemented', 'WEb'));
	}

	/**
	 * (non-PHPdoc)
	 * 
	 * @see Interface_Database::Count()
	 */
	public function Count($data, $table, $options = array()) {

		/**
		 * TODO implement method.
		 */
		throw new ErrorException(__('Database library: functionality not implemented', 'WEb'));
	}

	/**
	 * (non-PHPdoc)
	 * 
	 * @see Interface_Database::Increment()
	 */
	public function Increment($field, $table, $options = array()) {

		/**
		 * TODO implement method.
		 */
		throw new ErrorException(__('Database library: functionality not implemented', 'WEb'));
	}

	/**
	 * (non-PHPdoc)
	 * 
	 * @see Interface_Database::GetNext()
	 */
	public function GetNext($field, $table, $options = array()) {

		/**
		 * TODO implement method.
		 */
		throw new ErrorException(__('Database library: functionality not implemented', 'WEb'));
	}

	/**
	 * (non-PHPdoc)
	 * 
	 * @see Interface_Database::GetMax()
	 */
	public function GetMax($field, $table, $options = array()) {

		/**
		 * TODO implement method.
		 */
		throw new ErrorException(__('Database library: functionality not implemented', 'WEb'));
	}

	/**
	 * (non-PHPdoc)
	 * 
	 * @see Interface_Database::ValueExists()
	 */
	public function ValueExists($table, $field, $value) {

		/**
		 * TODO implement method.
		 */
		throw new ErrorException(__('Database library: functionality not implemented', 'WEb'));
	}

	/**
	 * (non-PHPdoc)
	 * 
	 * @see Interface_Database::GetInsertId()
	 */
	public function GetInsertId() {

		/**
		 * TODO implement method.
		 */
		throw new ErrorException(__('Database library: functionality not implemented', 'WEb'));
	}

	/**
	 * (non-PHPdoc)
	 * 
	 * @see Interface_Database::GetAffectedRows()
	 */
	public function GetAffectedRows() {

		/**
		 * TODO implement method.
		 */
		throw new ErrorException(__('Database library: functionality not implemented', 'WEb'));
	}
}
?>