<?php
/**
 * $Id: Router.lib.php 229 2017-03-28 08:15:08Z radu $
 *
 * Router library.
 *
 * @author     Radu Maier <radu@webserv.co>
 * @copyright  Copyright (c) 2008 - 2012, Radu Maier
 * @license    http://www.opensource.org/licenses/bsd-license.php
 * @package    WEb
 * @subpackage WebRouter
 * @version    $Revision: 229 $
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
 * Router
 * Router library.
 *
 * @author Radu Maier <radu@webserv.co>
 * @package WEb
 * @subpackage WebRouter
 */
class Router extends Library
{

    /**
     * Stores the request.
     *
     * @var string
     */
    private $_mRequest = NULL;

    /**
     * Stores the raw request.
     *
     * @var string
     */
    private $_mRawRequest = NULL;

    /**
     * Stores the raw URL as appears in browser.
     *
     * @var string
     */
    private $_mRawUrl = NULL;

    /**
     * Stores the current location.
     * Useful for redirects.
     * This is set to url route,
     * not necessarily module/controller/action.
     * In this class however, it is stored as full location including request query.
     *
     * @var string
     */
    private $_mLocation = NULL;

    /**
     * Stores default route.
     *
     * @var string
     */
    private $_mDefaultRoute = NULL;

    /**
     * Stores custom routes.
     *
     * @var array
     */
    private $_mCustomRoutes = array();

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
     * Get request.
     * This is the main functionality.
     *
     * @return array
     */
    public function GetRequest()
    {
        if (Core::IsCli()) {
            
            return NULL;
        }
        
        if (empty($this->_mRequest)) {
            
            $this->_mRequest = $this->_Route();
        }
        
        return $this->_mRequest;
    }

    /**
     * Remove request query item.
     *
     * @param string $item            
     *
     * @return boolean
     */
    public function RemoveRequestQueryItem($item)
    {
        if (isset($this->_mRequest['query'][$item])) {
            
            unset($this->_mRequest['query'][$item]);
            
            return TRUE;
        }
        
        return FALSE;
    }

    /**
     * Get current Url.
     *
     * @return string
     */
    public function GetCurrentUrl()
    {
        $url = Core::Config()->Get('url.www');
        
        return $url . $this->_mLocation;
    }

    /**
     * Get referer.
     *
     * @return string
     */
    public function GetReferer()
    {
        return ! empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : NULL;
    }

    /**
     * Get unrouted (raw) Url.
     *
     * @return string
     */
    public function GetCurrentRawUrl()
    {
        return $this->_mRawUrl;
    }

    /**
     * Get request protocol (http or https).
     *
     * @return string
     */
    public function GetProtocol()
    {
        $protocol = 'http';
        
        if (isset($_SERVER['HTTPS']) && 'off' != $_SERVER['HTTPS']) {
            
            $protocol = 'https';
        } else if ((! empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') || (! empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on')) {
            
            $protocol = 'https';
        }
        
        return $protocol;
    }

    /**
     * Get host name.
     *
     * @return string
     */
    public function GetHost()
    {
        
        /**
         *
         * @var string
         */
        $host = NULL;
        
        if (isset($_SERVER['HTTP_HOST']) && ('' != $_SERVER['HTTP_HOST'])) {
            /**
             *
             * @var string
             */
            $host = $_SERVER['HTTP_HOST'];
        } else if (isset($_SERVER['SERVER_NAME']) && ('' != $_SERVER['SERVER_NAME'])) {
            /**
             *
             * @var string
             */
            $host = $_SERVER['SERVER_NAME'];
        }
        
        return $host;
    }

    /**
     * Perform routing.
     * Get, parses, and sets the request.
     *
     * @return array
     */
    private function _Route()
    {
        Core::Log()->Write(__('Router library: Initializing routing.', 'WEb'), 'debug');
        
        /**
         * Set Raw URL.
         *
         * @var string
         */
        $this->_mRawUrl = $this->GetProtocol() . '://' . $this->GetHost() . $_SERVER['REQUEST_URI'];
        
        /**
         * Set request.
         *
         * @var string
         */
        $this->_mRawRequest = $this->_GetRawRequest();
        
        /**
         * Set temporary location to current request.
         *
         * @var string
         */
        $this->_mLocation = Core::Security()->FilterInput($this->_mRawRequest, TRUE, TRUE);
        
        /**
         * Parse custom routes
         */
        $this->_ParseCustomRoutes();
        
        $keep_getrequest = Core::Config()->Get('router.keep_getrequest');
        
        if (empty($keep_getrequest)) {
            
            /**
             * Empty _GET/_REQUEST array for security.
             */
            $this->_EmptyGetRequest();
        }
        
        /**
         * Get parsed request.
         *
         * @var array
         */
        $request = $this->_GetParsedRequest();
        
        /**
         * Format final request.
         */
        return $this->_FormatFinalRequest($request);
    }

