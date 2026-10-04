<?php
/**
 * $Id: Config.lib.php 231 2017-03-29 14:00:56Z radu $
 *
 * Engine configuration library.
 *
 * @author     Radu Maier <radu@webserv.co>
 * @copyright  Copyright (c) 2008 - 2014, Radu Maier
 * @license    http://www.opensource.org/licenses/bsd-license.php
 * @package    WEb
 * @subpackage WebConfig
 * @version    $Revision: 231 $
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
 * Config
 *
 * Configuration library.
 *
 * @author Radu Maier <radu@webserv.co>
 * @package WEb
 * @subpackage WebConfig
 *            
 */
class Config extends Library
{

    /**
     * Stores configuration options.
     *
     * @var array
     */
    private $_mConfig = array(
        'dev' => array(),
        'test' => array(),
        'prod' => array()
    );

    /**
     * Configuration environment.
     *
     * Defaults to dev.
     *
     * @var string
     */
    private $_mConfigEnv = 'dev';

    /**
     * Configuration string delimiter.
     *
     * Defaults to a dot.
     *
     * @var string
     */
    private $_mConfigDelimiter = '.';

    /**
     * I18n keywords.
     *
     * @var array
     */
    private $_mI18nKeys = array();

    /**
     * I18n keywords specific to application.
     *
     * @var array
     */
    private $_mI18nKeysApp = array();

    /**
     * Implement GetInstance() method.
     *
     * This is required for PHP < 5.3 compatibility (no late static binding)
     *
     * @param string $className
     *            class name to instantiate
     *            
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
     * Load main configuration settings.
     */
    public function Init()
    {
        
        /**
         *
         * @var string
         */
        $path_core = $this->Get('path.core');
        
        /**
         *
         * @var string
         */
        $path_app = $this->Get('path.app');
        
        /**
         * Set core directories.
         */
        $this->Set($path_core . 'etc/dir.cfg.xml');
        
        /**
         * Set application directories.
         */
        $this->Set($path_app . 'etc/dir.cfg.xml');
        
        return TRUE;
    }

    /**
     * Set I18n keys.
     *
     * @return bool
     */
    public function SetI18nKeys()
    {
        
        /**
         *
         * @var string
         */
        $keys = $this->Get('i18n.keys');
        
        if (FALSE !== $keys) {
            /**
             *
             * @var array
             */
            $this->_mI18nKeys = explode(',', $keys);
        }
        
        /**
         *
         * @var string
         */
        $keys_app = $this->Get('i18n.keys_app');
        
        if (FALSE !== $keys_app) {
            /**
             *
             * @var array
             */
            $this->_mI18nKeysApp = explode(',', $keys_app);
        }
        
        return TRUE;
    }

    /**
     * Set configuration environment.
     *
     * @param string $cfgEnv
     *            new environment
     *            
     * @return boolean
     */
    public function SetEnv($cfgEnv = NULL)
    {
        
        /**
         * Check for valid environment.
         */
        if (! array_key_exists($cfgEnv, $this->_mConfig)) {
            /**
             * Stop.
             */
            return FALSE;
        }
        
        /**
         * Set new configuration environment.
         *
         * @var string
         */
        $this->_mConfigEnv = $cfgEnv;
        
        /**
         * Return result.
         */
        return TRUE;
    }

    /**
     * Get configuration environment
     *
     * @return string
     */
    public function GetEnv()
    {
        
        /**
         * Return the current configuration environment.
         */
        return $this->_mConfigEnv;
    }

