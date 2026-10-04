<?php
/**
 * $Id: Error.lib.php 219 2016-05-18 14:42:52Z radu $
 *
 * Error handling and reporting library.
 *
 * @author     Radu Maier <radu@webserv.co>
 * @copyright  Copyright (c) 2008 - 2014, Radu Maier
 * @license    http://www.opensource.org/licenses/bsd-license.php
 * @package    WEb
 * @subpackage WebError
 * @version    $Revision: 219 $
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
 * Error
 * Error handling library.
 *
 * @author Radu Maier <radu@webserv.co>
 * @package WEb
 * @subpackage WebError
 */
class ErrorHandler extends Library {

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
	 * Register as the error handler.
	 *
	 * @return boolean
	 */
	public function Load() {

		set_error_handler(array(
				ErrorHandler::GetInstance(),
				'Handle'
		));
		
		Core::Log()->Write(__('Error library: Registering as error handler.', 'WEb'), 'debug');
		
		return TRUE;
	}

	/**
	 * Restore error handler.
	 *
	 * @return boolean
	 */
	public function Unload() {

		restore_error_handler();
		
		Core::Log()->Write(__('Error library: Restoring default error handler.', 'WEb'), 'debug');
		
		return TRUE;
	}

	/**
	 * Handle errors.
	 *
	 * @param int $errCode        	
	 * @param string $errMessage        	
	 * @param string $errFile        	
	 * @param int $errLine        	
	 * @throws ErrorException
	 */
	public function Handle($errCode, $errMessage, $errFile, $errLine) {

		throw new ErrorException($errMessage, 0, $errCode, $errFile, $errLine);
	}

	/**
	 * Report errors.
	 *
	 * @param mixed $errObject        	
	 */
	public function Report($errObject) {

		/**
		 *
		 * @var array
		 */
		$err = array(
				'code' => $errObject->GetCode(),
				'severity' => $errObject instanceof ErrorException ? $errObject->GetSeverity() : NULL,
				'message' => $errObject->GetMessage(),
				'file' => $errObject->GetFile(),
				'line' => $errObject->GetLine(),
				'trace' => $errObject->GetTraceAsString()
		);
		
		/**
		 *
		 * @var array
		 */
		$msg = array();
		
		/**
		 *
		 * @var array
		 */
		$msg[] = array(
				__('The application made a boo boo:', 'WEb'),
				'info'
		);
		
		/**
		 *
		 * @var array
		 */
		$msg[] = array(
				$err['message'],
				'error'
		);
		
		/**
		 * Severity 512 is reserved for sensitive operations,
		 * so we show no information apart from the actual message.
		 */
		if (512 != $err['severity']) {
			/**
			 *
			 * @var array
			 */
			$msg[] = array(
					sprintf(__('Code: %s', 'WEb'), $err['code']),
					'debug'
			);
			
			/**
			 *
			 * @var array
			 */
			$msg[] = array(
					sprintf(__('Severity: %s', 'WEb'), $err['severity']),
					'debug'
			);
			
			/**
			 *
			 * @var array
			 */
			$msg[] = array(
					sprintf(__('File: %s', 'WEb'), $err['file']),
					'error'
			);
			
			/**
			 *
			 * @var array
			 */
			$msg[] = array(
					sprintf(__('Line: %s', 'WEb'), $err['line']),
					'error'
			);
			
			/**
			 * Check if trace is enabled.
			 */
			if (1 == Core::Config()->Get('error.trace')) {
				/**
				 *
				 * @var array
				 */
				$msg[] = array(
						__('Trace:', 'WEb') . PHP_EOL . $err['trace'],
						'debug'
				);
			}
		}
		
		/**
		 *
		 * @var array
		 */
		$msg[] = array(
				'Execution stopped.',
				'info'
		);
		
		/**
		 * Iterate messages
		 */
		foreach ($msg as $msg_data) {
			/**
			 * Write messages to log.
			 */
			Core::Log()->Write($msg_data[0], $msg_data[1]);
		}
		
		/**
		 * Display friendly error message in browser.
		 */
		return $this->_ShowErrorMessage();
	}

	/**
	 * Show error message in browser.
	 */
	private function _ShowErrorMessage() {

		if (Core::IsCli()) {
			
			echo "500 Internal Server Error";
			
			exit();
		}
		
		header('HTTP/1.1 500 Internal Server Error');
		
		$custom_message = Core::Config()->Get('error.message');
		
		if (! empty($custom_message)) {
			
			echo $custom_message;
		} else {
			
			echo '<!doctype html><html><head><meta http-equiv="content-type" content="text/html; charset=UTF-8"><title>' . __('Error', 'WEb') . '</title></head><body><h1>' . __('The application made a boo boo.', 'WEb') . '</h1><p>' . __('But there is no reason to panic.', 'WEb') . ' ' . __('The person responsible has been notified.', 'WEb') . '</p><p>' . __('Please come back later.', 'WEb') . '</p><hr><address>' . sprintf(__('The friendly server at %s.', 'WEb'), $_SERVER['SERVER_NAME']) . '</address></body></html>';
		}
		
		exit();
	}
}
?>