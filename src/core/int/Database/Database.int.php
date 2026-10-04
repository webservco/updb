<?php
/**
 * $Id: Database.int.php 222 2016-10-24 09:26:09Z radu $
 *
 * Database interface.
 *
 * All database libraries will implement it
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
 * Interface_Database
 * Database interface.
 * 
 * @author Radu Maier <radu@webserv.co>
 * @package WEb
 * @subpackage WebDatabase
 */
interface Interface_Database {

	/**
	 * Close database connection.
	 * 
	 * @return bool
	 */
	public function Disconnect();

	/**
	 * Escape a string for use in a database query.
	 * 
	 * Deprecates EscapeString();
	 * 
	 * @param string $string        	
	 *
	 * @return string
	 */
	public function Escape($string);

	/**
	 * Get DBMS Version number.
	 * 
	 * @return string
	 */
	public function GetVersion();

	/**
	 * Return the result of performing a raw query.
	 * 
	 * @param string $query        	
	 *
	 * @return mixed
	 */
	public function Query($query);

	/**
	 * Generate an universally unique identifier.
	 * The Id is return as a string without hyphens.
	 * 
	 * @return string
	 */
	public function GenerateId();

	/**
	 * Get the priamry key of a table.
	 * 
	 * @param string $table        	
	 *
	 * @return mixed
	 */
	public function GetPrimaryKey($table);

	/**
	 * Create record in the database.
	 * 
	 * @param mixed $data        	
	 * @param string $table        	
	 *
	 * @return mixed
	 */
	public function Create($data, $table);

	/**
	 * Get data from database.
	 * 
	 * @param mixed $data        	
	 * @param string $table        	
	 * @param array $options        	
	 *
	 * @return mixed
	 */
	public function Retrieve($data, $table, $options = array());

	/**
	 * Update data.
	 * 
	 * @param mixed $data        	
	 * @param string $table        	
	 * @param array $options        	
	 *
	 * @return mixed
	 */
	public function Update($data, $table, $options = array());

	/**
	 * Delete data.
	 * 
	 * @param string $table        	
	 * @param array $options        	
	 *
	 * @return mixed
	 */
	public function Delete($table, $options = array());

	/**
	 * Get only one result from the database.
	 * 
	 * @param mixed $data        	
	 * @param string $table        	
	 * @param array $options        	
	 *
	 * @return mixed
	 */
	public function RetrieveOne($data, $table, $options = array());

	/**
	 * Count method.
	 * 
	 * @param string $data        	
	 * @param string $table        	
	 * @param array $options        	
	 */
	public function Count($data, $table, $options = array());

	/**
	 * Increment a field.
	 * 
	 * @param string $field        	
	 * @param string $table        	
	 * @param array $options        	
	 */
	public function Increment($field, $table, $options = array());

	/**
	 * Get the next value of a field.
	 * 
	 * @param string $field        	
	 * @param string $table        	
	 * @param array $options        	
	 */
	public function GetNext($field, $table, $options = array());

	/**
	 * Get the maximum value of a field.
	 * 
	 * @param string $field        	
	 * @param string $table        	
	 * @param array $options        	
	 */
	public function GetMax($field, $table, $options = array());

	/**
	 * Check if value exists.
	 * 
	 * @param string $table        	
	 * @param string $field        	
	 * @param string $value        	
	 */
	public function ValueExists($table, $field, $value);

	/**
	 * Get insert id.
	 * 
	 * @return int
	 */
	public function GetInsertId();

	/**
	 * Get affected rows.
	 * 
	 * @return int
	 */
	public function GetAffectedRows();
}
?>