    /**
     * Format request query.
     * Returns key/value or numeric indexed array.
     * Request can be in the form 'lorem/ipsum/dolor/sic'.
     * If parameter count is not even, the last parameter
     * is returned as ['param'] = 'param'.
     *
     * @param string $requestQuery            
     * @param bool $returnNumeric
     *            return numeric indexed array instead of key/value?
     * @return array
     */
    public function ParseRequestQuery($requestQuery, $returnNumeric = FALSE)
    {
        
        /**
         *
         * @var array
         */
        $query = array();
        
        /**
         *
         * @var array
         */
        $raw_query = explode('/', $requestQuery);
        
        /**
         *
         * @var array
         */
        $raw_query = array_diff($raw_query, array(
            ''
        ));
        
        if (FALSE !== $returnNumeric) {
            return $raw_query;
        }
        
        $params = count($raw_query);
        
        for ($position = 0; $position < $params; $position += 2) {
            /**
             * If the data count is not even, set the last value to empty.
             *
             * @var array
             */
            $query[$raw_query[$position]] = ($position == ($params - 1)) ? NULL : $raw_query[$position + 1];
        }
        
        Core::Log()->Write(__('Router library: Parsed request query.', 'WEb'), 'debug');
        
        return $query;
    }

    /**
     * Remove fake extension from request.
     *
     * @param string $request            
     */
    public function RemoveFakeExtensionDUMP($request)
    {
        
        /**
         *
         * @var string
         */
        $extension = Core::Config()->Get('seo.extension');
        
        /**
         *
         * @var bool
         */
        $check = Core::String()->EndsWith($extension, $request);
        
        /**
         * Check if request ends with the fake extension.
         */
        if (('' != $extension) && (FALSE !== $check)) {
            Core::Log()->Write(__('Router library: Removing fake extension.', 'WEb'), 'debug');
            
            return str_replace($extension, '', $request);
        }
        
        return $request;
    }

    /**
     * Remove fake extension from request.
     *
     * @param string $request            
     */
    public function RemoveFakeExtension($request)
    {
        
        /**
         *
         * @var string
         */
        $extension = Core::Config()->Get('seo.extension');
        
        if (! empty($extension)) {
            
            if (is_array($extension)) {
                
                foreach ($extension as $extension) {
                    
                    /**
                     *
                     * @var bool
                     */
                    $check = Core::String()->EndsWith($extension, $request);
                    
                    /**
                     * Check if request ends with the fake extension.
                     */
                    if (('' != $extension) && (FALSE !== $check)) {
                        Core::Log()->Write(__('Router library: Removing fake extension.', 'WEb'), 'debug');
                        
                        $request = str_replace($extension, '', $request);
                    }
                }
            } else {
                
                /**
                 *
                 * @var bool
                 */
                $check = Core::String()->EndsWith($extension, $request);
                
                /**
                 * Check if request ends with the fake extension.
                 */
                if (('' != $extension) && (FALSE !== $check)) {
                    Core::Log()->Write(__('Router library: Removing fake extension.', 'WEb'), 'debug');
                    
                    $request = str_replace($extension, '', $request);
                }
            }
        }
        
        return $request;
    }

    /**
     * Perform redirect.
     * If the locationis NULL, a self redirect will be performed.
     * If the location is "/", a homepage redirect will be performed.
     * If the location starts with "http://" or "https://", an external redirect will be performed.
     *
     * @param string $newLocation            
     *
     * @return void
     */
    public function Redirect($newLocation = NULL)
    {
        if (empty($newLocation)) {
            
            return $this->_RedirectSelf();
        }
        
        $url_www = Core::Config()->Get('url.www');
        
        /**
         * Check if the "home" keyword is present.
         */
        if ('/' == $newLocation) {
            
            /**
             * Redirect to home.
             *
             * @var string
             */
            $url = $url_www;
        } else {
            
            if (Core::String()->StartsWith('http://', $newLocation) || Core::String()->StartsWith('https://', $newLocation)) {
                /**
                 *
                 * @var string
                 */
                $url = $newLocation;
            } else {
                /**
                 *
                 * @var string
                 */
                $url = $url_www . $newLocation;
            }
        }
        
        /**
         * Perform redirect.
         */
        header("Location: {$url}");
        
        /**
         * Stop script execution.
         */
        exit();
    }

