<?php
/**
 * $Id: Security.lib.php 230 2017-03-28 08:58:16Z radu $
 *
 * Security library.
 *
 * @author     Radu Maier <radu@webserv.co>
 * @copyright  Copyright (c) 2008 - 2012, Radu Maier
 * @license    http://www.opensource.org/licenses/bsd-license.php
 * @package    WEb
 * @subpackage WebSecurity
 * @version    $Revision: 230 $
 * @since      Version 1
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
 * Security
 * Security library.
 *
 * @author Radu Maier <radu@webserv.co>
 * @package WEb
 * @subpackage WebSecurity
 */
class Security extends Library {

	/**
	 * Stores total number of GET requests.
	 *
	 * @var int
	 */
	private $_mNumGet;

	/**
	 * Stores total number of POST requests.
	 *
	 * @var int
	 */
	private $_mNumPost;

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
	 * Sanitize input.
	 *
	 * @return bool
	 */
	public function Sanitize() {

		Core::Log() -> Write(__('Security library: Initializing input sanitization.', 'WEb'), 'debug');

		/**
		 * Init GET request number.
		 *
		 * @var int
		 */
		$this -> _mNumGet = 0;

		/**
		 * Filter $_GET array.
		 *
		 * @var array
		 */
		$_GET = array_map(array(
			$this,
			'_FilterRequest'
		), $_GET);

		/**
		 *
		 * @var string
		 */
		$get_message = sprintf(___('Security library: %s GET request sanitized.', 'Security library: %s GET requests sanitized.', $this -> _mNumGet, 'WEb'), $this -> _mNumGet);

		Core::Log() -> Write($get_message, 'debug');

		/**
		 * Init POST request number.
		 *
		 * @var int
		 */
		$this -> _mNumPost = 0;

		/**
		 * Filter $_POST array.
		 * IMPORTANT: Tags are not disabled nor removed.
		 * Further validation is required.
		 * If database connection is not available,
		 * filtering is done as for $_GET.
		 */
		$_POST = array_map(array(
			$this,
			'_FilterPost'
		), $_POST);

		/**
		 *
		 * @var string
		 */
		$post_message = sprintf(___('Security library: %s POST request sanitized.', 'Security library: %s POST requests sanitized.', $this -> _mNumPost, 'WEb'), $this -> _mNumPost);

		Core::Log() -> Write($post_message, 'debug');

		Core::Log() -> Write(__('Security library: Sanitize complete.', 'WEb'), 'debug');

		return TRUE;
	}

	/**
	 * Filter request.
	 *
	 * @param string $input
	 *        	request
	 * @param boolean $disableTags
	 * @return string
	 */
	private function _FilterRequest($input, $disableTags = TRUE) {

		/**
		 * Filter input.
		 *
		 * @var string
		 */
		$input = $this -> FilterInput($input, $disableTags, TRUE);

		$this -> _mNumGet++;

		return $input;
	}

	/**
	 * Filter post data.
	 * Please note that this method doesn't disable tags.
	 *
	 * @param mixed $input
	 *        	data
	 * @return mixed
	 */
	private function _FilterPost($input) {

		if (is_array($input)) {

			foreach ($input as $key => $value) {

				$input[$key] = $this -> _FilterPost($value, FALSE, FALSE);

				$this -> _mNumPost++;
			}
		} else {

			$input = $this -> FilterInput($input, FALSE, FALSE);

			$this -> _mNumPost++;
		}

		return $input;
	}

	/**
	 * Filter user input.
	 *
	 * @param string $input
	 *        	input string
	 * @param bool $disableTags
	 *        	should we disable tags?
	 * @param bool $filterRequest
	 *        	is method used to filter $_GET request?
	 * @return string
	 */
	public function FilterInput($input, $disableTags = TRUE, $filterRequest = FALSE) {
		/**
		 * Some unwanted chars.
		 *
		 * @var array
		 */
		$unwanted = array('\\');

		/**
		 * Remove unwanted chars.
		 * We didn't put these in the RemoveBadChars() method
		 * because we also want them removed
		 * if input is used in a database query.
		 *
		 * @var string
		 */
		$input = str_replace($unwanted, '', $input);

		/**
		 * Check if the method is used to filter $_GET request.
		 */
		if ((FALSE !== $filterRequest)) {

			/**
			 * Decode url string.
			 */
			$input = urldecode($input);

			/**
			 * Remove _GET request bad chars.
			 */
			$input = Core::String() -> RemoveRequestBadChars($input);

			/**
			 * Strip tags
			 *
			 * @var string.
			 */
			$input = Core::String() -> StripTags($input);
		}

		/**
		 * Check if we should also block tags.
		 */
		if (FALSE !== $disableTags) {
			/**
			 * Disable tags.
			 *
			 * @var string
			 */
			$input = Core::String() -> DisableTags($input);
		}

		/**
		 * Return result.
		 */
		return trim($input);
	}

	/**
	 * Get an assets's security salt.
	 *
	 * @param mixed $asset
	 * @param int $saltLength
	 *
	 * @return string
	 */
	public function GetSalt($asset, $saltLength = 20) {

		return strrev(substr(sha1(strtolower($asset)), 1, $saltLength));
	}

	/**
	 * Get an asset's security token.
	 * If empty asset is provided, a random token is generated.
	 *
	 * @param string $salt
	 * @param mixed $asset
	 *
	 * @return string
	 */
	public function GetToken($salt, $asset = NULL) {

		if (empty($asset)) {
			$asset = uniqid(rand(), true);
		}

		return $salt . sha1($salt . $asset);
	}

}
?>