    /**
     * Store configuration setting(s)
     *
     * @tutorial WEb/ImConfig/Config.cls#SetMethod
     *          
     * @param mixed $cfgData
     *            configuration data to store
     * @param mixed $cfgSetting
     *            configuration setting used to store the value
     * @param mixed $cfgEnv
     *            configuration environment
     *            
     * @return mixed
     */
    public function Set($cfgData = NULL, $cfgSetting = NULL, $cfgEnv = NULL)
    {
        
        /**
         * Check if data is not set, or is an empty array (no such file).
         */
        if (! $cfgData || empty($cfgData)) {
            /**
             * No point to continue.
             */
            return FALSE;
        }
        
        /**
         * If we have an environment request we use it.
         * Otherwise we use the current environment.
         *
         * @var string
         */
        $cfg_env = ($cfgEnv) ? $cfgEnv : $this->_mConfigEnv;
        
        /**
         * Check if the setting parameter is set.
         */
        if ($cfgSetting) {
            /**
             * Simply set the setting to the value.
             */
            $this->_mConfig[$cfg_env][$cfgSetting] = $cfgData;
            
            /**
             * Return result.
             */
            return TRUE;
        } /**
         * No setting parameter is set,
         * so we must process the data.
         */
        else {
            /**
             * Init processed config data.
             *
             * @var array
             */
            $cfg_data = array();
            
            /**
             * Assume data is an array with settings and values.
             */
            if (is_array($cfgData)) {
                /**
                 * Add configuration data to configuration array.
                 */
                return $this->Add($cfgData);
            } /**
             * Otherwise data must be an object or a file path (string.)
             */
            else {
                /**
                 * Process cofiguration data.
                 *
                 * @var array
                 */
                $cfg_data = $this->Process($cfgData);
            }
            
            /**
             * Call the method again using the data array.
             */
            return $this->Set($cfg_data);
        }
        
        /**
         * If we got here we have no valid data to store.
         */
        return FALSE;
    }

    /**
     * Process configuration data.
     *
     * Returns an array from an xml object or path to an xml file.
     *
     * @param mixed $cfgData            
     *
     * @return array
     */
    public function Process($cfgData)
    {
        
        /**
         * Init processed config data.
         *
         * @var array
         */
        $cfg_data = array();
        
        /**
         * Assume data is a valid configuration object.
         */
        if (is_object($cfgData)) {
            /**
             * Parse the configuration object into the data array.
             *
             * @var array
             */
            $cfg_data = $this->_ParseConfigObject($cfgData);
        } /**
         * Assume data is a string containing the path to a configuration file.
         */
        else if (is_string($cfgData)) {
            /**
             * Parse the configuration file into the data array.
             *
             * @var array
             */
            $cfg_data = $this->_ParseConfigFile($cfgData);
        }
        
        return $cfg_data;
    }

    /**
     * Add data to the configuration array.
     *
     * Note: main function for storing configuration data is Set()
     *
     * @see Config::Set()
     *
     * @param array $cfgData
     *            configuration data
     *            
     * @return mixed
     */
    public function Add($cfgData)
    {
        
        /**
         * Add configuration data to the main configuration array.
         *
         * @var array
         */
        $this->_mConfig = $this->_AddConfigData($this->_mConfig, $cfgData);
        
        /**
         * Return result.
         */
        return TRUE;
    }

