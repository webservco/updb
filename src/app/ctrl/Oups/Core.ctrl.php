<?php
/**
 * $Id$
 *
 * Core controller.
 *
 * Module: Oups
 *
 * @author     Radu Maier <radu@webserv.co>
 * @copyright  Copyright (c) 2008 - 2013, Radu Maier
 * @license    http://www.opensource.org/licenses/bsd-license.php
 * @package    UpdbApp
 * @subpackage OupsModule
 * @version    $Revision$
 * @since      Version 5
 * @access     public
 * @filesource
 */

if (!defined('_WEB')) {
	
	exit();
}

/**
 * Controller_Oups_Core
 * Core Controller for Oups module.
 * 
 * @author Radu Maier <radu@webserv.co>
 * @package UpdbApp
 * @subpackage OupsModule
 */
class Controller_Oups_Core extends ControllerApp {

	public function Oups404() {

		$this->Init();
		
		$data = $this->Module()->GetData($this->_mRequest);
		
		$data['strings']['title'] = __('Page not found');
		
		$data['strings']['message'] = __('Sorry!') . ' ' . __('Page not found') . '.';
		
		header(__('HTTP/1.1 404 Not Found'));
		
		echo $this->GetOutput($data, '_main/error');
		
		return TRUE;
	}
}
?>