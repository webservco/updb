<?php
/**
 * $Id: Mysqli.lib.php 222 2016-10-24 09:26:09Z radu $
 *
 * MySQL Improved database library.
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
 * Database_Mysqli
 * MySQL Improved database library.
 *
 * @author Radu Maier <radu@webserv.co>
 * @package WEb
 * @subpackage WebDatabase
 */
class Database_Mysqli extends Database implements Interface_Database
{

    /**
     * Stores the mysqli database statement.
     *
     * @var mixed
     */
    protected $_mDbStmt;

    /**
     * Comparison operators.
     *
     * @var array
     */
    private $_mCompOperators = array(
        'eq' => '=',
        'not' => '!=',
        'lt' => '<',
        'gt' => '>',
        'lte' => '<=',
        'gte' => '>='
    );

    /**
     * Order by.
     *
     * @var array
     */
    private $_mOrderBy = array(
        'asc' => 'ASC',
        'desc' => 'DESC'
    );

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
        $db_link = new mysqli($this->_mDbHost, $this->_mDbUser, $this->_mDbPassword, $this->_mDbDatabase, $db_port);
        
        if (0 != $db_link->connect_errno) {
            /**
             *
             * @var string
             */
            $message = __('Database library: Error connecting to database management server.', 'WEb') . ' ' . $db_link->connect_error;
            
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
        
        /**
         * UTF Compatibility.
         */
        $this->_mDbLink->set_charset('utf8');
        
        /**
         * Check if we should fic GROUP_CONCAT limit
         */
        if (1 == Core::Config()->Get('database.fix_gc')) {
            $this->_mDbLink->query("SET SESSION group_concat_max_len = 1048576");
        }
        
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
        
        return $this->_mDbLink->real_escape_string($string);
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
         * Open database connection if not already opened.
         */
        $this->Connect();
        
        return $this->_mDbLink->server_info;
    }

    public function Query($query)
    {
        
        /**
         * Open database connection if not already opened.
         */
        $this->Connect();
        
        $result = $this->_mDbLink->query($query);
        
        if (! $result) {
            
            Core::Log()->Write($query, 'info');
            
            $message = __('Database library: Error executing query.', 'WEb') . ' ' . $this->_mDbLink->error;
            
            throw new ErrorException($message);
        }
        
        Core::Log()->Write($query, 'debug');
        
        return $result;
    }

    /**
     * (non-PHPdoc)
     *
     * @see Interface_Database::GenerateId()
     */
    public function GenerateId()
    {
        
        /**
         * Open database connection if not already opened.
         */
        $this->Connect();
        
        $result = $this->_mDbLink->query("SELECT REPLACE(UUID(), '-', '')");
        
        if (! $result) {
            
            $message = __('Database library: Error generating UUID:', 'WEb') . ' ' . $this->_mDbLink->error;
            
            throw new ErrorException($message);
        }
        
        /**
         *
         * @var array
         */
        $row = $result->fetch_row();
        
        $result->close();
        
        return $row[0];
    }

    /**
     * (non-PHPdoc)
     *
     * @see Interface_Database::GetPrimaryKey()
     */
    public function GetPrimaryKey($table)
    {
        
        /**
         * Open database connection if not already opened.
         */
        $this->Connect();
        
        /**
         *
         * @var string
         */
        $query = "SELECT COLUMN_NAME AS primary_key FROM INFORMATION_SCHEMA.STATISTICS
		WHERE INDEX_NAME='PRIMARY'
		AND INDEX_SCHEMA='{$this->_mDbDatabase}'
		AND TABLE_NAME='{$table}'";
        
        /**
         *
         * @var resource
         */
        $result = $this->_mDbLink->query($query);
        
        if (! $result) {
            
            $message = __('Database library: Error getting primary key:', 'WEb') . ' ' . $this->_mDbLink->error;
            
            throw new ErrorException($message);
        }
        
        /**
         *
         * @var array
         */
        $row = $result->fetch_row();
        
        $result->close();
        
        return $row[0];
    }