    /**
     * Get current request.
     *
     * @return string
     */
    private function _GetRawRequest()
    {
        
        /**
         *
         * @var string
         */
        $request = NULL;
        
        /**
         * This has to be first, in order for URLs containing "/index.php/" to be parsed correctly.
         */
        if (isset($_SERVER['REQUEST_URI'])) {
            
            Core::Log()->Write(__('Router library: Performing REQUEST_URI routing.', 'WEb'), 'debug');
            
            /**
             * Set request.
             *
             * @var string
             */
            $request = $this->_processRequest($_SERVER['REQUEST_URI']);
        } else if (isset($_SERVER['PATH_INFO'])) {
            
            Core::Log()->Write(__('Router library: Performing PATH_INFO routing.', 'WEb'), 'debug');
            
            /**
             * Set request.
             *
             * @var string
             */
            $request = $this->_ProcessRequest($_SERVER['PATH_INFO']);
        } else if (isset($_SERVER['ORIG_PATH_INFO'])) {
            
            Core::Log()->Write(__('Router library: Performing ORIG_PATH_INFO routing.', 'WEb'), 'debug');
            
            /**
             * Set request.
             *
             * @var string
             */
            $request = $this->_processRequest($_SERVER['ORIG_PATH_INFO']);
        } /**
         * In this case the request is old style (?option=value)
         * so we need to do some rewriting.
         */
        else if (isset($_SERVER['QUERY_STRING']) && ('' != $_SERVER['QUERY_STRING'])) {
            
            Core::Log()->Write(__('Router library: Performing QUERY_STRING routing.', 'WEb'), 'debug');
            
            /**
             * Process QUERY_STRING.
             * This method doesn't return anything.
             * It does a redirect.
             */
            return $this->_ProcessQueryString();
        }
        
        /**
         *
         * @var string
         */
        $request = ltrim($request, '/');
        
        /**
         *
         * @var string
         */
        $request = rtrim($request, '/');
        
        return $request;
    }

    /**
     * Process request.
     *
     * @param string $firstPart            
     *
     * @return string
     */
    private function _ProcessRequest($firstPart = NULL)
    {
        if (FALSE !== strpos($firstPart, 'index.php?')) {
            
            return $this->_ProcessQueryString();
        }
        
        if (FALSE !== strpos($firstPart, '/index.php/')) {
            
            $firstPart = str_replace('/index.php/', '/', $firstPart);
        }
        
        $add_req2 = TRUE;
        
        if (FALSE !== strpos($firstPart, '?')) {
            
            $add_req2 = FALSE;
            
            if ('/' != substr($firstPart, strpos($firstPart, '?') - 1, 1)) {
                
                $firstPart = str_replace('?', '/?', $firstPart);
            }
        }
        
        /**
         * Initialize first part of request.
         *
         * @var string
         */
        $req1 = $this->_FormatRequest($firstPart);
        
        /**
         * Initialize second part of request.
         *
         * @var string
         */
        $req2 = '';
        
        if ($add_req2) {
            
            /**
             * Check for QUERY_STRING.
             */
            if (isset($_SERVER['QUERY_STRING']) && ('' != $_SERVER['QUERY_STRING'])) {
                /**
                 * Process request.
                 * Replace '&' with '/'.
                 *
                 * @var string
                 */
                $req2 = $this->_FormatRequest($_SERVER['QUERY_STRING']);
            }
        }
        
        return $this->RemoveFakeExtension($req1 . $req2);
    }

