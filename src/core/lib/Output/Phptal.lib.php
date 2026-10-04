<?php
/**
 * $Id: Phptal.lib.php 168 2014-04-26 07:28:51Z radu $
 *
 * PHPTAL output library.
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
 * Output_Phptal
 *
 * PHPTAL Output library.
 *
 * @author     Radu Maier <radu@webserv.co>
 * @package    WEb
 * @subpackage WebOutput
 *
 */
class Output_Phptal extends Output implements Interface_Output {
	/**
	 * Stores the PHPTAL translation object.
	 *
	 * @var mixed
	 */
	private $_mTranslator;

	/**
	 * (non-PHPdoc)
	 * @see Interface_Output::Init()
	 */
	public function Init() {
		/**
		 * Check if initialization is already done
		 */
		if (NULL != $this->_mEngineObject) {
			return FALSE;
		}

		Core::Log()->Write(__('Output library: Initializing output engine object.', 'WEb'), 'debug');

		/**
		 * @var string
		 */
		$this->_mSkinDir = Core::Config()->Get('output.skin');

		/**
		 * @var string
		 */
		$path_app = Core::Config()->Get('path.app');

		/**
		 * @var string
		 */
		$this->_mSkinPath = $path_app . Core::Config()->Get('dir.app.opt') . '/' . Core::Config()->Get('dir.app.tpl') . '/';

		/**
		 * @var string
		 */
		$this->_mTemplate = Core::Config()->Get('output.template');

		/**
		 * @var extension
		 */
		$this->_mExtension = Core::Config()->Get('output.extension');

		/**
		 * @var object
		 */
		$this->_mEngineObject = $this->_LoadEngine('PHPTAL');

		/**
		 * @var string
		 */
		$this->_mEngineObject->setTemplateRepository($this->_mSkinPath . $this->_mSkinDir);

		/**
		 * @var string
		 */
		$dir_tmp = Core::Config()->Get('dir.app.tmp');

		/**
		 * PHPTAL temp dir.
		 *
		 * @var string
		 */
		$this->_mEngineObject->setPhpCodeDestination($path_app . $dir_tmp . '/');

		if (FALSE !== Core::I18n()) {
			/**
			 * @var array
			 */
			$engine_path = $this->GetEnginePath('PHPTAL');

			require_once $engine_path . 'PHPTAL/GetTextTranslator.php';

			/**
			 * @var mixed
			 */
			$this->_mTranslator = new PHPTAL_GetTextTranslator();

			/**
			 * @var string
			 */
			$i18n_locale = Core::I18n()->GetLocale();

			/**
			 * @var string
			 */
			$i18n_domain = Core::I18n()->GetDomain();

			$this->_mTranslator->SetLanguage($i18n_locale);

			/**
			 * @var string
			 */
			$domain_path = Core::Config()->Get('path.app') . Core::Config()->Get('dir.app.i18n') . '/';

			$this->_mTranslator->addDomain('App', $domain_path);

			$this->_mTranslator->useDomain('App');
		}

		return TRUE;
	}

	/**
	 * (non-PHPdoc)
	 * @see Interface_Output::Parse()
	 */
	public function Render($outTemplate = NULL, $outDisplay = FALSE) {
		Core::Log()->Write(__('Output library: Rendering template.', 'WEb'), 'debug');

		/**
		 * Initialize output engine object.
		 */
		$this->Init();

		if (empty($outTemplate)) {

			/**
			 * @var string
			 */
			$outTemplate = $this->_mTemplate;
		}

		/**
		 * @var string
		 */
		$tpl_file = $outTemplate . $this->_mExtension;

		try {
			$this->_mEngineObject->setTemplate($tpl_file);

			if (!empty($this->_mTranslator)) {
				$this->_mEngineObject->setTranslator($this->_mTranslator);
			}

			if (FALSE !== $outDisplay) {
				try {
					return $this->_mEngineObject->echoExecute();
				} catch (PHPTAL_ConfigurationException $e) {
					throw new ErrorException($e->GetMessage(), 0, $e->GetCode(), $e->getFile(), $e->getLine());

					return FALSE;
				}
			} else {
				return $this->_mEngineObject->execute();
			}
		} catch (PHPTAL_IOException $e) {
			throw new ErrorException($e->GetMessage(), 0, $e->GetCode(), $e->getFile(), $e->getLine());

			return FALSE;
		} catch (PHPTAL_ParserException $e) {
			throw new ErrorException($e->GetMessage(), 0, $e->GetCode(), $e->getFile(), $e->getLine());

			return FALSE;
		}
	}

	/**
	 * Set output data.
	 *
	 * @param string $outName
	 * @param mixed $outValue
	 *
	 * @return bool
	 */
	public function Set($outName, $outValue) {
		Core::Log()->Write(__('Output library: Setting data.', 'WEb'), 'debug');

		return $this->_mEngineObject->set($outName, $outValue);
	}

}
?>