<?php
/**
 * $Id: Form.lib.php 204 2015-12-18 17:12:37Z radu $
 * Form handling library.
 * 
 * @author Radu Maier <radu@webserv.co>
 * @copyright Copyright (c) 2008 - 2014, Radu Maier
 * @license http://www.opensource.org/licenses/bsd-license.php
 * @package WEb
 * @subpackage WebForm
 * @version $Revision: 204 $
 * @since Version 5
 * @access public
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
 * Form
 * Form handling library.
 *
 * @author Radu Maier <radu@webserv.co>
 * @package WEb
 * @subpackage WebForm
 */
class Form extends Library
{

    /**
     * Form sending method
     *
     * @var string
     */
    private $_mMethod;

    /**
     * Request query.
     * Used to store form data for GET method.
     *
     * @var array
     */
    private $_mRequestQuery = array();

    /**
     * Actual form data.
     * If validation is made, contains actual form data.
     * Otherwise it contains NULL values or default data.
     *
     * @var array
     */
    private $_mData = array();

    /**
     * Form meta data.
     * Contains aditional form info,
     * such as titles, description, required fields.
     *
     * @var string
     */
    private $_mMeta = array();

    /**
     * Form data for JavaScript click/blur functionality.
     *
     * Key is item id, value is text to show/hide.
     *
     * @var array
     */
    private $_mClickBlur = array();

    /**
     * Form filters.
     *
     * @var array
     */
    private $_mFilters = array();

    /**
     * Form validation conditions.
     *
     * @var array
     */
    private $_mConditions = array();

    /**
     * Form errors.
     * Contains all form errors resulting from validation.
     *
     * @var array
     */
    private $_mErrors = array();

    /**
     * Filtered form data.
     * Contains the form data with the filters applied.
     * This is usually used for actual data manipulation (eg db storage)
     * after form validation.
     *
     * @var array
     */
    private $_mDataFiltered = array();

    /**
     * Implement GetInstance() method.
     * This is required for PHP < 5.3 compatibility (no late static binding)
     *
     * @param string $className
     *            class name to instantiate
     * @return object
     */
    public static function GetInstance($className = __CLASS__)
    {
        
        /**
         * Call parent method with current class name as parameter.
         */
        return parent::_GetInstance($className);
    }

    /**
     * Set form handling method
     *
     * @param string $method            
     *
     * @return bool
     */
    public function SetMethod($method = 'post', $requestQuery = array())
    {
        
        /**
         * Checkif we have a valid method.s
         */
        if (! in_array($method, array(
            'post',
            'get'
        ))) {
            return FALSE;
        }
        
        /**
         *
         * @var string
         */
        $this->_mMethod = $method;
        
        if ('get' == $this->_mMethod) {
            
            $this->_mRequestQuery = $requestQuery;
        }
        
        return TRUE;
    }

    /**
     * Set data.
     *
     * @param array $formData            
     *
     * @return bool
     */
    public function SetData($formData = array())
    {
        
        /**
         * Iterate form data.
         */
        foreach ($formData as $field => $data) {
            
            if (! empty($data['i18n'])) {
                
                $app_langs = Core::I18n()->GetAvailableLangs();
                
                foreach ($app_langs as $lang => $locale) {
                    
                    $this->_SetItemData("{$field}_{$lang}", $data, $locale);
                }
            } else {
                
                $this->_SetItemData($field, $data);
            }
        }
        
        return TRUE;
    }

