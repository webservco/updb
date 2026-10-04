<?php
/**
 * $Id: Module.lib.php 224 2016-10-24 14:10:37Z radu $
 *
 * Module library.
 *
 * All modules extend this library.
 *
 * @author     Radu Maier <radu@webserv.co>
 * @copyright  Copyright (c) 2008 - 2012, Radu Maier
 * @license    http://www.opensource.org/licenses/bsd-license.php
 * @package    WEb
 * @subpackage WebModule
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
 * Module
 * Module library.
 *
 * @author Radu Maier <radu@webserv.co>
 * @package WEb
 * @subpackage WebModule
 */
class Module extends Library
{

    /**
     * Use sessions flag
     *
     * @var boolean
     */
    protected $_mUseSessions = FALSE;

    /**
     * Web app info.
     *
     * @var array
     */
    protected $_mWebAppInfo = array();

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
     * Load configuration file for current library.
     * Needs to override the Library method.
     *
     * @param string $className
     *            name of the class to load configuration for
     * @return bool
     */
    protected function _LoadConfig($className)
    {
        
        /**
         * App prefix.
         */
        $this->_mAppPrefix = Core::Config()->Get('app.prefix');
        
        /**
         * Add module i18n.
         */
        Core::I18n()->Add($className);
        
        /**
         * Load module i18n.
         */
        Core::I18n()->Load($className);
        
        /**
         *
         * @var array
         */
        $parts = explode('_', $className);
        
        /**
         *
         * @var string
         */
        // $module_name = array_key_exists(1, $parts) ? strtolower($parts[1]) : $parts[0]; //XXX
        $module_name = array_key_exists(1, $parts) ? $parts[1] : $parts[0];
        
        $module_file = strtolower($module_name) . '.cfg.xml';
        
        /**
         *
         * @var string
         */
        $path_app = Core::Config()->Get('path.app');
        
        /**
         *
         * @var string
         */
        // $cfg_file = $path_app . 'etc/' . Core::Config()->Get('dir.app.mod') . '/' . ucfirst($module_name) . '/' . $module_name . '.cfg.xml'; //XXX
        $cfg_file = $path_app . 'etc/' . Core::Config()->Get('dir.app.mod') . '/' . $module_name . '/' . $module_file;
        
        if (! is_readable($cfg_file)) {
            if (function_exists('__')) {
                /**
                 *
                 * @var string
                 */
                $config_msg = sprintf(__('%s module: Skipping configuration file "%s" (not found).', 'WEb'), $className, $module_file);
                
                Core::Log()->Write($config_msg, 'debug');
            }
            
            return FALSE;
        }
        
        if (function_exists('__')) {
            /**
             *
             * @var string
             */
            $config_msg = sprintf(__('%s module: Loading configuration file "%s".', 'WEb'), $className, $module_file);
            
            Core::Log()->Write($config_msg, 'debug');
        }
        
        return Core::Config()->Set($cfg_file);
    }

    /**
     * Get current module name.
     *
     * @param array $request            
     *
     * @return string
     */
    public function GetCurrentModule($request)
    {
        if ($request === null) {
            return null;
        }

        return strtolower(str_replace('Module_', '', $request['module']));
    }

    /**
     * General information.
     *
     * @param array $request            
     *
     * @return array
     */
    public function GetInfo($request)
    {
        $data = array();
        
        /**
         * Application prefix.
         */
        $data['prefix'] = $this->_mAppPrefix;
        
        /**
         * Current module name.
         */
        $data['module'] = $this->GetCurrentModule($request);
        
        /**
         * Current location (Module/Controller/Action)
         */
        $data['location'] = isset($request['location']) ? $request['location'] : null;
        
        /**
         * Location path.
         */
        $data['path'] = NULL;
        
        /**
         * Location path without page number.
         */
        $data['path_pagination'] = NULL;
        
        if (! empty($request['query'])) {
            
            foreach ($request['query'] as $key => $value) {
                
                $key = urlencode($key);
                
                $value = urlencode($value);
                
                $data['path'] .= "/{$key}/{$value}";
                
                if ('page' != $key) {
                    $data['path_pagination'] .= "/{$key}/{$value}";
                }
            }
        }
        
        /**
         * Next URL.
         */
        $data['url_next'] = ! empty($request['query']['next']) ? Core::String()->DecodeUrlString($request['query']['next']) : NULL;
        
        return $data;
    }

