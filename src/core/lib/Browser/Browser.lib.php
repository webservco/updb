<?php
/**
 * $Id: Browser.lib.php 168 2014-04-26 07:28:51Z radu $
 *
 * Browser library.
 *
 * @author     Radu Maier <radu@webserv.co>
 * @copyright  Copyright (c) 2008 - 2014, Radu Maier
 * @license    http://www.opensource.org/licenses/bsd-license.php
 * @package    WEb
 * @subpackage WebBrowser
 * @version    $Revision: 168 $
 * @since      Version 5
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
 * Browser
 * Browser library.
 * 
 * @author Radu Maier <radu@webserv.co>
 * @package WEb
 * @subpackage WebBrowser
 */
class Browser extends Library {

	/**
	 * Stores library properties.
	 * 
	 * @var array
	 */
	private $_mProp = array();

	/**
	 * Stores post data.
	 * 
	 * @var array
	 */
	private $_mPostData = array();

	/**
	 * Implement GetInstance() method.
	 * This is required for PHP < 5.3 compatibility (no late static binding)
	 * 
	 * @param string $className
	 *        	class name to instantiate
	 * @return object
	 */
	public static function GetInstance($className = __CLASS__) {

		/**
		 * Call parent method with current class name as parameter.
		 */
		return parent::_GetInstance($className);
	}

	/**
	 * Set psot data.
	 * 
	 * @param array $postData        	
	 */
	public function SetPostData($postData = array()) {

		$this->_mPostData = $postData;
	}

	/**
	 * Open URL handle.
	 * 
	 * @param string $url        	
	 *
	 * @return mixed
	 */
	public function Open($url) {

		if (empty($this->_mProp)) {
			$this->_SetDefaults();
		}
		
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
		$headers['User-Agent'] = $this->_mProp['agent'];
		
		/**
		 *
		 * @var unknown_type
		 */
		$headers['Referer'] = $this->_mProp['referer'];
		
		/**
		 *
		 * @var unknown_type
		 */
		$headers['Accept-Encoding'] = $this->_mProp['encoding'];
		
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
		if (!$socket) {
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
		$request = (!empty($this->_mPostData)) ? "POST $path HTTP/1.0\r\n" : "GET $path HTTP/1.0\r\n";
		
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
		if (!empty($this->_mPostData)) {
			/**
			 * Init post data.
			 * 
			 * @var array
			 */
			$post_data = array();
			
			/**
			 * Iterate post data.
			 */
			foreach ($this->_mPostData as $post_key => $post_value) {
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
		if (!empty($post_string)) {
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
	 * Set default captcha settings.
	 * 
	 * @return bool
	 */
	private function _SetDefaults() {

		/**
		 *
		 * @var array
		 */
		$defaults = Core::Config()->Get('browser');
		
		/**
		 *
		 * @var array
		 */
		$this->_mProp['agent'] = !empty($defaults['agent']) ? $defaults['agent'] : Core::Useragent()->GetUserAgent();
		
		/**
		 *
		 * @var array
		 */
		$this->_mProp['referer'] = !empty($defaults['referer']) ? $defaults['referer'] : Core::Config()->Get('url.www');
		
		/**
		 *
		 * @var array
		 */
		$this->_mProp['encoding'] = !empty($defaults['encoding']) ? $defaults['encoding'] : 'x-compress; x-zip';
		
		return TRUE;
	}
}
?>