    /**
     * Set item data.
     *
     * @param string $field            
     * @param array $data            
     *
     * @return bool
     */
    private function _SetItemData($field, $data, $locale = NULL)
    {
        
        /**
         * Set main data.
         *
         * @var array
         */
        $this->_mData[$field] = (! empty($data['content'])) ? $data['content'] : NULL;
        
        /**
         * Set meta type.
         * Defaults to 'text'
         *
         * @var array
         */
        $this->_mMeta[$field]['type'] = (! empty($data['type'])) ? $data['type'] : 'text';
        
        if (! empty($data['title'])) {
            
            $this->_mMeta[$field]['title'] = ! empty($locale) ? "{$data['title']} ({$locale})" : $data['title'];
        } else {
            
            $this->_mMeta[$field]['title'] = NULL;
        }
        
        /**
         * Set meta description.
         *
         * @var array
         */
        $this->_mMeta[$field]['description'] = (! empty($data['description'])) ? $data['description'] : NULL;
        
        /**
         * Set meta required.
         *
         * @var array
         */
        $this->_mMeta[$field]['required'] = (! empty($data['required'])) ? (bool) $data['required'] : FALSE;
        
        /**
         * Internationalization.
         */
        $this->_mMeta[$field]['i18n'] = (! empty($locale)) ? TRUE : FALSE;
        
        /**
         * Set meta ignore.
         *
         * @var array
         */
        $this->_mMeta[$field]['ignore'] = (! empty($data['ignore'])) ? (bool) $data['ignore'] : FALSE;
        
        /**
         * Set meta custom.
         *
         * @var array
         */
        $this->_mMeta[$field]['custom'] = (! empty($data['custom'])) ? $data['custom'] : FALSE;
        
        /**
         * Set options for select fields.
         */
        $this->_mMeta[$field]['options'] = array();
        
        if (! empty($data['options']['option']) && is_array($data['options']['option'])) {
            
            if ('value' === key($data['options']['option'])) {
                
                $data['options']['option'] = array(
                    0 => $data['options']['option']
                );
            }
            
            foreach ($data['options']['option'] as $option) {
                
                $this->_mMeta[$field]['options'][$option['value']] = $option['name'];
            }
        }
        
        /**
         * Check if filters are present.
         */
        if (! empty($data['filters'])) {
            /**
             * Set filters.
             *
             * @var array
             */
            $this->_mFilters[$field] = $data['filters'];
        }
        
        /**
         * Check if conditions are present.
         */
        if (! empty($data['conditions'])) {
            /**
             * Set conditions
             *
             * @var array
             */
            $this->_mConditions[$field] = $data['conditions'];
        }
        
        /**
         * Sety ClickBlur data.
         */
        if (! empty($data['clickblur']) && ! empty($data['content'])) {
            
            $this->_mClickBlur[$field] = $data['content'];
        }
        
        return TRUE;
    }

    /**
     * Set custom meta values.
     *
     * @param string $field            
     * @param string $type            
     * @param mixed $data            
     *
     * @return boolean
     */
    public function SetMeta($field, $type, $data)
    {
        $this->_mMeta[$field][$type] = $data;
        
        return TRUE;
    }

    /**
     * Get form metadata.
     *
     * @return array
     */
    public function GetMeta()
    {
        return $this->_mMeta;
    }

    /**
     * Get form click/blur data.
     *
     * @return array
     */
    public function GetClickBlur()
    {
        return $this->_mClickBlur;
    }

    /**
     * Get form data.
     *
     * @return array
     */
    public function GetData()
    {
        return $this->_mData;
    }

    /**
     * Get filtered form data.
     *
     * @return array
     */
    public function GetFilteredData()
    {
        return $this->_mDataFiltered;
    }

    /**
     * Get filtered from data, escaped for using in database inserts.
     *
     * @return array
     */
    public function GetEscapedFilteredData()
    {
        $escaped_filtered_data = array();
        
        foreach ($this->_mDataFiltered as $field => $data) {
            
            if (is_array($data)) {
                
                foreach ($data as $key => $value) {
                    
                    $escaped_filtered_data[$field][$key] = Core::Database()->EscapeString($value);
                }
            } else {
                
                if (! empty($this->_mClickBlur[$field]) && $this->_mClickBlur[$field] == $data) {
                    
                    $escaped_filtered_data[$field] = NULL;
                } else {
                    
                    $escaped_filtered_data[$field] = Core::Database()->EscapeString($data);
                }
            }
        }
        
        return $escaped_filtered_data;
    }

    /**
     * Get form errors.
     *
     * @return array
     */
    public function GetErrors()
    {
        return $this->_mErrors;
    }

