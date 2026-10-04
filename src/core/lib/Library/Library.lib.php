<?php
/**
 * $Id: Library.lib.php 224 2016-10-24 14:10:37Z radu $
 *
 * Engine base library.
 *
 * @author     Radu Maier <radu@webserv.co>
 * @copyright  Copyright (c) 2008 - 2014, Radu Maier
 * @license    http://www.opensource.org/licenses/bsd-license.php
 * @package    WEb
 * @subpackage WebLibrary
 * @version    $Revision: 224 $
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
 * Library
 *
 * Base library.
 *
 * @author Radu Maier <radu@webserv.co>
 * @package WEb
 * @subpackage WebLibrary
 *            
 */
abstract class Library
{

    /**
     * Stores object instances.
     *
     * @var array
     */
    protected static $_msInstance = array();

    /**
     * App prefix.
     *
     * @var string
     */
    protected $_mAppPrefix = NULL;

    /**
     * Stores the Output object.
     *
     * @var mixed
     */
    protected $_mOutputObject;

    /**
     * Get object instance.
     *
     * @param string $className
     *            name of the class to instantiate
     *            
     * @return object
     */
    protected static function _GetInstance($className)
    {
        
        /**
         * Check if requested instance is the base library.
         */
        if (__CLASS__ == $className) {
            /**
             * Stop.
             */
            return FALSE;
        }
        
        /**
         * Check that instance is not already set.
         */
        if (! isset(self::$_msInstance[$className])) {
            /**
             * Set object instance.
             */
            self::$_msInstance[$className] = new $className();
            
            if (function_exists('__')) {
                /**
                 * Check if loaded classis an object, not a library.
                 */
                if (property_exists(self::$_msInstance[$className], '_mId')) {
                    /**
                     *
                     * @var string
                     */
                    $inst_msg = sprintf(__('%s object: Loading.', 'WEb'), $className);
                } else {
                    /**
                     *
                     * @var string
                     */
                    $inst_msg = sprintf(__('%s library: Loading.', 'WEb'), $className);
                }
                
                Core::Log()->Write($inst_msg, 'debug');
            }
            
            /**
             * Load object configuration.
             */
            self::$_msInstance[$className]->_LoadConfig($className);
        }
        
        /**
         * Return object instance.
         */
        return self::$_msInstance[$className];
    }

    /**
     * Load configuration file for current library.
     *
     * @param string $className
     *            name of the class to load configuration for
     *            
     * @return bool
     */
    protected function _LoadConfig($className)
    {
        
        /**
         * App prefix.
         */
        $this->_mAppPrefix = Core::Config()->Get('app.prefix');
        
        /**
         *
         * @var string
         */
        $config_name = strtolower(str_replace('_', '.', $className)) . '.cfg';
        
        /**
         *
         * @var string
         */
        $path_app = Core::Config()->Get('path.app');
        
        /**
         *
         * @var string
         */
        $config_file = $path_app . 'etc/lib/' . $config_name . '.xml';
        
        if (! is_readable($config_file)) {
            if (function_exists('__')) {
                /**
                 *
                 * @var string
                 */
                $config_msg = sprintf(__('%s library: Skipping configuration file "%s" (not found).', 'WEb'), $className, $config_name);
                
                Core::Log()->Write($config_msg, 'debug');
            }
            
            return FALSE;
        }
        
        if (function_exists('__')) {
            /**
             *
             * @var string
             */
            $config_msg = sprintf(__('%s library: Loading configuration file "%s".', 'WEb'), $className, $config_name);
            
            Core::Log()->Write($config_msg, 'debug');
        }
        
        return Core::Config()->Set($config_file);
    }

    /**
     * Method to access the Output object.
     *
     * @return object
     */
    final public function Output()
    {
        if (NULL == $this->_mOutputObject) {
            /**
             *
             * @var string
             */
            $engine = Core::Config()->Get('output.engine');
            
            /**
             *
             * @var string
             */
            $engine = (FALSE !== $engine) ? $engine : 'WEb';
            
            /**
             *
             * @var string
             */
            $engine_class = 'Output_' . ucfirst(strtolower($engine));
            
            /**
             * PHP < 5.3 compatibility.
             * $this->_mOutputObject = $engine_class::GetInstance($engine_class);
             *
             * @var object
             */
            $this->_mOutputObject = call_user_func(array(
                $engine_class,
                'GetInstance'
            ), $engine_class);
            
            $out_msg = sprintf(__('Controller library: Output library "%s" loading.', 'WEb'), $engine);
            
            Core::Log()->Write($out_msg, 'debug');
        }
        
        return $this->_mOutputObject;
    }

    /**
     * Avoid normal class loading.
     */
    final protected function __construct()
    {}

    /**
     * Avoid class cloning.
     */
    final protected function __clone()
    {}
}
?>