<?php
/**
 * $Id: Graphics.lib.php 213 2016-03-04 16:09:07Z radu $
 *
 * Graphics library.
 *
 * @author     Radu Maier <radu@webserv.co>
 * @copyright  Copyright (c) 2008 - 2014, Radu Maier
 * @license    http://www.opensource.org/licenses/bsd-license.php
 * @package    WEb
 * @subpackage WebGraphics
 * @version    $Revision: 213 $
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
 * Graphics
 * Graphics library.
 *
 * @author Radu Maier <radu@webserv.co>
 * @package WEb
 * @subpackage WebGraphics
 */
class Graphics extends Library {
	
	/**
	 * Library properties.
	 *
	 * @var array
	 */
	private $_mProp = array();

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

	public function SetImageDir($imageDir) {

		$this->_mProp['image_dir'] = $imageDir;
	}

	public function SetImageName($imageName) {

		$this->_mProp['image_name'] = $imageName;
	}

	public function SetThumbnailDir($thumbnailDir) {

		$this->_mProp['thumbnail_dir'] = $thumbnailDir;
	}

	public function SetThumbnailName($thumbnailName) {

		$this->_mProp['thumbnail_name'] = $thumbnailName;
	}

	public function SetThumbnailConfigType($thumbnailConfigType) {

		$this->_mProp['thumbnail_config_type'] = $thumbnailConfigType;
	}

	public function GetThumbnail($imageDir, $imageName, $thumbnailDir = NULL, $thumbnailName = NULL, $thumbnailConfigType = 'default', $writeFile = TRUE) {

		$this->SetImageDir($imageDir);
		
		$this->SetImageName($imageName);
		
		$this->SetThumbnailDir($thumbnailDir);
		
		$this->SetThumbnailName($thumbnailName);
		
		$this->SetThumbnailConfigType($thumbnailConfigType);
		
		return $this->CreateThumbnail($writeFile);
	}

	public function CreateThumbnail($writeFile = TRUE, $size = NULL, $type = NULL, $fixOrientation = NULL) {

		$this->_SetThumbnailSettings($size, $type, $fixOrientation);
		
		$image_path = "{$this->_mProp['image_dir']}{$this->_mProp['image_name']}";
		
		if (! is_readable($image_path)) {
			
			throw new ErrorException(__('Graphics library: Source image not found.', 'WEb'));
		}
		
		ob_start();
		
		list ($image_width, $image_height, $image_type) = getimagesize($image_path);
		
		switch ($image_type) {
			
			case 1 :
				
				$raw = imagecreatefromgif($image_path);
				
				break;
			
			case 2 :
				
				$raw = imagecreatefromjpeg($image_path);
				
				if (! empty($this->_mProp['fix_orientation'])) {
					
					if (function_exists('exif_read_data')) {
						
						$exif_data = exif_read_data($image_path);
						
						if (! empty($exif_data)) {
							
							if (! empty($exif_data['Orientation'])) {
								
								$rotate = NULL;
								
								switch ($exif_data['Orientation']) {
									
									case 3 :
										$rotate = 180;
										break;
									
									case 6 :
										$rotate = - 90;
										$image_height_new = $image_width;
										$image_width_new = $image_height;
										break;
									
									case 8 :
										$rotate = 90;
										$image_height_new = $image_width;
										$image_width_new = $image_height;
										break;
									
									default :
										break;
								}
								
								if (! empty($rotate)) {
									
									Core::Log()->Write(sprintf(__('Graphics Library: [fix_orientation] rotating by %s degrees.', 'WEb'), $rotate), 'debug');
									
									$raw = imagerotate($raw, $rotate, 0);
									
									if (! empty($image_width_new) && ! empty($image_height_new)) {
										
										$image_width = $image_width_new;
										$image_height = $image_height_new;
									}
								} else {
									
									Core::Log()->Write(__('Graphics Library: [fix_orientation] no rotation needed.', 'WEb'), 'debug');
								}
							} else {
								
								Core::Log()->Write(__('Graphics Library: [fix_orientation] no EXIF Orientation data found.', 'WEb'), 'debug');
							}
						} else {
							
							Core::Log()->Write(__('Graphics Library: [fix_orientation] no EXIF data found.', 'WEb'), 'debug');
						}
					} else {
						
						Core::Log()->Write(__('Graphics Library: [fix_orientation] EXIF library not available.', 'WEb'), 'debug');
					}
				}
				
				break;
			
			case 3 :
				
				$raw = imagecreatefrompng($image_path);
				
				break;
			
			default :
				
				throw new ErrorException(__('Graphics library: Image type not supported.', 'WEb'));
				
				break;
		}
		
		$this->_SetThumbnailDimmensions($image_width, $image_height);
		
		$new = imagecreatetruecolor($this->_mProp['thumbnail_width'], $this->_mProp['thumbnail_height']);
		
		imagecopyresampled($new, $raw, 0, 0, 0, 0, $this->_mProp['thumbnail_width'], $this->_mProp['thumbnail_height'], $image_width, $image_height);
		
		switch ($this->_mProp['thumbnail_type']) {
			
			case 1 :
				
				imagegif($new);
				$extension = '.gif';
				
				break;
			
			case 2 :
				
				imagejpeg($new, NULL, 90);
				$extension = '.jpg';
				
				break;
			
			case 3 :
			default :
				
				imagepng($new, NULL, 6);
				$extension = '.png';
				
				break;
		}
		
		$imageData = ob_get_contents();
		
		ob_end_clean();
		
		imagedestroy($new);
		
		if (empty($writeFile)) {
			
			return $imageData;
		}
		
		$thumbnail_name = ! empty($this->_mProp['thumbnail_name']) ? $this->_mProp['thumbnail_name'] : pathinfo($image_path, PATHINFO_FILENAME);
		
		$thumbnail_dir = ! empty($this->_mProp['thumbnail_dir']) ? $this->_mProp['thumbnail_dir'] : $this->_mProp['image_dir'];
		
		$thumbnail_file_name = "{$thumbnail_name}{$extension}";
		
		file_put_contents("{$thumbnail_dir}{$thumbnail_file_name}", $imageData);
		
		return $thumbnail_file_name;
	}