    /**
     * (non-PHPdoc)
     *
     * @see Interface_Database::Create()
     */
    public function Create($data, $table)
    {
        
        /**
         * Open database connection if not already opened.
         */
        $this->Connect();
        
        /**
         *
         * @var array
         */
        $fields = array();
        
        /**
         *
         * @var array
         */
        $values = array();
        
        foreach ($data as $field => $value) {
            /**
             * Ignore NULL values.
             */
            if (isset($value)) {
                if ($this->_IsBinaryField($field)) {
                    /**
                     *
                     * @var array
                     */
                    $fields[] = $this->_PrepareBinaryField($field);
                    /**
                     *
                     * @var array
                     */
                    $values[] = "UNHEX('{$this->EscapeString($value)}')";
                } else {
                    /**
                     *
                     * @var array
                     */
                    $fields[] = $field;
                    /**
                     *
                     * @var array
                     */
                    $values[] = "'{$this->EscapeString($value)}'";
                }
            }
        }
        
        /**
         *
         * @var string
         */
        $qf_fields = implode(', ', $fields);
        
        /**
         *
         * @var string
         */
        $qf_values = implode(', ', $values);
        
        /**
         *
         * @var string
         */
        $query = "INSERT INTO {$table} ({$qf_fields}) VALUES ({$qf_values})";
        
        Core::Log()->Write(__('Database Library: Preparing create query.', 'WEb'), 'debug');
        
        Core::Log()->Write($query, 'debug');
        
        /**
         *
         * @var resource
         */
        $this->_mDbStmt = $this->_mDbLink->prepare($query);
        
        if (FALSE === $this->_mDbStmt) {
            return FALSE;
        }
        
        if (! $this->_mDbStmt->execute()) {
            
            $message = __('Database library: Error executing statement:', 'WEb') . ' ' . $this->_mDbLink->error;
            
            throw new ErrorException($message);
        }
        
        $this->_mDbStmt->close();
        
        /**
         * Perhaps needed in the future.
         * return $this->_mDbLink->insert_id;
         */
        
        return TRUE;
    }

    /**
     * (non-PHPdoc)
     *
     * @see Interface_Database::Retrieve()
     */
    public function Retrieve($data, $table, $options = array())
    {
        
        /**
         * Open database connection if not already opened.
         */
        $this->Connect();
        
        /**
         * Needed for result binding.
         *
         * @var array
         */
        $data = (! is_array($data)) ? array(
            $data
        ) : $data;
        
        /**
         *
         * @var array
         */
        $fields = array();
        
        foreach ($data as $id => $field) {
            /**
             * Split the column name by the cast operator.
             *
             * @var array
             */
            $raw_field = explode(':', $field);
            
            /**
             * Set column name
             *
             * @var string
             */
            $col = $raw_field[0];
            
            if ($this->_IsBinaryField($col)) {
                /**
                 *
                 * @var array
                 */
                $col = $this->_PrepareBinaryField($col, TRUE);
            }
            
            /**
             * Check if we sould cast the column name.
             */
            if (! empty($raw_field[1])) {
                /**
                 * Set column name for the query.
                 *
                 * @var string
                 */
                $col = "{$col} AS {$raw_field[1]}";
                
                /**
                 * Set field name for the result.
                 *
                 * @var string
                 */
                $data[$id] = $raw_field[1];
            }
            
            /**
             *
             * @var array
             */
            $fields[] = $col;
        }
        
        /**
         *
         * @var string
         */
        $columns = implode(', ', $fields);
        
        /**
         * Set default condition query fill.
         *
         * @var string
         */
        $qf_conditions = $this->_PrepareConditionsString($options);
        
        /**
         * Set default order query fill.
         *
         * @var string
         */
        $qf_order = $this->_PrepareOrderString($options);
        
        /**
         * Set default limit query fill.
         *
         * @var string
         */
        $qf_limit = $this->_PrepareLimitString($options);
        
        /**
         *
         * @var string
         */
        $query = "SELECT {$columns} FROM {$table}{$qf_conditions}{$qf_order}{$qf_limit}";
        
        Core::Log()->Write(__('Database Library: Preparing retrieve query.', 'WEb'), 'debug');
        
        Core::Log()->Write($query, 'debug');
        
        $this->_mDbStmt = $this->_mDbLink->prepare($query);
        
        if (FALSE === $this->_mDbStmt) {
            return FALSE;
        }
        
        if (! $this->_mDbStmt->execute()) {
            
            $message = __('Database library: Error executing statement:', 'WEb') . ' ' . $this->_mDbLink->error;
            
            throw new ErrorException($message);
        }
        
        /**
         *
         * @var array
         */
        $parameters = array();
        
        /**
         *
         * @var array
         */
        $params = $data;
        
        /**
         * Reset hack
         *
         * @var array
         */
        $data = array();
        
        foreach ($params as $key => $val) {
            /**
             *
             * @var array
             */
            $parameters[] = &$data[$val];
        }
        
        /**
         * Set field names to use in result.
         */
        call_user_func_array(array(
            $this->_mDbStmt,
            'bind_result'
        ), $this->_ArrayValuesAsRefference($parameters));
        
        /**
         * Init empty result.
         *
         * @var array
         */
        $results = array();
        
        while ($this->_mDbStmt->fetch()) {
            /**
             *
             * @var array
             */
            $result = array();
            
            foreach ($data as $key => $val) {
                /**
                 *
                 * @var array
                 */
                $result[$key] = $val;
            }
            /**
             *
             * @var array
             */
            $results[] = $result;
        }
        
        $this->_mDbStmt->close();
        
        if (empty($results)) {
            return array();
        }
        
        return $results;
    }

