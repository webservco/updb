<?php
/**
 * $Id: Captcha.lib.php 168 2014-04-26 07:28:51Z radu $
 *
 * Captcha library.
 *
 * @author     Radu Maier <radu@webserv.co>
 * @copyright  Copyright (c) 2008 - 2014, Radu Maier
 * @license    http://www.opensource.org/licenses/bsd-license.php
 * @package    WEb
 * @subpackage WebCaptcha
 * @version    $Revision: 168 $
 * @since      Version 5
 * @access     public
 * @filesource
 */

/**
 * Security check.
 */
if (!defined('_WEB'))
{
	/**
	 * Stop script execution.
	 */
	exit();
}


/**
 * Captcha
 *
 * Captcha library.
 *
 * @author     Radu Maier <radu@webserv.co>
 * @package    WEb
 * @subpackage WebCaptcha
 *
 */
class Captcha extends Library
{
	/**
	 * Library properties.
	 *
	 * @var array
	 */
	private $_mProp = array();

	/**
	 * Implement GetInstance() method.
	 *
	 * This is required for PHP < 5.3 compatibility (no late static binding)
	 *
	 * @param string $className class name to instantiate
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
	 * Set captcha dimensions.
	 *
	 * @param int $width
	 * @param int $height
	 *
	 * @return bool
	 */
	public function SetDimensions($width, $height)
	{
		if (empty($this->_mProp))
		{
			$this->_SetDefaults();
		}

		/**
		 * @var array
		 */
		$this->_mProp['width'] = $width;

		/**
		 * @var array
		 */
		$this->_mProp['height'] = $height;

		return TRUE;
	}

	/**
	 * Set captcha color.
	 *
	 * @param string $hexColor
	 * @param string $colorType
	 *
	 * @return bool
	 */
	public function SetColor($hexColor, $colorType = 'background')
	{
		if (empty($this->_mProp))
		{
			$this->_SetDefaults();
		}

		/**
		 * @var array
		 */
		$color = str_split($hexColor, 2);

		/**
		 * @var array
		*/
		$color = array_map('hexdec', $color);

		switch ($colorType)
		{
			case 'foreground' :
				/**
				 * @var array
				 */
				$this->_mProp['foreground'] = $color;

				break;

			case 'background' :

			default :
				/**
				 * @var array
				 */
				$this->_mProp['background'] = $color;

				break;
		}

		return TRUE;
	}

	/**
	 * Set captcha font.
	 *
	 * @param string $font
	 *
	 * @return bool
	 */
	public function SetFont($font)
	{
		if (empty($this->_mProp))
		{
			$this->_SetDefaults();
		}

		/**
		 * @var array
		 */
		$this->_mProp['font'] = $font;

		return TRUE;
	}

	/**
	 * Set captcha font size.
	 *
	 * @param int $size
	 *
	 * @return bool
	 */
	public function SetSize($size)
	{
		if (empty($this->_mProp))
		{
			$this->_SetDefaults();
		}

		/**
		 * @var array
		 */
		$this->_mProp['size'] = $size;

		return TRUE;
	}

	/**
	 * Set default captcha settings.
	 *
	 * @return bool
	 */
	private function _SetDefaults()
	{
		/**
		 * @var array
		 */
		$defaults = Core::Config()->Get('captcha');

		/**
		 * @var array
		*/
		$this->_mProp['type'] = $defaults['type'];

		/**
		 * @var array
		 */
		$this->_mProp['width'] = $defaults['width'];

		/**
		 * @var array
		 */
		$this->_mProp['height'] = $defaults['height'];

		/**
		 * Set backgound color.
		 */
		$this->SetColor($defaults['background'], 'background');

		/**
		 * Set foreground color.
		*/
		$this->SetColor($defaults['foreground'], 'foreground');

		/**
		 * @var array
		*/
		$this->_mProp['transparent'] = (1 == $defaults['transparent']) ? TRUE : FALSE;

		/**
		 * @var array
		 */
		$this->_mProp['font'] = $defaults['font'];

		/**
		 * @var array
		 */
		$this->_mProp['size'] = $defaults['size'];

		return TRUE;
	}

