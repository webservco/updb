<?php
/**
 * $Id: FacebookApi.lib.php 207 2015-12-28 08:37:03Z radu $
 *
 * Facebook API library.
 *
 * @author     Radu Maier <radu@webserv.co>
 * @copyright  Copyright (c) 2008 - 2014, Radu Maier
 * @license    http://www.opensource.org/licenses/bsd-license.php
 * @package    WEb
 * @subpackage WebFacebookApi
 * @version    $Revision: 207 $
 * @since      Version 7
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
 * FacebookApi
 * Facebook API library.
 *
 * @author Radu Maier <radu@webserv.co>
 * @package WEb
 * @subpackage WebFacebookApi
 */
class FacebookApi extends Library {

	private $_mFacebook = NULL;

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

	public function GetAppId() {

		return Core::Config()->Get('facebookapi.appid');
	}

	public function GetDefaultMeta($data) {

		return array(
			'fb' => array(
				'app_id' => $this->GetAppId() 
			),
			'og' => array(
				'type' => 'article',
				'title' => $data['strings']['title'],
				'site_name' => $data['strings']['title_app'],
				'url' => $data['url']['current'],
				'description' => Core::Config()->Get('app.description'),
				'image' => "{$data['url']['www']}apple-touch-icon.png",
				'locale' => $data['i18n']['langs'][$data['i18n']['lang']]['locale'], 
			) 
		);
	}

	private function _InitApi() {

		if (! empty($this->_mFacebook)) {
			
			return FALSE;
		}
		
		$path_app = Core::Config()->Get('path.app');
		
		$config = Core::Config()->Get('facebookapi');
		
		if (empty($config['appid']) || empty($config['secret'])) {
			
			throw new ErrorException(__('FacebookApi Library: Initialization error - missing credentials', 'App'));
		}
		
		require "{$path_app}{$config['path_sdk']}facebook.php";
		
		$this->_mFacebook = new Facebook(array(
			'appId' => $config['appid'],
			'secret' => $config['secret'],
			'allowSignedRequest' => FALSE 
		));
		
		if (empty($this->_mFacebook)) {
			
			throw new ErrorException(__('FacebookApi Library: Initialization error.', 'App'));
		}
	}

	public function GetUserId() {

		$this->_InitApi();
		
		return $this->_mFacebook->getUser();
	}

	public function GetLoginUrl($redirectUri) {

		$this->_InitApi();
		
		$scope = Core::Config()->Get('facebookapi.scope');
		
		return $this->_mFacebook->getLoginUrl(array(
			'scope' => $scope,
			'redirect_uri' => $redirectUri 
		));
	}

	public function GetLogoutUrl($nextUrl = NULL) {

		$this->_InitApi();
		
		return $this->_mFacebook->getLogoutUrl(array(
			'next' => $nextUrl 
		));
	}

	public function Logout() {

		$this->_InitApi();
		
		$this->_mFacebook->destroySession();
		
		unset($_SESSION['FacebookApi']);
		
		return TRUE;
	}

	public function GetProfile() {

		$this->_InitApi();
		
		$user_id = $this->GetUserId();
		
		if (! empty($user_id)) {
			
			return $this->_mFacebook->api('/me');
		}
		
		return FALSE;
	}

	/**
	 * @TODO check
	 *
	 * @param string $path        	
	 * @param string $message        	
	 * @return mixed boolean
	 */
	public function PostPhoto($path, $message) {

		$this->_InitApi();
		
		$user_id = $this->GetUserId();
		
		if (! empty($user_id)) {
			
			Core::Log()->Write(__('FacebookApi Library: Posting photo.', 'App'), 'debug');
			
			try {
				
				$this->_mFacebook->setFileUploadSupport(true);
				
				$response = $this->_mFacebook->api('/me/photos/', 'post', array(
					'message' => $message,
					'source' => "@{$path}" 
				));
				
				Core::Log()->Write(print_r($response, TRUE), 'debug');
				
				if (! empty($response['post_id'])) {
					
					Core::Log()->Write(__('FacebookApi Library: Posting photo - success.', 'App'), 'debug');
					
					return $response['post_id'];
				}
			} catch (FacebookApiException $e) {
				
				Core::Log()->Write($e->Getmessage(), 'error');
			}
		}
		
		return FALSE;
	}

	public function ScrapeUrl($url) {

		$fields = array(
			'id' => urlencode($url),
			'scrape' => true 
		);
		
		$fields_string = NULL;
		
		foreach ($fields as $key => $value) {
			
			$fields_string .= "{$key}={$value}&";
		}
		
		rtrim($fields_string, '&');
		
		$curl = curl_init();
		
		curl_setopt($curl, CURLOPT_URL, 'https://graph.facebook.com');
		
		curl_setopt($curl, CURLOPT_POST, TRUE);
		
		curl_setopt($curl, CURLOPT_POSTFIELDS, $fields_string);
		
		$result = curl_exec($curl);
		
		if ($result === FALSE) {
			
			$error_message = curl_error($curl);
			
			throw new ErrorException(__('FacebookApi Library: FacebookScrape cURL error', 'App') . " - {$error_message}");
			
			exit();
		}
		
		curl_close($curl);
		
		return $result;
	}
}
?>