	private function _SetThumbnailSettings($size, $type, $fixOrientation) {

		$thumbnailConfigType = ! empty($this->_mProp['thumbnail_config_type']) ? $this->_mProp['thumbnail_config_type'] : 'default';
		
		$settings = Core::Config()->Get("graphics.thumbnail.{$thumbnailConfigType}");
		
		$this->_mProp['thumbnail_size'] = ! empty($settings['size']) ? $settings['size'] : 96;
		
		$this->_mProp['thumbnail_type'] = ! empty($settings['type']) ? $settings['type'] : 2;
		
		$this->_mProp['fix_orientation'] = ! empty($settings['fix_orientation']) ? TRUE : FALSE;
		
		if (! empty($size)) {
			$this->_mProp['thumbnail_size'] = $size;
		}
		
		if (! empty($type)) {
			$this->_mProp['thumbnail_type'] = $type;
		}
		
		if (isset($fixOrientation)) {
			$this->_mProp['fix_orientation'] = $fixOrientation;
		}
		
		if (empty($this->_mProp['image_dir'])) {
			
			throw new ErrorException(__('Graphics library: Image directory is required.', 'WEb'));
		}
		
		if (empty($this->_mProp['image_name'])) {
			
			throw new ErrorException(__('Graphics library: Image name is required.', 'WEb'));
		}
	}

	private function _SetThumbnailDimmensions($imageWidth, $imageHeight) {

		/**
		 * Check source dimensions - is landscape?
		 */
		if ($imageWidth > $imageHeight) {
			/**
			 * Set thumbnail width.
			 */
			$this->_mProp['thumbnail_width'] = $this->_mProp['thumbnail_size'];
			/**
			 * Set thumbnail height.
			 */
			$this->_mProp['thumbnail_height'] = ($imageHeight * $this->_mProp['thumbnail_size']) / $imageWidth;
		} /**
		 * Check source dimensions - is portrait?
		 */
		else if ($imageWidth < $imageHeight) {
			/**
			 * Set thumbnail width.
			 */
			$this->_mProp['thumbnail_width'] = ($imageWidth * $this->_mProp['thumbnail_size']) / $imageHeight;
			/**
			 * Set thumbnail height.
			 */
			$this->_mProp['thumbnail_height'] = $this->_mProp['thumbnail_size'];
		} /**
		 * Check source dimensions - is square?
		 */
		else if ($imageWidth == $imageHeight) {
			/**
			 * Set thumbnail width.
			 */
			$this->_mProp['thumbnail_width'] = $this->_mProp['thumbnail_size'];
			/**
			 * Set thumbnail height.
			 */
			$this->_mProp['thumbnail_height'] = $this->_mProp['thumbnail_size'];
		}
		
		return TRUE;
	}
}
?>