<?php
/**
 * $Id: Core.lib.php 211 2016-01-21 13:39:25Z radu $
 *
 * Engine core library.
 *
 * @author     Radu Maier <radu@webserv.co>
 * @copyright  Copyright (c) 2008 - 2014, Radu Maier
 * @license    http://www.opensource.org/licenses/bsd-license.php
 * @package    WEb
 * @subpackage WebCore
 * @version    $Revision: 211 $
 * @since      Version 5
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
 * Core
 * Core library.
 *
 * @author Radu Maier <radu@webserv.co>
 * @package WEb
 * @subpackage WebCore
 */
class Core
{

    /**
     * Stores the Acl object.
     *
     * @var mixed
     */
    protected static $_msAclObject;

    /**
     * Stores the application object.
     *
     * @var mixed
     */
    protected static $_msAppObject;
    
    /**
     * Stores the arrays object.
     *
     * @var mixed
     */
    protected static $_msArraysObject;

    /**
     * Stores the Browser object.
     *
     * @var mixed
     */
    protected static $_msBrowserObject;

    /**
     * Stores the Captcha object.
     *
     * @var mixed
     */
    protected static $_msCaptchaObject;

    /**
     * Stores configuration object.
     *
     * @var mixed
     */
    protected static $_msConfigObject;

    /**
     * Stores the database object.
     *
     * @var mixed
     */
    protected static $_msDatabaseObject;

    /**
     * Stores the date object.
     *
     * @var mixed
     */
    protected static $_msDateObject;

    /**
     * Stores the error handling object.
     *
     * @var mixed
     */
    protected static $_msErrorObject;

    /**
     * Stores the Form object.
     *
     * @var mixed
     */
    protected static $_msFormObject;

    /**
     * Stores the Geo object.
     *
     * @var mixed
     */
    protected static $_msGeoObject;

    /**
     * Stores the Graphics object.
     *
     * @var mixed
     */
    protected static $_msGraphicsObject;

    /**
     * Stores the internationalization object.
     *
     * @var mixed
     */
    protected static $_msI18nObject;

    /**
     * Stores the logging object.
     *
     * @var mixed
     */
    protected static $_msLogObject;

    /**
     * Stores the Mail object.
     *
     * @var mixed
     */
    protected static $_msMailObject;

    /**
     * Stores the Router object.
     *
     * @var mixed
     */
    protected static $_msRouterObject;

    /**
     * Stores the Security object.
     *
     * @var mixed
     */
    protected static $_msSecurityObject;

    /**
     * Stores the Session object.
     *
     * @var mixed
     */
    protected static $_msSessionObject;

    /**
     * Stores the SMTP object.
     *
     * @var mixed
     */
    protected static $_msSmtpObject;

    /**
     * Stores the String object.
     *
     * @var mixed
     */
    protected static $_msStringObject;

    /**
     * Stores the Useragent object.
     *
     * @var mixed
     */
    protected static $_msUseragentObject;

    /**
     * Stores the FacebookApi object.
     *
     * @var mixed
     */
    protected static $_msFacebookApiObject;

    /**
     * Stores the User object.
     *
     * @var unknown_type
     */
    protected static $_msUserObject;

    /**
     * License already checked.
     *
     * @var mixed
     */
    private static $_msLicenseChecked = FALSE;

    /**
     * Method to access the Acl object from everywhere.
     *
     * @return object
     */
    final public static function Acl()
    {
        if (NULL == self::$_msAclObject) {
            /**
             *
             * @var object
             */
            self::$_msAclObject = Acl::GetInstance();
        }
        
        return self::$_msAclObject;
    }

    /**
     * Method to access the application object from everywhere.
     *
     * @return object
     */
    final public static function App()
    {
        if (NULL == self::$_msAppObject) {
            /**
             *
             * @var object
             */
            self::$_msAppObject = App::GetInstance();
        }
        
        return self::$_msAppObject;
    }