    /**
     * Transform normal request string into custom one.
     *
     * @param string $requestString            
     *
     * @return string
     */
    private function _FormatRequest($requestString)
    {
        
        /**
         *
         * @var string
         */
        $www_url = Core::Config()->Get('url.www');
        
        /**
         *
         * @var string
         */
        $server_path = parse_url($www_url, PHP_URL_PATH);
        
        /**
         *
         * @var string
         */
        $unwanted = ('/' != $server_path) ? $server_path : '';
        
        /**
         *
         * @var string
         */
        $request_string = str_replace($unwanted, '', $requestString);
        
        /**
         *
         * @var string
         */
        $request_string = str_replace('?', '', $request_string);
        
        /**
         *
         * @var string
         */
        $request_string = str_replace('&', '/', $request_string);
        
        /**
         *
         * @var string
         */
        $request_string = str_replace('=', '/', $request_string);
        
        /**
         *
         * @var string
         */
        $request_string = str_replace('//', '/0/', $request_string);
        
        return $request_string;
    }

    /**
     * Process query string.
     * Turn normal request (?option1=value1&option2=value2) into internal request (/value1/value2).
     * Then do a redirect.
     *
     * @return mixed.
     */
    private function _ProcessQueryString()
    {
        
        /**
         * Init query string.
         * It is easier to work with the $_GET so we use it instead of QUERY_STRING.
         *
         * @var string
         */
        $qs = implode('/', $_GET);
        
        /**
         * Init request uri.
         *
         * @var string
         */
        $req_uri = str_replace('?' . $_SERVER['QUERY_STRING'], '/' . $qs, $_SERVER['REQUEST_URI']);
        
        /**
         *
         * @var string
         */
        $req_uri = str_replace('//', '/', $req_uri);
        
        /**
         *
         * @var string
         */
        $url = $this->GetProtocol() . '://' . $this->GetHost() . $req_uri;
        
        /**
         * Redirect to new custom URL.
         */
        return header("Location: " . $url);
    }

    /**
     * Parse custom routes list.
     * This also checks for empty request and sets to default route.
     *
     * @return bool
     */
    private function _ParseCustomRoutes()
    {
        Core::Log()->Write(__('Router library: Parsing custom routes.', 'WEb'), 'debug');
        
        /**
         * Set costom routes, (and also default route).
         */
        $this->_SetCustomRoutes();
        
        /**
         * Check for empty request.
         */
        if (NULL == $this->_mRawRequest) {
            Core::Log()->Write(__('Router library: Empty request. Setting to default.', 'WEb'), 'debug');
            
            /**
             * Set request to default request, and exit.
             *
             * @var string
             */
            $this->_mRawRequest = $this->_mDefaultRoute;
            
            return TRUE;
        }
        
        /**
         * Parse custom routes.
         */
        foreach ($this->_mCustomRoutes as $route_name => $route_value) {
            /**
             * Replace {num} wildcards with regular expressions.
             *
             * @var string
             */
            $route_name = str_replace('{num}', '[0-9]+', $route_name);
            
            /**
             * Replace {any} wildcards with regular expressions.
             *
             * @var string
             */
            $route_name = str_replace('{any}', '.+', $route_name);
            
            /**
             * Check for a custom route match.
             */
            if (preg_match('#^' . $route_name . '$#', $this->_mRawRequest) || preg_match('#^' . $route_name . '/$#', $this->_mRawRequest)) {
                /**
                 * Check for back-references.
                 */
                if ((strpos($route_value, '$') !== FALSE) && (strpos($route_name, '(') !== FALSE)) {
                    /**
                     * Parse request.
                     *
                     * @var string
                     */
                    $route_value = preg_replace('#^' . $route_name . '$#', $route_value, $this->_mRawRequest);
                }
                /**
                 * Set new request.
                 *
                 * @var string
                 */
                $this->_mRawRequest = $route_value;
                
                Core::Log()->Write(__('Router library: Found matching custom route.', 'WEb'), 'debug');
                
                return TRUE;
            }
        }
        
        Core::Log()->Write(__('Router library: No matching custom route found.', 'WEb'), 'debug');
        
        return FALSE;
    }

    /**
     * Set custom routes from config.
     * This also sets the default route.
     *
     * @return bool
     */
    private function _SetCustomRoutes()
    {
        $routes = Core::Config()->Get('route');
        
        if (empty($routes)) {
            $this->_mCustomRoutes = array();
            
            return FALSE;
        }
        
        if ('name' === key($routes)) {
            $routes = array(
                0 => $routes
            );
        }
        
        foreach ($routes as $route) {
            if ('index' == $route['name']) {
                Core::Log()->Write(__('Router library: Setting default route.', 'WEb'), 'debug');
                
                /**
                 *
                 * @var string
                 */
                $this->_mDefaultRoute = $route['value'];
            } else {
                Core::Log()->Write(__('Router library: Setting custom route.', 'WEb'), 'debug');
                
                /**
                 *
                 * @var array
                 */
                $this->_mCustomRoutes[$route['name']] = $route['value'];
            }
        }
        
        return TRUE;
    }