	/**
	 * Get new captcha token.
	 *
	 * @return string.
	 */
	public function GetNewToken()
	{
		/**
		 * @var string
		 */
		$token = strtoupper(strrev(substr(sha1(uniqid(rand(), TRUE)), 17, 5)));

		if (!empty($_SESSION))
		{
			$_SESSION[__CLASS__]['token'] = $token;
		}

		return $token;
	}

	/**
	 * Get captcha token.
	 *
	 * @return string
	 */
	public function GetToken()
	{
		if (!empty($_SESSION[__CLASS__]['token']))
		{
			return $_SESSION[__CLASS__]['token'];
		}

		return $this->GetNewToken();
	}

	/**
	 * Get captcha image.
	 *
	 * @param bool $outputImage
	 *
	 * @return mixed
	 */
	public function GetImage($outputImage = TRUE)
	{
		if (empty($this->_mProp))
		{
			$this->_SetDefaults();
		}

		/**
		 * Turn on output buffering.
		 */
		ob_start();

		/**
		 * Set text from captcha.
		 *
		 * @var array
		*/
		$captcha = str_split($this->GetToken(), 1);

		/**
		 * Initialize image.
		 *
		 * @var resource
		*/
		$image = imagecreatetruecolor($this->_mProp['width'], $this->_mProp['height']);

		/**
		 * Set background color.
		 *
		 * @var resource
		*/
		$background = imagecolorallocate($image, $this->_mProp['background'][0], $this->_mProp['background'][1], $this->_mProp['background'][2]);

		/**
		 * Check if we have transparency enabled.
		*/
		if (FALSE !== $this->_mProp['transparent'])
		{
			/**
			 * Set transparent background.
			 */
			imagecolortransparent($image, $background);
		}

		/**
		 * Set foreground color.
		 *
		 * @var resource
		 */
		$foreground = imagecolorallocate($image, $this->_mProp['foreground'][0], $this->_mProp['foreground'][1], $this->_mProp['foreground'][2]);

		/**
		 * Fill image with background color.
		*/
		imagefill($image, 0, 0, $background);

		/**
		 * @var string
		*/
		$font_path = Core :: Config()->Get('path.www') . Core :: Config()->Get('dir.app.usr') . '/' .
				Core :: Config()->Get('dir.app.skins') . '/' . Core :: Config()->Get('output.skin') . '/fonts/' . $this->_mProp['font'];

		/**
		 * Write to image (1).
		 */
		imagettftext($image, $this->_mProp['size'], 10, 3, 16, $foreground, $font_path, $captcha['0']);

		/**
		 * Write to image (2).
		*/
		imagettftext($image, $this->_mProp['size'], -30, 13, 12, $foreground, $font_path, $captcha['1']);

		/**
		 * Write to image (3).
		*/
		imagettftext($image, $this->_mProp['size'], 10, 30, 16, $foreground, $font_path, $captcha['2']);

		/**
		 * Write to image (4).
		*/
		imagettftext($image, $this->_mProp['size'], -10, 40, 14, $foreground, $font_path, $captcha['3']);

		/**
		 * Write to image (5).
		*/
		imagettftext($image, $this->_mProp['size'], 10, 58, 16, $foreground, $font_path, $captcha['4']);

		/**
		 * Check image type
		*/
		switch ($this->_mProp['type'])
		{
			case 'png' :
			default :
				/**
				 * Output png image.
				 */
				imagepng($image);

				/**
				 * Set png image header.
				*/
				header("Content-type: image/png");

				break;
		}

		/**
		 * Set cache control header (HTTP/1.1).
		 */
		header("Cache-Control: no-cache, must-revalidate");

		/**
		 * Set expire header to a date in the past.
		*/
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

		/**
		 * Get image data.
		*/
		$data = ob_get_contents();

		/**
		 * Delete image from memory.
		*/
		imagedestroy($image);

		/**
		 * Erase the output buffer and turn off output buffering.
		*/
		ob_end_clean();

		return ($outputImage) ? $data : base64_encode($data);
	}
}
?>