    /**
     * Retrieve configuration setting(s).
     *
     * @tutorial WEb/ImConfig/Config.cls#GetMethod
     *          
     * @param mixed $cfgSetting
     *            configuration setting to retrieve
     * @param mixed $cfgEnv
     *            configuration environment
     * @param mixed $cfgArray
     *            configuration array to retrieve setting from
     *            
     * @return mixed
     */
    public function Get($cfgSetting = NULL, $cfgEnv = NULL, $cfgArray = NULL)
    {
        
        /*
         * Check for a matching alias for url.www
         * The order of the aliases is important (first match is returned)
         */
        if (empty($cfgArray) && 'url.www' == $cfgSetting) {
            
            if (! empty($_SERVER['HTTP_HOST'])) {
                
                $alias_items = $this->Get('url.aliases');
                
                if (is_array($alias_items) && array_key_exists('item', $alias_items)) {
                    
                    $aliases = is_array($alias_items['item']) ? $alias_items['item'] : [
                        $alias_items['item']
                    ];
                    
                    $protocol = Core::Router()->GetProtocol();
                    
                    foreach ($aliases as $alias) {
                        
                        if (Core::String()->StartsWith("{$protocol}://{$_SERVER['HTTP_HOST']}", $alias)) {
                            return $alias;
                        }
                    }
                }
            }
        }
        
        /**
         * Set configuration environment.
         *
         * If it is received as parameter, use it.
         * Otherwise use the current environment.
         *
         * @var string
         */
        $cfg_env = ($cfgEnv) ? $cfgEnv : $this->_mConfigEnv;
        
        /**
         * Configuration array holds the data we want to retrieve.
         *
         * If we have it as parameter (used in recursion), use it.
         * Otherwise use the main configuration array set to current environment.
         *
         * @var mixed
         */
        $cfg_array = ($cfgArray) ? $cfgArray : $this->_mConfig[$cfg_env];
        
        /**
         * Check if request setting is an empty array.
         * Additional $cfgArray check is required in order
         * not to break recursivity.
         */
        if (! $cfgArray && is_array($cfgSetting) && empty($cfgSetting)) {
            /**
             * Stop.
             */
            return FALSE;
        }
        
        /**
         * Check if request setting is missing or configuration array is
         * actually a string.
         * In both cases the result we are interested in
         * is the actual configuration array variable.
         */
        if (! $cfgSetting || ! is_array($cfg_array)) {
            /**
             * If the configuration array is not really an array, it means the
             * recursion has gone too deep and the configuration array variable
             * actually contains the requested value.
             * Also, if there is no request setting set, the full configuration
             * for the current environment is retrieved.
             *
             * Return value only if not empty.
             */
            
            return isset($cfg_array) ? $cfg_array : FALSE;
        }
        
        /**
         * If requested setting is an array
         * (resulted by processing a special string)
         * we process it recursively until we have a simple string.
         */
        if (is_array($cfgSetting)) {
            /**
             * Check if we have the first request element
             * in the configuration array.
             */
            if (array_key_exists(0, $cfgSetting) && array_key_exists($cfgSetting[0], $cfg_array)) {
                /**
                 * Remove the first element form the request.
                 *
                 * @var mixed
                 */
                $key = array_shift($cfgSetting);
                
                /**
                 * Go down one element in the configuration array,
                 * and call the method again, with the remaining request.
                 */
                return $this->Get($cfgSetting, $cfg_env, $cfg_array[$key]);
            }
            
            /**
             * The requested setting doesn't exist in our configuration array.
             */
            return FALSE;
        }
        
        /**
         * Requested setting must be a string.
         * Check if it is a special string.
         */
        if (FALSE !== strpos($cfgSetting, $this->_mConfigDelimiter)) {
            /**
             * Make array from special string.
             *
             * @var array
             */
            $cfg_setting = explode($this->_mConfigDelimiter, $cfgSetting);
            
            /**
             * Call the method again, this time using an array.
             */
            return $this->Get($cfg_setting, $cfg_env, $cfg_array);
        }
        
        /**
         * Requested setting must be a simple string.
         * Check if setting exists in array and return the result.
         *
         * This is the main functionality of the method.
         * All processing and recursivity must end up here.
         */
        if (array_key_exists($cfgSetting, $cfg_array)) {
            /**
             * Return requested value but only if it is not empty.
             */
            return isset($cfg_array[$cfgSetting]) ? $cfg_array[$cfgSetting] : FALSE;
        }
        
        /**
         * If we got this far, we have no valid input.
         */
        return FALSE;
    }

    /**
     * Parse configuration file.
     *
     * @param string $cfgPath
     *            full path to configuration file
     *            
     * @return array
     */
    private function _ParseConfigFile($cfgPath)
    {
        
        /**
         * Init configuration array.
         *
         * @var array
         */
        $cfg = array();
        
        /**
         * Check if we have access to the file.
         */
        if (is_readable($cfgPath)) {
            
            /**
             * Disable libxml errors and allow user to fetch error information.
             */
            libxml_use_internal_errors(TRUE);
            
            /**
             * Load data in file into an object.
             *
             * @var object
             */
            $cfg_object = simplexml_load_file($cfgPath, 'SimpleXMLElement', LIBXML_NOCDATA);
            
            /**
             * Check if we have a valid XML object.
             */
            if (FALSE !== $cfg_object) {
                /**
                 * Loop through the object.
                 */
                foreach ($cfg_object as $cfg_setting => $cfg_data) {
                    /**
                     * Set the configuration settings.
                     *
                     * @var array
                     */
                    $cfg[$cfg_setting] = $this->_ParseConfigObject($cfg_data);
                }
            }
        }
        
        /**
         * Return the configuration array.
         */
        return $cfg;
    }

