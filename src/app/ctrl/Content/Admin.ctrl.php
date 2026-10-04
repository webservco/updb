<?php
/**
 * $Id$
 *
 * Admin controller.
 *
 * Module: Content
 *
 * @author     Radu Maier <radu@webserv.co>
 * @copyright  Copyright (c) 2008 - 2013, Radu Maier
 * @license    http://www.opensource.org/licenses/bsd-license.php
 * @package    UpdbApp
 * @subpackage ContentModule
 * @version    $Revision$
 * @since      Version 7
 * @access     public
 * @filesource
 */

if (!defined('_WEB')) {
	exit();
}

/**
 * Controller_Content_Admin
 * Admin Controller for Content module.
 * 
 * @author Radu Maier <radu@webserv.co>
 * @package UpdbApp
 * @subpackage ContentModule
 */
class Controller_Content_Admin extends ControllerApp {

	protected $_mTemplatePrefix = 'admin_';

	public function Home() {

		$this->Init();
		
		$data = $this->Module()->GetData($this->_mRequest);
		
		if (!$this->CheckAdminAccess($data)) {
			
			return FALSE;
		}
		
		/**
		 * Custom functionality below this line.
		 */
		
		$data['strings']['title'] = __('Home');
		
		echo $this->GetOutput($data);
		
		return TRUE;
	}
}
?>