    /**
     * Empty _GET array and corresponding _REQUEST data.
     *
     * @return boolean
     */
    private function _EmptyGetRequest()
    {
        
        /**
         * Check if a request exists.
         */
        if (! empty($_GET)) {
            
            /**
             * Remove corresponding _REQUEST data.
             */
            foreach ($_GET as $key => $value) {
                
                unset($_REQUEST[$key]);
            }
            
            /**
             * Empty _GET array.
             */
            $_GET = array();
            
            Core::Log()->Write(__('Router library: Emptying _GET/_REQUEST data.', 'WEb'), 'debug');
            
            return TRUE;
        }
        
        return FALSE;
    }

    /**
     * Parse request for returning to application library.
     *
     * @return array
     */
    private function _GetParsedRequest()
    {
        
        /**
         * Create list of keys.
         *
         * @var _array
         */
        $request_keys = array(
            'module',
            'controller',
            'action',
            'query'
        );
        
        /**
         * Split request in four parts.
         *
         * @var array
         */
        $request_values = explode('/', $this->_mRawRequest, 4);
        
        /**
         *
         * @var int
         */
        $total_values = count($request_values);
        
        /**
         * Check if we have enough values.
         */
        if (4 > $total_values) {
            /**
             * Fill with blanks.
             */
            $request_values = array_pad($request_values, 4, NULL);
        }
        
        /**
         * Create new request array.
         *
         * @var array
         */
        $request = array_combine($request_keys, $request_values);
        
        /**
         * If module is empty, set it to default.
         *
         * @var string
         */
        $request['module'] = (! empty($request['module'])) ? Core::Security()->FilterInput($request['module'], TRUE, TRUE) : Core::Config()->Get('router.default.module');
        
        /**
         * If controller is empty, set it to default.
         *
         * @var string
         */
        $request['controller'] = (! empty($request['controller'])) ? Core::Security()->FilterInput($request['controller'], TRUE, TRUE) : Core::Config()->Get('router.default.controller');
        
        /**
         * If action is empty, set it to default.
         *
         * @var string
         */
        $request['action'] = (! empty($request['action'])) ? Core::Security()->FilterInput($request['action'], TRUE, TRUE) : Core::Config()->Get('router.default.action');
        
        /**
         * If query is not empty, filter it.
         *
         * @var string
         */
        $request['query'] = (! empty($request['query'])) ? Core::Security()->FilterInput($request['query'], TRUE, TRUE) : '';
        
        /**
         * Set final location.
         * This contains only the url route with the request query filtered out.
         *
         * @var string
         */
        $request['location'] = rtrim(str_replace($request['query'], '', $this->_mLocation), '/');
        
        Core::Log()->Write(__('Router library: Request parsing done.', 'WEb'), 'debug');
        
        return $request;
    }

    private function _FormatFinalRequest($request)
    {
        
        /**
         *
         * @var string
         */
        // $module = ucfirst(strtolower($request['module'])); //XXX
        $module = $request['module'];
        
        /**
         *
         * @var string
         */
        $request['module'] = 'Module_' . $module;
        
        /**
         *
         * @var string
         */
        // $controller = ucfirst(strtolower($request['controller'])); //XXX
        $controller = $request['controller'];
        
        /**
         *
         * @var string
         */
        $request['controller'] = 'Controller_' . $module . '_' . $controller;
        
        /**
         *
         * @var string
         */
        // $request['action'] = ucfirst(strtolower($request['action'])); //XXX
        $request['action'] = $request['action'];
        
        /**
         *
         * @var array
         */
        $request['query'] = (! empty($request['query'])) ? $this->ParseRequestQuery($request['query']) : array();
        
        Core::Log()->Write(__('Router library: Request formatting done.', 'WEb'), 'debug');
        
        Core::Log()->Write(__('Router library: Routing complete.', 'WEb'), 'debug');
        
        return $request;
    }

    /**
     * Perform a self redirect.
     *
     * @return void
     */
    private function _RedirectSelf()
    {
        $url = $this->GetCurrentUrl();
        
        /**
         * Redirect.
         */
        header("Location: {$url}");
        
        /**
         * Stop script execution.
         */
        exit();
    }
}
?>