    public function GetDefaultUrls()
    {
        $data = array();
        
        /**
         * Current full URL.
         */
        $data['current'] = Core::Router()->GetCurrentUrl();
        
        /**
         * Current unrouted URL.
         */
        $data['current_raw'] = Core::Router()->GetCurrentRawUrl();
        
        /**
         * App URL.
         */
        $data['www'] = Core::Config()->Get('url.www');
        
        /**
         * Skin URL
         */
        $data['skin'] = Core::Config()->Get('url.www') . Core::Config()->Get('dir.app.usr') . '/' . Core::Config()->Get('dir.app.skins') . '/' . Core::Config()->Get('output.skin') . '/';
        
        /**
         * Scripts URL
         */
        $data['scripts'] = Core::Config()->Get('url.www') . Core::Config()->Get('dir.app.usr') . '/scripts/';
        
        /**
         * Media URL.
         */
        $data['media'] = Core::Config()->Get('url.www') . Core::Config()->Get('dir.app.usr') . '/media/';
        
        return $data;
    }

    /**
     * Get default paths.
     *
     * @return array
     */
    public function GetDefaultPaths()
    {
        $data = array();
        
        /**
         * App Path.
         */
        $data['app'] = Core::Config()->Get('path.app');
        
        /**
         * App WWW path.
         */
        $data['www'] = Core::Config()->Get('path.www');
        
        /**
         * Media Path.
         */
        $data['media'] = Core::Config()->Get('path.www') . Core::Config()->Get('dir.app.usr') . '/media/';
        
        return $data;
    }

    public function GetMenu($url_www)
    {
        $menu = array();
        
        $menu_config = Core::Config()->Get("menu");
        
        if (! empty($menu_config)) {
            
            foreach ($menu_config as $menu_node => $menu_data) {
                
                if (! empty($menu_data['item'])) {
                    
                    if ('title' === key($menu_data['item'])) {
                        
                        $menu_data['item'] = array(
                            0 => $menu_data['item']
                        );
                    }
                    
                    $menu[$menu_node] = $this->GetMenuItem($url_www, $menu_data['item']);
                }
            }
        }
        
        return $menu;
    }

    public function GetMenuItem($url_www, $menuData = array())
    {
        $menu = array();
        
        if (! empty($menuData)) {
            
            foreach ($menuData as $menu_item) {
                
                if (! empty($menu_item['url'])) {
                    
                    $menu_href = $menu_item['url'];
                } else {
                    
                    if (! empty($menu_item['link'])) {
                        
                        if ('index' == $menu_item['link']) {
                            
                            $menu_href = $url_www;
                        } else {
                            
                            $menu_href = $url_www . $menu_item['link'];
                        }
                    } else {
                        
                        $menu_href = NULL;
                    }
                }
                
                $submenu = NULL;
                
                if (! empty($menu_item['submenu'])) {
                    
                    $submenu_data = Core::Config()->Get("menu.{$menu_item['submenu']}.item");
                    
                    if (! empty($submenu_data)) {
                        $submenu = $this->GetMenuItem($url_www, $submenu_data);
                    }
                }
                
                $target = ! empty($menu_item['target']) ? $menu_item['target'] : NULL;
                
                $menu[] = array(
                    'title' => $menu_item['title'],
                    'href' => $menu_href,
                    'target' => $target,
                    'submenu' => $submenu,
                    'resource' => $menu_item['resource']
                );
            }
        }
        
        return $menu;
    }

    public function GetTimezone($request)
    {
        $timezone = NULL;
        
        if (! empty($request['query']['timezone'])) {
            
            $timezone = Core::String()->DecodeUrlString($request['query']['timezone']);
            
            Core::Date()->SetTimeZone($timezone);
            
            if ($this->_mUseSessions) {
                
                $_SESSION['Date']['TimeZone'] = $timezone;
            }
        }
        
        if ($this->_mUseSessions) {
            
            if (empty($_SESSION['Date']['TimeZone'])) {
                
                $_SESSION['Date']['TimeZone'] = Core::Date()->GetTimeZone();
            }
        }
        
        return ! empty($timezone) ? $timezone : Core::Date()->GetTimeZone();
    }