    /**
     * Method to access the Arrays object from everywhere.
     *
     * @return object
     */
    final public static function Arrays()
    {
        if (NULL == self::$_msArraysObject) {
            /**
             *
             * @var object
             */
            self::$_msArraysObject = Arrays::GetInstance();
        }
        
        return self::$_msArraysObject;
    }

    /**
     * Method to access the Browser object from everywhere.
     *
     * @return object
     */
    final public static function Browser()
    {
        if (NULL == self::$_msBrowserObject) {
            /**
             *
             * @var object
             */
            self::$_msBrowserObject = Browser::GetInstance();
        }
        
        return self::$_msBrowserObject;
    }

    /**
     * Method to access the Captcha object from everywhere.
     *
     * @return object
     */
    final public static function Captcha()
    {
        if (NULL == self::$_msCaptchaObject) {
            /**
             *
             * @var object
             */
            self::$_msCaptchaObject = Captcha::GetInstance();
        }
        
        return self::$_msCaptchaObject;
    }

    /**
     * Method to acces the configuration object from everywhere.
     *
     * @return object
     */
    final public static function Config()
    {
        if (NULL == self::$_msConfigObject) {
            /**
             *
             * @var object
             */
            self::$_msConfigObject = Config::GetInstance();
        }
        
        return self::$_msConfigObject;
    }

    /**
     * Method to access the database object from everywhere.
     *
     * @return object
     */
    final public static function Database()
    {
        
        /**
         *
         * @var string
         */
        $db_type = self::Config()->Get('database.type');
        
        /**
         * If we have no database type,
         * database connection will not be available.
         */
        if (FALSE === $db_type) {
            return FALSE;
        }
        
        if (NULL == self::$_msDatabaseObject) {
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
                $db_type = $parts[1];
            }
            
            /**
             *
             * @var string
             */
            $db_class = 'Database_' . ucfirst(strtolower($db_type));
            
            /**
             * Load the appropriate database library.
             * PHP < 5.3 compatibility.
             * self::$_msDatabaseObject = $db_class::GetInstance($db_class);
             *
             * @var object
             */
            self::$_msDatabaseObject = call_user_func(array(
                $db_class,
                'GetInstance'
            ), $db_class);
            
            /**
             *
             * @var string
             */
            $out_msg = sprintf(__('Core library: Database library "%s" loading.', 'WEb'), $db_class);
            
            self::Log()->Write($out_msg, 'debug');
        }
        