    public function Validate()
    {
        
        /**
         * Set request data.
         */
        $this->_SetRequestData();
        
        /**
         * Filter form data.
         */
        $this->_Filter();
        
        /**
         * Iterate filtered form data.
         */
        foreach ($this->_mDataFiltered as $field => $data) {
            
            if (! empty($this->_mClickBlur[$field]) && $this->_mClickBlur[$field] == $data) {
                
                $data = NULL;
            }
            
            /**
             * Check if field is required.
             */
            if (! empty($this->_mMeta[$field]['required']) && FALSE !== $this->_mMeta[$field]['required']) {
                /**
                 * Check for empty data.
                 */
                if (empty($data)) {
                    /**
                     *
                     * @var array
                     */
                    $this->_mErrors[$field][] = sprintf(Core::Config()->Get('form.error.required.text'), $this->_mMeta[$field]['title']);
                }
            }
            
            /**
             * Check if we have conditions for current field.
             */
            if (! empty($this->_mConditions[$field]) && ! empty($data)) {
                /**
                 * Get conditions.
                 *
                 * @var array
                 */
                $conditions = array_diff(explode('|', $this->_mConditions[$field]), array(
                    ''
                ));
                
                /**
                 * Iterate conditions.
                 */
                foreach ($conditions as $condition) {
                    /**
                     *
                     * @var string
                     */
                    $condition_function = $condition;
                    
                    /**
                     *
                     * @var string
                     */
                    $condition_parameter = NULL;
                    
                    /**
                     * Check if condition contains a parameter.
                     */
                    if (preg_match("/(.*?)\[(.*?)\]/", $condition, $match)) {
                        /**
                         *
                         * @var string
                         */
                        $condition_function = $match[1];
                        
                        /**
                         *
                         * @var string
                         */
                        $condition_parameter = $match[2];
                    }
                    
                    /**
                     * Check if condition method exists in string library.
                     */
                    if (method_exists(Core::String(), $condition_function)) {
                        /**
                         * Perform actual validation.
                         *
                         * @var bool
                         */
                        $validation = Core::String()->$condition_function($data, $condition_parameter);
                        
                        if (FALSE === $validation) {
                            /**
                             *
                             * @var string
                             */
                            $condition_index = strtolower($condition_function);
                            
                            /**
                             *
                             * @var string
                             */
                            $error_message = Core::Config()->Get("form.error.{$condition_index}.text");
                            
                            if (! empty($error_message)) {
                                /**
                                 *
                                 * @var array
                                 */
                                $this->_mErrors[$field][] = sprintf($error_message, $this->_mMeta[$field]['title'], $condition_parameter);
                                
                                /**
                                 *
                                 * @var string
                                 */
                                $error_message_description = Core::Config()->Get("form.error.{$condition_index}.description");
                                
                                if (! empty($error_message_description)) {
                                    
                                    /**
                                     *
                                     * @var array
                                     */
                                    $this->_mErrors[$field][] = sprintf($error_message_description, $condition_parameter);
                                }
                            } else {
                                /**
                                 *
                                 * @var array
                                 */
                                $this->_mErrors[$field][] = sprintf(Core::Config()->Get("form.error.default.text"), $this->_mMeta[$field]['title']);
                            }
                        }
                    } else {
                        /**
                         *
                         * @var string
                         */
                        $err_msg = sprintf(__('Form Library: Validation method "%s" not found.', 'WEb'), $condition_function);
                        
                        throw new ErrorException($err_msg);
                    }
                }
            }
        }
        
        /**
         * Return result depending on whether or not we have errors.
         */
        return empty($this->_mErrors) ? TRUE : FALSE;
    }

    /**
     * Set request data.
     *
     * @return bool
     */
    private function _SetRequestData()
    {
        
        /**
         *
         * @var array
         */
        $request = ('post' == $this->_mMethod) ? $_POST : $this->_mRequestQuery;
        
        /**
         * Iterate form data.
         */
        foreach ($this->_mData as $field => $data) {
            /**
             * Check if we have request.
             */
            if (isset($request[$field])) {
                
                if ('checkbox' == $this->_mMeta[$field]['type']) {
                    
                    if (is_array($request[$field])) {
                        
                        foreach ($request[$field] as $key => $value) {
                            
                            $this->_mData[$field][$value] = TRUE;
                        }
                    } else {
                        
                        $this->_mData[$field] = ! empty($request[$field]) ? TRUE : FALSE;
                    }
                } else {
                    
                    $this->_mData[$field] = $request[$field];
                }
            } else {
                
                if ('checkbox' == $this->_mMeta[$field]['type']) {
                    
                    $this->_mData[$field] = FALSE;
                }
            }
        }
        
        return TRUE;
    }

    /**
     * Filter form data.
     *
     * @return bool
     */
    private function _Filter()
    {
        
        /**
         * Iterate form data
         */
        foreach ($this->_mData as $field => $data) {
            if (! empty($this->_mFilters[$field])) {
                /**
                 * Get filters.
                 *
                 * @var array
                 */
                $filters = array_diff(explode('|', $this->_mFilters[$field]), array(
                    ''
                ));
                
                /**
                 * Iterate filters
                 */
                foreach ($filters as $filter) {
                    /**
                     * Check if filter is a built in function.
                     */
                    if (function_exists($filter)) {
                        /**
                         * Filter data using a built in function.
                         *
                         * @var array
                         */
                        $this->_mDataFiltered[$field] = ${$filter}($data);
                    } /**
                     * Check if filter is a string method
                     */
                    else 
                        if (method_exists(Core::String(), $filter)) {
                            /**
                             * Filter data using custom string method.
                             *
                             * @var array
                             */
                            $this->_mDataFiltered[$field] = call_user_func(array(
                                Core::String(),
                                $filter
                            ), $data);
                        } else {
                            /**
                             *
                             * @var string
                             */
                            $err_msg = sprintf(__('Form Library: Filter method "%s" not found.', 'WEb'), $filter);
                            
                            throw new ErrorException($err_msg);
                        }
                }
            } else {
                
                /**
                 * If field is set to ignore we don't add it to the filtered array.
                 */
                if ($this->_mMeta[$field]['ignore']) {} else {
                    /**
                     * No filters are present, set the data as is.
                     *
                     * @var array
                     */
                    $this->_mDataFiltered[$field] = $data;
                }
            }
        }
        
        return TRUE;
    }
}
?>