    /**
     * (non-PHPdoc)
     *
     * @see Interface_Database::Update()
     */
    public function Update($data, $table, $options = array())
    {
        
        /**
         * Open database connection if not already opened.
         */
        $this->Connect();
        
        /**
         * Set default values query fill.
         *
         * @var string
         */
        $qf_data = NULL;
        
        foreach ($data as $key => $value) {
            /**
             * Ignore NULL values.
             */
            if (isset($value)) {
                /**
                 *
                 * @var string
                 */
                $value = $this->EscapeString($value);
                
                if ($this->_IsBinaryField($key)) {
                    /**
                     *
                     * @var string
                     */
                    $key = $this->_PrepareBinaryField($key);
                    
                    /**
                     *
                     * @var string
                     */
                    $value = "UNHEX('$value')";
                } else {
                    /**
                     *
                     * @var string
                     */
                    $value = "'{$value}'";
                }
                
                /**
                 *
                 * @var string
                 */
                $values[] = "{$key} = {$value}";
            }
        }
        
        /**
         *
         * @var string
         */
        $qf_data .= implode(', ', $values);
        
        /**
         * Set default condition query fill.
         *
         * @var string
         */
        $qf_conditions = $this->_PrepareConditionsString($options);
        
        /**
         * Set default limit query fill.
         *
         * @var string
         */
        $qf_limit = $this->_PrepareLimitString($options);
        
        /**
         *
         * @var string
         */
        $query = "UPDATE {$table} SET {$qf_data}{$qf_conditions}{$qf_limit}";
        
        Core::Log()->Write(__('Database Library: Preparing update query.', 'WEb'), 'debug');
        
        Core::Log()->Write($query, 'debug');
        
        $this->_mDbStmt = $this->_mDbLink->prepare($query);
        
        if (! $this->_mDbStmt->execute()) {
            
            $message = __('Database library: Error executing statement:', 'WEb') . ' ' . $this->_mDbLink->error;
            
            throw new ErrorException($message);
        }
        
        $this->_mDbStmt->close();
        
        return $this->_mDbLink->affected_rows;
    }

