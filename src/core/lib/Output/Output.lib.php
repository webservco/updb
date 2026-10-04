<?php
/**
 * $Id: Output.lib.php 168 2014-04-26 07:28:51Z radu $
 *
 * Main output library.
 *
 * All custom output libraries will extend this library.
 *
 * @author     Radu Maier <radu@webserv.co>
 * @copyright  Copyright (c) 2008 - 2014, Radu Maier
 * @license    http://www.opensource.org/licenses/bsd-license.php
 * @package    WEb
 * @subpackage WebOutput
 * @version    $Revision: 168 $
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
 * Output
 * Output library.
 * 
 * @author Radu Maier <radu@webserv.co>
 * @package WEb
 * @subpackage WebOutput
 */
class Output extends Library {

	/**
	 * Stores the output engine object.
	 * Can also be used to store initialization status (bool)
	 * 
	 * @var mixed
	 */
	protected $_mEngineObject;

	/**
	 * Stores skin name.
	 * 
	 * @var string
	 */
	protected $_mSkinDir = NULL;

	/**
	 * Stores skin directory path.
	 * 
	 * @var string
	 */
	protected $_mSkinPath;

	/**
	 * Stores template file name.
	 * 
	 * @var string
	 */
	protected $_mTemplate = NULL;

	/**
	 * Stores extra templates file names.
	 * 
	 * @var string
	 */
	protected $_mTemplatesExtra = array();

	/**
	 * Stores the template prefix
	 * 
	 * @var string
	 */
	protected $_mTemplatePrefix = NULL;

	/**
	 * Stores template file extension.
	 * 
	 * @var string
	 */
	protected $_mExtension = NULL;

	/**
	 * Stores output data.
	 * 
	 * @var array
	 */
	protected $_mData = array();

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
	 * Set skin.
	 * 
	 * @param string $skinName        	
	 *
	 * @return bool
	 */
	final public function SetSkin($skinName) {

		/**
		 *
		 * @var string
		 */
		$this->_mSkinDir = $skinName;
		
		/**
		 *
		 * @var string
		 */
		$msg_skin = sprintf(__('Output library: Skin directory set to "%s".', 'WEb'), $skinName);
		
		Core::Log()->Write($msg_skin, 'debug');
		
		return TRUE;
	}

	/**
	 * All in one output method.
	 * Sets data and calls the output engine render method.
	 * 
	 * @param bool $outDisplay        	
	 * @param string $outTemplate        	
	 * @param array $outData        	
	 *
	 * @return mixed
	 */
	final public function Out($outTemplate = NULL, $outData = NULL, $outDisplay = FALSE) {

		/**
		 * Initialize output engine object.
		 */
		$this->Init();
		
		/**
		 *
		 * @var string
		 */
		$out_template = $outTemplate ? $outTemplate : $this->_mTemplate;
		
		Core::Log()->Write(__('Output library: Calling Set() method.', 'WEb'), 'debug');
		
		/**
		 * Check if we actually have data.
		 */
		if (is_array($outData)) {
			foreach ($outData as $out_name => $out_value) {
				/**
				 * Set output data.
				 */
				$this->Set($out_name, $out_value);
			}
		}
		
		Core::Log()->Write(__('Output library: Calling output engine Render() method.', 'WEb'), 'debug');
		
		return $this->Render($out_template, $outDisplay);
	}

	/**
	 * Set output data.
	 * Can be overridden in extending class.
	 * 
	 * @param string $outName        	
	 * @param mixed $outValue        	
	 *
	 * @return bool
	 */
	public function Set($outName, $outValue) {

		/**
		 *
		 * @var array
		 */
		$this->_mData[$outName] = $outValue;
		
		Core::Log()->Write(__('Output library: Setting data.', 'WEb'), 'debug');
		
		return TRUE;
	}

	/**
	 * Get main template name.
	 * 
	 * @return string
	 */
	public function GetTemplate() {

		return $this->_mTemplatePrefix . $this->_mTemplate;
	}