    /**
     * Parse configuration object.
     *
     * @param mixed $cfgData
     *            configuration data
     */
    private function _ParseConfigObject($cfgData)
    {
        
        /**
         * Check if input is an object.
         */
        if (is_object($cfgData)) {
            /**
             * Remove comments.
             */
            unset($cfgData->comment);
            
            /**
             * Transform object into an array.
             *
             * @var array
             */
            $cfgData = get_object_vars($cfgData);
        }
        
        /**
         * Check is input is an array.
         */
        if (is_array($cfgData)) {
            /**
             * Check for i18n support.
             */
            if (function_exists('__')) {
                /**
                 * Iterate through i18n keys.
                 */
                foreach ($this->_mI18nKeys as $i18n_key) {
                    /**
                     * Check if configuration data contains i18n keys.
                     */
                    if (array_key_exists($i18n_key, $cfgData) && is_string($cfgData[$i18n_key])) {
                        /**
                         * Translate configuration data.
                         *
                         * @var array
                         */
                        $cfgData[$i18n_key] = __($cfgData[$i18n_key]);
                    }
                }
                
                /**
                 * Iterate through i18n application keys.
                 */
                foreach ($this->_mI18nKeysApp as $i18n_key_app) {
                    /**
                     * Check if configuration data contains i18n application keys.
                     */
                    if (array_key_exists($i18n_key_app, $cfgData) && is_string($cfgData[$i18n_key_app])) {
                        /**
                         * Translate configuration data using the application text domain.
                         *
                         * @var array
                         */
                        $cfgData[$i18n_key_app] = __($cfgData[$i18n_key_app], 'App');
                    }
                }
            }
            
            /**
             * Iterate through it as it may contain further objects.
             */
            return array_map(array(
                $this,
                __FUNCTION__
            ), $cfgData);
        }
        
        /**
         * Trim values in the configuration array.
         *
         * @var array
         */
        $cfgData = $this->_TrimConfigData($cfgData);
        
        /**
         * Return parsed object.
         */
        return $cfgData;
    }

    /**
     * Trim values in the configuration array.
     *
     * @param mixed $cfgData
     *            configuration data
     */
    private function _TrimConfigData($cfgData)
    {
        
        /**
         * If data is not an array, trim it.
         * Otherwise call the method recursively.
         */
        return (is_array($cfgData)) ? array_map(array(
            $this,
            __FUNCTION__
        ), $cfgData) : trim($cfgData);
    }

    /**
     * Add data to the configuration array.
     *
     * Made a separate method from Add()
     * in order to benefit from recursivity.
     *
     * @see Config::Add()
     *
     * @param array $cfgArray
     *            main configuration array
     * @param array $cfgData
     *            configuration data to add
     */
    private function _AddConfigData($cfgArray, $cfgData)
    {
        
        /**
         * Only proceed if both parameters are arrays.
         */
        if (is_array($cfgArray) && is_array($cfgData)) {
            /**
             * Loop through the configuration data.
             */
            foreach ($cfgData as $cfg_setting => $cfg_value) {
                /**
                 * Check that the configuration setting is an array in the
                 * main configuration array, and that the configuration value
                 * is also an array.
                 */
                if (array_key_exists($cfg_setting, $cfgArray) && is_array($cfgArray[$cfg_setting]) && is_array($cfg_value)) {
                    /**
                     * Call the method recursively to set the new value
                     * of the main configuration array.
                     *
                     * @var array
                     */
                    $cfgArray[$cfg_setting] = $this->_AddConfigData($cfgArray[$cfg_setting], $cfg_value);
                } /**
                 * Configuration value is a simple string,
                 */
                else {
                    /**
                     * Set new configuration value.
                     *
                     * @var array
                     */
                    $cfgArray[$cfg_setting] = $cfg_value;
                }
            }
        }
        
        /**
         * At this point all processing is complete,
         * so we just return the main configuration array.
         */
        return $cfgArray;
    }
}
?>