    /**
     * (non-PHPdoc)
     *
     * @see Interface_Database::Delete()
     */
    public function Delete($table, $options = array())
    {
        
        /**
         * Open database connection if not already opened.
         */
        $this->Connect();
        
        /**
         * Set default condition query fill.
         *
         * @var string
         */
        $qf_conditions = $this->_PrepareConditionsString($options);
        
        /**
         * Set default order query fill.
         *
         * @var string
         */
        $qf_order = $this->_PrepareOrderString($options);
        
        /**
         * Set default limit query fill.
         *
         * @var string
         */
        $qf_limit = $this->_PrepareLimitString($options);
        
        /**
         *
         * @var string
         */
        $query = "DELETE FROM {$table}{$qf_conditions}{$qf_order}{$qf_limit}";
        
        Core::Log()->Write(__('Database Library: Preparing delete query.', 'WEb'), 'debug');
        
        Core::Log()->Write($query, 'debug');
        
        /**
         *
         * @var resource
         */
        $this->_mDbStmt = $this->_mDbLink->prepare($query);
        
        if (! $this->_mDbStmt->execute()) {
            
            $message = __('Database library: Error executing statement:', 'WEb') . ' ' . $this->_mDbLink->error;
            
            throw new ErrorException($message);
        }
        
        $this->_mDbStmt->close();
        
        return $this->_mDbLink->affected_rows;
    }

    /**
     * (non-PHPdoc)
     *
     * @see Interface_Database::RetrieveOne()
     */
    public function RetrieveOne($data, $table, $options = array())
    {
        
        /**
         * Open database connection if not already opened.
         */
        $this->Connect();
        
        /**
         * Make sure the limit isset.
         *
         * @var array
         */
        $options['limit'] = 1;
        
        /**
         * Get result.
         *
         * @var array
         */
        $result = $this->Retrieve($data, $table, $options);
        
        return (empty($result)) ? FALSE : $result[0];
    }

    /**
     * (non-PHPdoc)
     *
     * @see Interface_Database::Count()
     */
    public function Count($data, $table, $options = array())
    {
        
        /**
         * Open database connection if not already opened.
         */
        $this->Connect();
        
        /**
         *
         * @var string
         */
        $data = "COUNT({$data}):total";
        
        /**
         * Get result.
         *
         * @var array
         */
        $result = $this->Retrieve($data, $table, $options);
        
        return (empty($result)) ? FALSE : $result[0];
    }

    /**
     * (non-PHPdoc)
     *
     * @see Interface_Database::Increment()
     */
    public function Increment($field, $table, $options = array())
    {
        
        /**
         * Open database connection if not already opened.
         */
        $this->Connect();
        
        /**
         * Set default condition query fill.
         *
         * @var string
         */
        $qf_conditions = $this->_PrepareConditionsString($options);
        
        /**
         * Set default limit query fill.
         *
         * @var string
         */
        $qf_limit = $this->_PrepareLimitString($options);
        
        /**
         *
         * @var string
         */
        $query = "UPDATE {$table} SET {$field} = $field+1{$qf_conditions}{$qf_limit}";
        
        Core::Log()->Write(__('Database Library: Preparing increment query.', 'WEb'), 'debug');
        
        Core::Log()->Write($query, 'debug');
        
        $this->_mDbStmt = $this->_mDbLink->prepare($query);
        
        if (! $this->_mDbStmt->execute()) {
            
            $message = __('Database library: Error executing statement:', 'WEb') . ' ' . $this->_mDbLink->error;
            
            throw new ErrorException($message);
        }
        
        $this->_mDbStmt->close();
        
        return $this->_mDbLink->affected_rows;
    }

    /**
     * (non-PHPdoc)
     *
     * @see Interface_Database::GetNext()
     */
    public function GetNext($field, $table, $options = array())
    {
        
        /**
         *
         * @var string
         */
        $field = "MAX({$field})+1:next_item";
        
        /**
         *
         * @var mixed
         */
        $result = $this->RetrieveOne($field, $table, $options);
        
        return (! empty($result['next_item'])) ? $result['next_item'] : 1;
    }

    /**
     * (non-PHPdoc)
     *
     * @see Interface_Database::GetMax()
     */
    public function GetMax($field, $table, $options = array())
    {
        
        /**
         *
         * @var string
         */
        $field = "MAX({$field}):max_item";
        
        /**
         *
         * @var mixed
         */
        $result = $this->RetrieveOne($field, $table, $options);
        
        return (! empty($result['max_item'])) ? $result['max_item'] : NULL;
    }