        return self::$_msDatabaseObject;
    }

    /**
     * Method to access the date object from everywhere.
     *
     * @return object
     */
    final public static function Date()
    {
        if (NULL == self::$_msDateObject) {
            /**
             *
             * @var object
             */
            self::$_msDateObject = Date::GetInstance();
        }
        
        return self::$_msDateObject;
    }

    /**
     * Method to access the error handling object from everywhere.
     *
     * @return object
     */
    final public static function Error()
    {
        if (NULL == self::$_msErrorObject) {
            /**
             *
             * @var object
             */
            self::$_msErrorObject = ErrorHandler::GetInstance();
        }
        
        return self::$_msErrorObject;
    }

    /**
     * Method to access the Form object from everywhere.
     *
     * @return object
     */
    final public static function Form()
    {
        if (NULL == self::$_msFormObject) {
            /**
             *
             * @var object
             */
            self::$_msFormObject = Form::GetInstance();
        }
        
        return self::$_msFormObject;
    }

    /**
     * Method to access the Geo object from everywhere.
     *
     * @return object
     */
    final public static function Geo()
    {
        if (NULL == self::$_msGeoObject) {
            /**
             *
             * @var object
             */
            self::$_msGeoObject = Geo::GetInstance();
        }
        
        return self::$_msGeoObject;
    }

    /**
     * Method to access the Graphics object from everywhere.
     *
     * @return object
     */
    final public static function Graphics()
    {
        if (NULL == self::$_msGraphicsObject) {
            /**
             *
             * @var object
             */
            self::$_msGraphicsObject = Graphics::GetInstance();
        }
        
        return self::$_msGraphicsObject;
    }

    /**
     * Method to access the internationalization object from everywhere.
     *
     * @return object
     */
    final public static function I18n()
    {
        if (NULL == self::$_msI18nObject) {
            /**
             *
             * @var object
             */
            self::$_msI18nObject = I18n::GetInstance();
        }
        
        return self::$_msI18nObject;
    }

    /**
     * Method to access the logging object from everywhere.
     *
     * @return object
     */
    final public static function Log()
    {
        if (NULL == self::$_msLogObject) {
            /**
             *
             * @var object
             */
            self::$_msLogObject = Log::GetInstance();
        }
        
        return self::$_msLogObject;
    }

    /**
     * Method to access the Mail object from everywhere.
     *
     * @return object
     */
    final public static function Mail()
    {
        if (NULL == self::$_msMailObject) {
            /**
             *
             * @var object
             */
            self::$_msMailObject = Mail::GetInstance();
        }
        
        return self::$_msMailObject;
    }

    /**
     * Method to access the Router object from everywhere.
     *
     * @return object
     */
    final public static function Router()
    {
        if (NULL == self::$_msRouterObject) {
            /**
             *
             * @var object
             */
            self::$_msRouterObject = Router::GetInstance();
        }
        
        return self::$_msRouterObject;
    }

    /**
     * Method to access the Security object from everywhere.
     *
     * @return object
     */
    final public static function Security()
    {
        if (NULL == self::$_msSecurityObject) {
            /**
             *
             * @var object
             */
            self::$_msSecurityObject = Security::GetInstance();
        }
        
        return self::$_msSecurityObject;
    }

    /**
     * Method to access the Session object from everywhere.
     *
     * @return object
     */
    final public static function Session()
    {
        if (NULL == self::$_msSessionObject) {
            /**
             *
             * @var object
             */
            self::$_msSessionObject = self::Get('Library_Session');
        }
        
        return self::$_msSessionObject;
    }

    /**
     * Method to access the SMTP object from everywhere.
     *
     * @return object
     */
    final public static function Smtp()
    {
        if (NULL == self::$_msSmtpObject) {
            /**
             *
             * @var object
             */
            self::$_msSmtpObject = Smtp::GetInstance();
        }
        
        return self::$_msSmtpObject;
    }

    /**
     * Method to access the String object from everywhere.
     *
     * @return object
     */
    final public static function String()
    {
        if (NULL == self::$_msStringObject) {
            /**
             *
             * @var object
             */
            self::$_msStringObject = StringLibrary::GetInstance();
        }
        
        return self::$_msStringObject;
    }

    /**
     * Method to access the Useragent object from everywhere.
     *
     * @return object
     */
    final public static function Useragent()
    {
        if (NULL == self::$_msUseragentObject) {
            /**
             *
             * @var object
             */
            self::$_msUseragentObject = Useragent::GetInstance();
        }
        
        return self::$_msUseragentObject;
    }

    /**
     * Method to access the FacebookApi object from everywhere.
     *
     * @return object
     */
    final public static function FacebookApi()
    {
        if (NULL == self::$_msFacebookApiObject) {
            /**
             *
             * @var object
             */
            self::$_msFacebookApiObject = FacebookApi::GetInstance();
        }
        
        return self::$_msFacebookApiObject;
    }

    /**
     * Method to access the User object from anywhere.
     *
     * @return object
     */
    final public static function User()
    {
        if (NULL == self::$_msUserObject) {
            /**
             *
             * @var object
             */
            self::$_msUserObject = self::Get('Object_User');
        }
        
        return self::$_msUserObject;
    }

    /**
     * Load classes.
     *
     * @param string $ClassName
     *            full class name
     */
    final public static function Load($className)
    {
        
        /**
         * Base search paths.
         *
         * @var array
         */
        $base = array(
            self::Config()->Get('path.app'),
            self::Config()->Get('path.core')
        );
        
        /**
         * Class path.
         *
         * @var string
         */
        $path = 'lib/';
        
        /**
         * Class parts
         *
         * @var array
         */
        $parts = explode('_', $className);
        
        /**
         * Class name.
         *
         * @var string
         */
        $name = $parts[0];
        
        /**
         * Class directory.
         *
         * @var string
         */
        $dir = $name . '/';
        
        /**
         * Class type.
         *
         * @var string
         */
        $type = 'lib';
        
        /**
         * Check if we have at least 2 parts.
         */
        if (array_key_exists(1, $parts)) {
            /**
             *
             * @var string
             */
            $name = $parts[1];
            
            /**
             * First part must contain class type.
             */
            switch ($parts[0]) {
                case 'Interface':
                    /**
                     *
                     * @var array
                     */
                    $base = array(
                        self::Config()->Get('path.core')
                    );
                    /**
                     *
                     * @var string
                     */
                    $path = self::Config()->Get('dir.core.int') . '/';
                    /**
                     *
                     * @var string
                     */
                    $dir = $name . '/';
                    /**
                     *
                     * @var string
                     */
                    $type = 'int';
                    
                    break;
                
                case 'Database':
                    /**
                     *
                     * @var array
                     */
                    $base = array(
                        self::Config()->Get('path.core')
                    );
                    /**
                     *
                     * @var string
                     */
                    $path = 'lib/';
                    /**
                     *
                     * @var string
                     */
                    $dir = 'Database/';
                    /**
                     *
                     * @var string
                     */
                    $type = 'lib';
                    
                    break;
                
                case 'Module':
                    /**
                     *
                     * @var array
                     */
                    $base = array(
                        self::Config()->Get('path.app')
                    );
                    /**
                     *
                     * @var string
                     */
                    $path = self::Config()->Get('dir.app.mod') . '/';
                    /**
                     *
                     * @var string
                     */
                    $dir = $name . '/';
                    /**
                     *
                     * @var string
                     */
                    $type = 'mod';
                    
                    break;
                
                case 'Object':
                    /**
                     *
                     * @var array
                     */
                    $base = array(
                        self::Config()->Get('path.app') . self::Config()->Get('dir.app.obj'),
                        self::Config()->Get('path.core') . self::Config()->Get('dir.core.obj')
                    );
                    /**
                     *
                     * @var string
                     */
                    $path = '/';
                    /**
                     *
                     * @var string
                     */
                    $dir = $name . '/';
                    /**
                     *
                     * @var string
                     */
                    $type = 'obj';
                    
                    break;
                
                case 'Controller':
                    /**
                     * We must have a third part in the raw name.
                     */
                    if (! array_key_exists(1, $parts)) {
                        break;
                    }
                    /**
                     *
                     * @var string
                     */
                    $name = $parts[2];
                    /**
                     *
                     * @var array
                     */
                    $base = array(
                        self::Config()->Get('path.app')
                    );
                    /**
                     *
                     * @var string
                     */
                    $path = self::Config()->Get('dir.app.ctrl') . '/';
                    /**
                     *
                     * @var string
                     */
                    $dir = $parts[1] . '/';
                    /**
                     *
                     * @var string
                     */
                    $type = 'ctrl';
                    
                    break;
                
                case 'Output':
                    /**
                     *
                     * @var array
                     */
                    $base = array(
                        self::Config()->Get('path.core')
                    );
                    /**
                     *
                     * @var string
                     */
                    $path = 'lib/';
                    /**
                     *
                     * @var string
                     */
                    $dir = 'Output/';
                    /**
                     *
                     * @var string
                     */
                    $type = 'lib';
                    
                    break;
                
                default:
                    
                    $dir = $name . '/';
                    
                    break;
            }
        }
        
        /**
         * Complete class path.
         *
         * @var string
         */
        $class_path = $path . $dir . $name . '.' . $type . '.php';
        
        /**
         * Each class can have an utilities file associated with it.
         * Utilities files should only contain procedural functions.
         *
         * @var string
         */
        $class_utils_path = $path . $dir . $name . 'Utils.' . $type . '.php';
        
        /**
         * Iterate through base search paths.
         */
        foreach ($base as $base_path) {
            /**
             * Check if class file is readable.
             */
            if (is_readable($base_path . $class_path)) {
                require_once $base_path . $class_path;
                
                /**
                 * Check if we have an utilities file.
                 * We only load it if the class file was loaded successfully.
                 */
                if (is_readable($base_path . $class_utils_path)) {
                    require_once $base_path . $class_utils_path;
                }
                
                /**
                 * License check disabled! XXX
                 */
                // if (!empty(self::$_msConfigObject) && !empty(self::$_msErrorObject) &&
                // (TRUE !== self::$_msLicenseChecked))
                // {
                /**
                 * Set license checked.
                 */
                // self::$_msLicenseChecked = TRUE;
                
                /**
                 * Check license.
                 */
                // self::_LicCheck();
                // }
                
                /**
                 * Stop.
                 */
                return TRUE;
            }
        }
        
        /**
         * Class file not found.
         */
        return FALSE;
    }

    /**
     * Wrapper for loading and instantiating a library type object.
     *
     * @param string $libraryName            
     *
     * @return mixed
     */
    final public static function Get($libraryName)
    {
        self::Load($libraryName);
        
        /**
         *
         * @var array
         */
        $parts = explode('_', $libraryName);
        
        $library_name = end($parts);
        
        if (array_key_exists(1, $parts)) {
            
            switch ($parts[0]) {
                
                case 'Module':
                    
                    $library_name = $libraryName;
                    
                    break;
                default:
                    break;
            }
        }
        
        if (! class_exists($library_name)) {
            /**
             *
             * @var string
             */
            $err_msg = sprintf(__('Core Library: Class "%s" not found.', 'WEb'), $library_name);
            
            throw new ErrorException($err_msg);
        }
        
        return call_user_func(array(
            $library_name,
            'GetInstance'
        ), $library_name);
    }

    /**
     * Check if PHP is running in CLI mode.
     *
     * @return boolean
     */
    public static function IsCli()
    {
        return (('cli' == PHP_SAPI) && empty($_SERVER['REMOTE_ADDR']));
    }

    /**
     * Open URL handle.
     *
     * @param string $url            
     * @param array $postData            
     *
     * @return mixed
     */
    private static function _Wget($url, $postData = array())
    {
        
        /**
         * Initialize headers.
         *
         * @var array
         */
        $headers = array();
        
        /**
         *
         * @var unknown_type
         */
        $headers['User-Agent'] = 'Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.2.2) Gecko/20100316 Firefox/3.6.2 GTB6 (.NET CLR 3.5.30729)';
        
        /**
         *
         * @var unknown_type
         */
        $headers['Referer'] = self::Config()->Get('url.www');
        
        /**
         *
         * @var unknown_type
         */
        $headers['Accept-Encoding'] = 'x-compress; x-zip';
        
        /**
         * Parse url.
         */
        preg_match("~([a-z]*://)?([^:^/]*)(:([0-9]{1,5}))?(/.*)?~i", $url, $url_parts);
        
        /**
         *
         * @var string
         */
        $protocol = $url_parts[1];
        
        /**
         *
         * @var string
         */
        $server = $url_parts[2];
        
        /**
         *
         * @var string
         */
        $port = ('' != $url_parts[4]) ? $url_parts[4] : '80';
        
        /**
         *
         * @var string
         */
        $path = ('' != $url_parts[5]) ? $url_parts[5] : '/';
        
        /**
         * Open socket.
         */
        $socket = fsockopen($server, $port);
        
        /**
         * Check if socket was opened successfully.
         */
        if (! $socket) {
            /**
             * Return result.
             */
            return FALSE;
        }
        
        /**
         * Set host header.
         */
        $headers['Host'] = $server;
        
        /**
         * Set request.
         */
        $request = (! empty($postData)) ? "POST $path HTTP/1.0\r\n" : "GET $path HTTP/1.0\r\n";
        
        /**
         * Send request.
         */
        fwrite($socket, $request);
        
        /**
         * Init post string.
         *
         * @var string
         */
        $post_string = NULL;
        
        /**
         * Check if we have post data.
         */
        if (! empty($postData)) {
            /**
             * Init post data.
             *
             * @var array
             */
            $post_data = array();
            
            /**
             * Iterate post data.
             */
            foreach ($postData as $post_key => $post_value) {
                /**
                 *
                 * @var array
                 */
                $post_data[] = "{$post_key}={$post_value}";
            }
            
            /**
             * Set post string.
             *
             * @var string
             */
            $post_string = implode('&', $post_data);
            
            /**
             * Set content type header.
             *
             * @var array
             */
            $headers['Content-Type'] = 'application/x-www-form-urlencoded';
            
            /**
             * Set content length header.
             *
             * @var array
             */
            $headers['Content-Length'] = strlen($post_string);
        }
        
        /**
         * Iterate headers.
         */
        foreach ($headers as $header_key => $header_value) {
            /**
             * Send headers.
             */
            fwrite($socket, "{$header_key}: {$header_value}\r\n");
        }
        
        /**
         * Send newline.
         */
        fwrite($socket, "\r\n");
        
        /**
         * Check if we have post data.
         */
        if (! empty($post_string)) {
            /**
             * Send post request string.
             */
            fwrite($socket, $post_string . "\r\n");
        }
        
        /**
         * Get first line of response.
         */
        $response = fgets($socket, 1000);
        
        /**
         * Get status.
         */
        $status = substr($response, 9, 3);
        
        while (trim($line = fgets($socket, 1000)) != '') {
            if ('401' == $status && (0 === strpos($line, "WWW-Authenticate: Basic realm=\""))) {
                /**
                 * Close socket
                 */
                fclose($socket);
                
                /**
                 * Return result.
                 */
                return FALSE;
            }
        }
        
        /**
         * Return result.
         */
        return $socket;
    }

    /**
     * Set license parameters.
     *
     * @return array
     */
    private static function _LicSetParams()
    {
        
        /**
         * Initialize license info.
         *
         * @var array
         */
        $license = array();
        
        /**
         *
         * @var string
         */
        $license_name = rtrim(str_replace(array(
            'http://',
            'www.'
        ), '', self::Config()->Get('url.www')), '/');
        
        /**
         * Set license CN
         *
         * @var array
         */
        $license['cn'] = strtr($license_name, '/.', '-_');
        
        /**
         * Set licence key.
         *
         * @var array
         */
        $license['key'] = substr(md5($license['cn']), 0, 26);
        
        /**
         * Init license directory.
         *
         * @var string
         */
        $dir = Core::Config()->Get('path.app') . Core::Config()->Get('dir.app.tmp') . '/';
        
        /**
         * Set license directory.
         */
        $license['dir'] = (FALSE !== strpos($dir, ';')) ? rtrim(substr($dir, strpos($dir, ';') + 1), '/') : rtrim($dir, '/');
        
        /**
         * Set license path.
         */
        $license['path'] = "{$license['dir']}/sess_{$license['key']}";
        
        return $license;
    }

    /**
     * Get license data from the license server.
     *
     * @param array $license            
     * @param string $request            
     *
     * @return string
     */
    private static function _LicGet($license, $request)
    {
        
        /**
         *
         * @var string
         */
        $key_path = self::_LicGetKeyPath($license);
        
        /**
         * Get private key.
         */
        $private_key = openssl_pkey_get_private(file_get_contents($key_path));
        
        /**
         * Generate signature.
         */
        openssl_sign($license['cn'], $signature, $private_key, OPENSSL_ALGO_SHA1);
        
        /**
         * Set signature post data.
         */
        $post_data['s'] = rawurlencode(base64_encode($signature));
        
        /**
         * Release private key.
         */
        openssl_free_key($private_key);
        
        /**
         * Set request post data.
         */
        $post_data['r'] = $request;
        
        /**
         * Set CN post data.
         */
        $post_data['cn'] = $license['cn'];
        
        /**
         * Open encrypted license file handle.
         */
        $license_file = self::_Wget('http://clients.webserv.co/', $post_data);
        
        /**
         * Get encrypted license string.
         */
        $license_string = fgets($license_file, 1024);
        
        /**
         * Close encrypted file handle.
         */
        fclose($license_file);
        
        return trim($license_string);
    }

    /**
     * Update license information.
     *
     * @param array $license            
     *
     * @return void
     */
    private static function _LicUpdate($license)
    {
        
        /**
         * Get encrypted license string.
         *
         * @var string
         */
        $license_string = self::_LicGet($license, 'a8ee21598ca7cefa744e25f401aae48130b99afd');
        
        /**
         * Get encrypted license key.
         *
         * @var string
         */
        $license_key = self::_LicGet($license, '170f27a90290081cdedf11e3c87e3abe90592e49');
        
        /**
         *
         * @var string
         */
        $key_path = self::_LicGetKeyPath($license);
        
        /**
         * Get private key.
         */
        $private_key = openssl_pkey_get_private("file://{$key_path}");
        
        /**
         * Init decrypted license.
         */
        $license_object = NULL;
        
        /**
         * Decrypt license info.
         */
        openssl_open(base64_decode($license_string), $license_object, base64_decode($license_key), $private_key) or die(openssl_error_string());
        
        /**
         * Release private key.
         */
        openssl_free_key($private_key);
        
        /**
         * Parse license data.
         */
        $license_data = simplexml_load_string($license_object);
        
        /**
         * Get license status.
         */
        $license_status = ($license_data) ? (string) $license_data->license[0]->status[0] : NULL;
        
        if ('ok' != $license_status) {
            /**
             * Trigger license error.
             */
            return self::_LicTriggerError($license, $license_status);
        }
        
        /**
         * Check if license status file exists.
         */
        if (is_readable($license['path'])) {
            /**
             * Delete license status file.
             */
            unlink($license['path']);
        }
        
        /**
         * Redirect to application root.
         */
        header('Location: ' . self::Config()->Get('url.www'));
        
        exit();
    }

    /**
     * Trigger license error.
     *
     * @param array $license            
     * @param string $status            
     * @param string $errorMessage            
     *
     * @return void
     */
    private static function _LicTriggerError($license, $status, $errorMessage = NULL)
    {
        
        /**
         *
         * @var string
         */
        $error_message = (! empty($errorMessage)) ? $errorMessage : __('License Error: Your license key is invalid or expired.', 'WEb');
        
        /**
         * Create license status file with dummy content.
         */
        file_put_contents($license['path'], "{$license['key']} {$status}");
        
        /**
         * Change file mode.
         */
        @chmod($license['path'], 0600);
        
        self::Log()->Write(sprintf(__('License Status: %s.', 'WEb'), $status), 'info');
        
        throw new ErrorException($error_message, 512);
    }

    /**
     * Get license key path.
     * Also checks if license key exists.
     *
     * @param array $license            
     *
     * @return mixed
     */
    private static function _LicGetKeyPath($license)
    {
        
        /**
         *
         * @var string
         */
        $key_path = self::Config()->Get('path.app') . 'etc/pki/private/' . $license['cn'] . '.key';
        
        if (! is_readable($key_path)) {
            /**
             *
             * @var string
             */
            $error_message = __('License Error: Your license key is missing.', 'WEb');
            
            return self::_LicTriggerError($license, 'missing', $error_message);
        }
        
        return $key_path;
    }

    /**
     * Check application license.
     *
     * @return mixed
     */
    private static function _LicCheck()
    {
        
        /**
         * Initialize license info.
         */
        $license = self::_LicSetParams();
        
        /**
         * Check if the license key is present.
         */
        self::_LicGetKeyPath($license);
        
        /**
         * Check if the licensepath isreadable, or if a license check request ('licenseCheck') is present.
         */
        if (is_readable($license['path']) || (! empty($_POST['r']) && '88096f65d8ce832d4f0a0edac045f7b9160a064d' == $_POST['r'])) {
            return self::_LicUpdate($license);
        }
        
        return TRUE;
    }
}
?>