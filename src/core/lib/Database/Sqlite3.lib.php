<?php
/**
 * $Id: Sqlite3.lib.php 222 2016-10-24 09:26:09Z radu $
 *
 * SQLite3 database library.
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
if (! defined('_WEB')) {
    /**
     * Stop script execution.
     */
    exit();
}

/**
 * Database_Sqlite3
 * SQLite3 database library.
 *
 * @author Radu Maier <radu@webserv.co>
 * @package WEb
 * @subpackage WebDatabase
 */
class Database_Sqlite3 extends Database implements Interface_Database
{

    /**
     * Setup database connection.
     *
     * @return bool
     */
    protected function _GetLink()
    {
        
        /**
         * Set configuration data.
         */
        $this->_SetData();
        
        $db_path = $this->_mDbDatabase . '.db';
        
        if (! is_readable($db_path)) {
            /**
             *
             * @var string
             */
            $message = __('Database library: Database file is not readable.', 'WEb');
            
            throw new ErrorException($message);
            
            return FALSE;
        }
        
        /**
         * Set database link identifier.
         *
         * @var resource
         */
        try {
            $db_link = new SQLite3($db_path);
        } catch (Exception $e) {
            /**
             *
             * @var string
             */
            $message = sprintf(__('Database library: Error opening database %s.', 'WEb'), $this->_mDbDatabase) . ' ' . $e->getMessage();
            
            throw new ErrorException($message);
            
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
        $db_message = sprintf(__('Database library: Database connection opened (driver: %s).', 'WEb'), Core::Config()->Get('database.type'));
        
        Core::Log()->Write($db_message, 'debug');
        
        return TRUE;
    }

    /**
     * (non-PHPdoc)
     *
     * @see Interface_Database::Disconnect()
     */
    public function Disconnect()
    {
        return $this->_mDbLink->close();
    }

    /**
     * (non-PHPdoc)
     *
     * @see Interface_Database::Escape()
     */
    public function Escape($string)
    {
        
        /**
         * Open database connection if not already opened.
         */
        $this->Connect();
        
        return $this->_mDbLink->escapeString($string);
    }

    /**
     * (non-PHPdoc)
     *
     * @see Interface_Database::Escape()
     * @deprecated
     */
    public function EscapeString($string)
    {
        return $this->Escape($string);
    }

    /**
     * (non-PHPdoc)
     *
     * @see Interface_Database::GetVersion()
     */
    public function GetVersion()
    {
        
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
    public function Query($query)
    {
        
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
    public function GenerateId()
    {
        
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
    public function GetPrimaryKey($table)
    {
        
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
    public function Create($data, $table)
    {
        
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
    public function Retrieve($data, $table, $options = array())
    {
        
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
    public function Update($data, $table, $options = array())
    {
        
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
    public function Delete($table, $options = array())
    {
        
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
    public function RetrieveOne($data, $table, $options = array())
    {
        
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
    public function Count($data, $table, $options = array())
    {
        
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
    public function Increment($field, $table, $options = array())
    {
        
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
    public function GetNext($field, $table, $options = array())
    {
        
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
    public function GetMax($field, $table, $options = array())
    {
        
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
    public function ValueExists($table, $field, $value)
    {
        
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
    public function GetInsertId()
    {
        
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
    public function GetAffectedRows()
    {
        
        /**
         * TODO implement method.
         */
        throw new ErrorException(__('Database library: functionality not implemented', 'WEb'));
    }
}
?>