    /**
     * (non-PHPdoc)
     *
     * @see Interface_Database::ValueExists()
     */
    public function ValueExists($table, $field, $value)
    {
        $options['conditions'][] = array(
            $field,
            $value
        );
        
        $result = $this->RetrieveOne($field, $table, $options);
        
        return ! empty($result[$field]) ? TRUE : FALSE;
    }

    /**
     * (non-PHPdoc)
     *
     * @see Interface_Database::GetInsertId()
     */
    public function GetInsertId()
    {
        $this->Connect();
        
        return $this->_mDbLink->insert_id;
    }

    /**
     * (non-PHPdoc)
     *
     * @see Interface_Database::GetAffectedRows()
     */
    public function GetAffectedRows()
    {
        $this->Connect();
        
        return $this->_mDbLink->affected_rows;
    }

    /**
     * PHP 5.3 fix call_user_func_array + mysqli bind_param
     *
     * @author http://ca.php.net/manual/en/mysqli-stmt.bind-param.php#96770
     * @param array $data            
     *
     * @return array
     */
    private function _ArrayValuesAsRefference($data = array())
    {
        if (strnatcmp(phpversion(), '5.3') >= 0) {
            /**
             *
             * @var array
             */
            $refs = array();
            
            foreach ($data as $key => $value) {
                /**
                 *
                 * @var array
                 */
                $refs[$key] = &$data[$key];
            }
            return $refs;
        }
        
        return $data;
    }

    /**
     * Prepare conditions query filler.
     *
     * @param array $options            
     *
     * @return string
     */
    private function _PrepareConditionsString($options = array())
    {
        
        /**
         *
         * @var string
         */
        $qf_conditions = NULL;
        
        /**
         * Check if we have any conditions.
         */
        if (! empty($options['conditions'])) {
            /**
             *
             * @var string
             */
            $qf_conditions .= ' WHERE ';
            
            /**
             *
             * @var array
             */
            $conditions = array();
            
            if (! is_array($options['conditions'][0])) {
                /**
                 *
                 * @var string
                 */
                $comp_operator = (array_key_exists(2, $options['conditions']) && array_key_exists($options['conditions'][2], $this->_mCompOperators)) ? $this->_mCompOperators[$options['conditions'][2]] : $this->_mCompOperators['eq'];
                
                /**
                 *
                 * @var string
                 */
                $value = $this->EscapeString($options['conditions'][1]);
                
                /**
                 * Check binary field
                 */
                if ($this->_IsBinaryField($options['conditions'][0])) {
                    /**
                     *
                     * @var array
                     */
                    $options['conditions'][0] = $this->_PrepareBinaryField($options['conditions'][0]);
                    /**
                     *
                     * @var array
                     */
                    $options['conditions'][1] = "UNHEX('{$value}')";
                } else {
                    /**
                     *
                     * @var array
                     */
                    $options['conditions'][1] = "'{$value}'";
                }
                /**
                 *
                 * @var string
                 */
                $qf_conditions .= "{$options['conditions'][0]} {$comp_operator} {$options['conditions'][1]}";
            } else {
                foreach ($options['conditions'] as $dump => $condition) {
                    /**
                     *
                     * @var string
                     */
                    $comp_operator = (array_key_exists(2, $condition) && array_key_exists($condition[2], $this->_mCompOperators)) ? $this->_mCompOperators[$condition[2]] : $this->_mCompOperators['eq'];
                    
                    /**
                     *
                     * @var string
                     */
                    $value = $this->EscapeString($condition[1]);
                    
                    /**
                     * Check binary field
                     */
                    if ($this->_IsBinaryField($condition[0])) {
                        /**
                         *
                         * @var array
                         */
                        $condition[0] = $this->_PrepareBinaryField($condition[0]);
                        
                        /**
                         *
                         * @var array
                         */
                        $condition[1] = "UNHEX('{$value}')";
                    } else {
                        /**
                         *
                         * @var array
                         */
                        $condition[1] = $this->EscapeString($condition[1]);
                        
                        /**
                         *
                         * @var array
                         */
                        $condition[1] = "'{$value}'";
                    }
                    /**
                     *
                     * @var array
                     */
                    $conditions[] = "{$condition[0]} {$comp_operator} {$condition[1]}";
                }
                /**
                 *
                 * @var string
                 */
                $qf_conditions .= implode(' AND ', $conditions);
            }
        }
        
        return $qf_conditions;
    }