    public function GetWebAppInfo($request)
    {
        $data = array();
        
        /**
         * App Id from configuration file.
         */
        $data['app_id'] = Core::Config()->Get('app.id');
        
        /**
         * OS Type (android, ios, other).
         * This does not tell us if it's mobile or PC
         */
        $data['os_type'] = Core::Useragent()->GetOsType();
        
        /**
         * Device Type (tablet, phone, other).
         */
        $data['device_type'] = Core::Useragent()->GetDeviceType();
        
        /**
         * Get custom web app version sent from mobile devices.
         * Custom user agent string must end with "WebApp/VERSION".
         */
        $data['version'] = Core::Useragent()->GetWebAppVersion();
        
        /**
         * Web app market id.
         */
        $data['market'] = ! empty($request['query']['market']) ? (int) $request['query']['market'] : 0;
        
        /**
         * Web app type.
         * free/pro/etc
         */
        $data['type'] = ! empty($request['query']['app_type']) ? $request['query']['app_type'] : NULL;
        
        /**
         * Internet explorer version (0 if other browser).
         */
        $data['ie'] = Core::Useragent()->GetIeVersion();
        
        $this->_mWebAppInfo = $data;
        
        return $data;
    }

    public function GetLangInfo()
    {
        $data = array();
        
        /**
         * App language.
         */
        $data['lang'] = substr(Core::I18n()->GetLocale(), 0, 2);
        
        /**
         * List of available languages.
         */
        $data['langs'] = Core::I18n()->GetAvailableLangs();
        
        return $data;
    }

    public function ChangeLocale($newLang, $redirect = NULL)
    {
        $app_langs = Core::I18n()->GetAvailableLangs();
        
        if (! empty($newLang) && array_key_exists($newLang, $app_langs)) {
            
            Core::I18n()->SetLocale($app_langs[$newLang]['locale']);
            
            if ($this->_mUseSessions && ! empty($_SESSION['I18n'])) {
                
                $_SESSION['I18n']['Chosen'] = TRUE;
            }
            
            if (isset($redirect)) {
                
                Core::Router()->Redirect($redirect);
                
                exit();
            }
            
            return TRUE;
        }
        
        return FALSE;
    }

    public function GetUserInfo()
    {
        if ($this->_mUseSessions) {
            
            Core::User()->Init();
            
            return $_SESSION['User'];
        }
        
        return FALSE;
    }