	/**
	 * Get extra templates list.
	 * 
	 * @return string
	 */
	public function GetTemplatesExtra() {

		$templates_extra = array();
		
		foreach ($this->_mTemplatesExtra as $template) {
			$templates_extra[] = $this->_mTemplatePrefix . $template;
		}
		
		return $templates_extra;
	}

	/**
	 * Get secondary template name/path.
	 * 
	 * @param array $request        	
	 * @param string $customTemplate        	
	 *
	 * @return string
	 */
	public function GetSubTemplate($request = array(), $customTemplate = NULL) {

		if (!empty($request['action']) && !empty($request['module'])) {
			
			/**
			 *
			 * @var string
			 */
			$template_suffix = !empty($customTemplate) ? $customTemplate : $request['action'];
			
			return str_replace('Module_', '', $request['module']) . '/' . $this->_mTemplatePrefix . strtolower($template_suffix);
		
		} else {
			
			return !empty($customTemplate) ? $customTemplate : 'error';
		}
	}

	/**
	 * Get engine path.
	 * Returns a valid output engine path, or false if notfound.
	 * 
	 * @param string $engineName        	
	 *
	 * @return bool
	 */
	public function GetEnginePath($engineName) {

		/**
		 *
		 * @var array
		 */
		$engine_config = Core::Config()->Get('output.' . strtolower($engineName));
		
		if (empty($engine_config)) {
			/**
			 *
			 * @var string
			 */
			$err_msg_a = sprintf(__('Output Library: Error loading external output engine "%s".', 'WEb'), $engineName);
			
			/**
			 *
			 * @var string
			 */
			$err_msg_b = _('Configuration file not found.');
			
			Core::Log()->Write($err_msg_a . ' ' . $err_msg_b, 'erorr');
			
			return FALSE;
		}
		
		if (empty($engine_config['path'])) {
			/**
			 *
			 * @var array
			 */
			$engine_paths = array(
				'Core' => Core::Config()->Get('path.core') . Core::Config()->Get('dir.core.opt') . '/',
				'App' => Core::Config()->Get('path.app') . Core::Config()->Get('dir.app.opt') . '/'
			);
		} else {
			/**
			 *
			 * @var array
			 */
			$engine_paths = array(
				'External' => $engine_config['path']
			);
		}
		
		foreach ($engine_paths as $type => $path) {
			/**
			 *
			 * @var string
			 */
			$engine_path = $path . $engine_config['dir'] . '/' . $engine_config['file'];
			
			if (is_readable($engine_path)) {
				/**
				 *
				 * @var string
				 */
				$path_msg = sprintf(__('Output library: External output engine "%s" found in "%s" path.', 'WEb'), $engineName, $type);
				
				Core::Log()->Write($path_msg, 'debug');
				
				return $path . $engine_config['dir'] . '/';
			}
		}
		
		return FALSE;
	}

	/**
	 * Load external output engine.
	 * Can be overridden in extending class.
	 * 
	 * @param string $engineName        	
	 *
	 * @return object
	 */
	protected function _LoadEngine($engineName) {

		/**
		 *
		 * @var string
		 */
		$load_msg = sprintf(__('Output library: Loading external output engine "%s".', 'WEb'), $engineName);
		
		Core::Log()->Write($load_msg, 'debug');
		
		/**
		 *
		 * @var array
		 */
		$engine_config = Core::Config()->Get('output.' . strtolower($engineName));
		
		/**
		 *
		 * @var string
		 */
		$engine_path = $this->GetEnginePath($engineName);
		
		if (FALSE === $engine_path) {
			/**
			 *
			 * @var string
			 */
			$err_msg_a = sprintf(__('Output Library: Error loading external output engine "%s".', 'WEb'), $engineName);
			/**
			 *
			 * @var string
			 */
			$err_msg_b = _('File not found.');
			
			throw new ErrorException($err_msg_a . ' ' . $err_msg_b, 256);
			
			exit();
		}
		
		require_once $engine_path . $engine_config['file'];
		
		return new $engine_config['class']();
	}

}
?>