    /**
     * Prepare order query filler.
     *
     * @param array $options            
     *
     * @return string
     */
    private function _PrepareOrderString($options = array())
    {
        
        /**
         *
         * @var string
         */
        $qf_order = NULL;
        
        /**
         * Check if we have ordering.
         */
        if (! empty($options['order'])) {
            /**
             *
             * @var string
             */
            $qf_order .= ' ORDER BY ';
            
            if (! is_array($options['order'])) {
                /**
                 * Check binary field
                 */
                if ($this->_IsBinaryField($options['order'])) {
                    /**
                     *
                     * @var array
                     */
                    $options['order'] = $this->_PrepareBinaryField($options['order']);
                }
                /**
                 *
                 * @var string
                 */
                $qf_order .= "{$options['order']}  {$this->_mOrderBy['asc']}";
            } else {
                
                /**
                 *
                 * @var array
                 */
                $ordering = array();
                
                foreach ($options['order'] as $order) {
                    if (! is_array($order)) {
                        /**
                         * Check binary field
                         */
                        if ($this->_IsBinaryField($order)) {
                            /**
                             *
                             * @var string
                             */
                            $order = $this->_PrepareBinaryField($order);
                        }
                        
                        /**
                         *
                         * @var array
                         */
                        $ordering[] = "{$order} {$this->_mOrderBy['asc']}";
                    } else {
                        /**
                         *
                         * @var string
                         */
                        $order_by = (array_key_exists(1, $order) && array_key_exists($order[1], $this->_mOrderBy)) ? $this->_mOrderBy[$order[1]] : $this->_mOrderBy['asc'];
                        
                        /**
                         * Check binary field
                         */
                        if ($this->_IsBinaryField($order[0])) {
                            /**
                             *
                             * @var string
                             */
                            $order[0] = $this->_PrepareBinaryField($order[0]);
                        }
                        
                        /**
                         *
                         * @var array
                         */
                        $ordering[] = "{$order[0]} {$order_by}";
                    }
                }
                
                /**
                 *
                 * @var string
                 */
                $qf_order .= implode(', ', $ordering);
            }
        }
        
        return $qf_order;
    }

    /**
     * Prepare limit query filler.
     *
     * @param array $options            
     *
     * @return string
     */
    private function _PrepareLimitString($options = array())
    {
        
        /**
         *
         * @var string
         */
        $qf_limit = NULL;
        
        /**
         * Check if we have a limit.
         */
        if (! empty($options['limit'])) {
            /**
             *
             * @var string
             */
            $qf_limit .= ' LIMIT ';
            
            if (is_array($options['limit'])) {
                /**
                 *
                 * @var string
                 */
                $qf_limit .= (array_key_exists(1, $options['limit'])) ? "{$options['limit'][1]}, {$options['limit'][0]}" : "{$options['limit'][0]}";
            } else {
                /**
                 *
                 * @var string
                 */
                $qf_limit .= $options['limit'];
            }
        }
        
        return $qf_limit;
    }

    /**
     * Check if field is binary.
     *
     * @param string $field            
     *
     * @return boolen
     */
    private function _IsBinaryField($field)
    {
        return (0 === strpos($field, 'bin.'));
    }

    /**
     * Parse binary field.
     *
     * @param string $field            
     * @param boolean $retrieve            
     *
     * @return string
     */
    private function _PrepareBinaryField($field, $retrieve = FALSE)
    {
        
        /**
         *
         * @var string
         */
        $field = str_replace('bin.', NULL, $field);
        
        return ($retrieve) ? "HEX({$field})" : $field;
    }
}
?>