    /**
     * Get default output data.
     *
     * @param array $request            
     *
     * @return array
     */
    public function GetDataDefault($request)
    {
        $data = array();
        
        /**
         * $data['query']
         * Request query.
         * Values:
         * array of key/data
         * Usage:
         * Controller: $data['query'][STUFF]
         * Template: $query[STUFF]
         */
        $data['query'] = ! empty($request['query']) ? $request['query'] : NULL;
        
        /**
         * $data['info']
         * General information
         * Values:
         * $data['info']['prefix'] - application prefix
         * $data['info']['module'] - current module name
         * $data['info']['location'] - current location (Module/App/Controller)
         * $data['info]['path'] - current location path (Item/1/page/3)
         * $data['info]['path_pagination'] - current location path without page number (Item/1/)
         * $data['info']['url_next'] - next url
         * Usage:
         * Controller: $data['info']['module']
         * Template: $info['module']
         */
        $data['info'] = $this->GetInfo($request);
        
        /**
         * $data['url']
         * Default URLs.
         * Values:
         * $data['url']['current'] (http://www.example.com/location)
         * $data['url']['www'] (http://www.example.com)
         * $data['url']['skin'] (http://www.example.com/myskin/)
         * $data['url']['scripts'] (http://www.example.com/js/)
         * $data['url']['media'] (http://www.example.com/media/)
         * Usage:
         * Controller: $data['url']['www']
         * Template: $url['www']
         */
        $data['url'] = $this->GetDefaultUrls();
        
        /**
         * $data['path']
         * Default paths.
         * Values:
         * $data['path']['app'] (/full/server/path/to/app/dir/)
         * $data['path']['www'] (/full/server/path/to/www/dir/)
         * $data['path']['media'] (/full/server/path/to/media/dir/)
         * Usage:
         * Controller: $data['path']['www']
         * Template: $path['www']
         */
        $data['path'] = $this->GetDefaultPaths();
        
        /**
         * $data['i18n']
         * Internationalization.
         * Values:
         * $data['i18n']['lang'] - 2 letter language code (en, it, etc)
         * $data['i18n']['langs'] - array of all available languages - language / locale
         * Usage:
         * Controller: $data['i18n']['lang']
         * Template: $i18n['lang']
         */
        $data['i18n'] = $this->GetLangInfo();
        
        /**
         * $data['strings']
         * Default messages used in most pages.
         * Values:
         * $data['strings']['title']
         * $data['strings']['description']
         * $data['strings']['app_title']
         * $data['strings']['no_data']
         * Usage:
         * Controller: $data['strings']['title']
         * Template: $strings['title']
         */
        $data['strings'] = $this->GetDefaultStrings();
        
        /**
         * $data['menu'] [OPTIONAL]
         * Custom menu functionality.
         * Values:
         * $data['menu'][MENU_NODE_FROM _CONFIG]['title']
         * $data['menu'][MENU_NODE_FROM_CONFIG]['href']
         * $data['menu'][MENU_NODE_FROM_CONFIG]['target']
         * $data['menu'][MENU_NODE_FROM_CONFIG]['submenu'] (another array with menu data)
         * $data['menu'][MENU_NODE_FROM_CONFIG]['resource']
         * Usage:
         * Controller: $data['menu']['MENU_NODE_FROM_CONFIG']
         * Template: $menu['MENU_NODE_FROM_CONFIG']
         */
        
        $opt_menus = Core::Config()->Get('options.menus');
        
        if (! empty($opt_menus)) {
            
            $data['menu'] = $this->GetMenu($data['url']['www']);
        }
        
        /**
         * $data['timezone'] [OPTIONAL]
         * Custom timezone functionality.
         * A custom timezone can be set by adding:
         * /timezone/ENCODEDTIMEZONEVALUE
         * to the URL
         * ENCODEDTIMEZONEVALUE is the timezone value encoded with Core::String()->EncodeUrlString
         * Example: "RXVyb3BlL1JvbWU." (Europe/Rome)
         * If session functionality is enabled, the custom timezone value is also stored in the variable:
         * $_SESSION['Date']['TimeZone']
         * Value:
         * string representation of custom timezone (Europe/Rome)
         * Usage:
         * Controller: $data['timezone']
         * Template: $timezone
         */
        
        $opt_timezone = Core::Config()->Get('options.timezone');
        
        if (! empty($opt_timezone)) {
            
            $data['timezone'] = $this->GetTimezone($request);
        }
        
        /**
         * $data['webapp'] [OPTIONAL]
         * Web app info.
         * Values:
         * $data['webapp']['app_id'] - custom app id (from config)
         * $data['webapp']['os_type'] - os type (android, ios, other)
         * $data['webapp']['device_type'] - mobile device type (tablet, phone, other)
         * $data['webapp']['version'] - custom app version set by_mobile devices
         * $data['webapp']['market'] - app market id (0 - none, 1 - google, 2 - samsung, 3 - amazon, 4 - itunes)
         * $data['webapp']['type'] - app type (free, pro, etc)
         * $data['webapp']['ie'] - Internet explorer version (0 if other browser).
         * Usage:
         * Controller: $data['webapp']['os_type']
         * Template: $webapp['os_type']
         */
        
        $opt_webapp = Core::Config()->Get('options.webapp');
        
        if (! empty($opt_webapp)) {
            
            $data['webapp'] = $this->GetWebAppInfo($request);
        }
        
        /**
         * $data['user'] [OPTIONAL]
         * User functionality.
         * Values (not logged in):
         * $data['user']['StatsCurrent']['time']
         * $data['user']['StatsCurrent']['ip']
         * $data['user']['StatsCurrent']['agent']
         * $data['user']['StatsCurrent']['auto']
         * $data['user']['InitialVisit']
         * Values (logged in):
         * $data['user']['Id']
         * $data['user']['Logged']
         * $data['user']['Persistent']
         * $data['user']['Prop']['user_id']
         * $data['user']['Prop']['user_email']
         * $data['user']['Prop']['user_status']
         * + custom user properties
         * $data['user']['StatsCurrent']['time']
         * $data['user']['StatsCurrent']['ip']
         * $data['user']['StatsCurrent']['agent']
         * $data['user']['StatsCurrent']['auto']
         * $data['user']['StatsPrevious']['time']
         * $data['user']['StatsPrevious']['ip']
         * $data['user']['StatsPrevious']['agent']
         * $data['user']['StatsPrevious']['auto']
         * $data['user']['InitialVisit']
         * Usage:
         * Controller: $data['user']['Id']
         * Template: $user['Id']
         */
        
        $opt_users = Core::Config()->Get('options.users');
        
        if (! empty($opt_users)) {
            
            $data['user'] = $this->GetUserInfo();
        }
        
        return $data;
    }

    public function GetData($request)
    {
        $this->_mUseSessions = (bool) Core::Config()->Get('options.sessions');
        
        $data = $this->GetDataDefault($request);
        
        return $this->GetDataCustom($request, $data);
    }
}
?>
