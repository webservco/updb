<?php
/**
 * $Id$
 *
 * Admin controller.
 *
 * Module: Users
 *
 * @author     Radu Maier <radu@webserv.co>
 * @copyright  Copyright (c) 2008 - 2013, Radu Maier
 * @license    http://www.opensource.org/licenses/bsd-license.php
 * @package    UpdbApp
 * @subpackage UsersModule
 * @version    $Revision$
 * @since      Version 7
 * @access     public
 * @filesource
 */

if (!defined('_WEB')) {
	exit();
}

/**
 * Controller_Users_Admin
 * Admin Controller for Users module.
 * 
 * @author Radu Maier <radu@webserv.co>
 * @package UpdbApp
 * @subpackage UsersModule
 */
class Controller_Users_Admin extends ControllerApp {

	protected $_mTemplatePrefix = 'admin_';

	public function Users() {

		$this->Init();
		
		$data = $this->Module()->GetData($this->_mRequest);
		
		$data['strings']['title'] = __('Users');
		
		if (!$this->CheckAdminAccess($data)) {
			
			return FALSE;
		}
		
		/**
		 * Custom functionality below this line.
		 */
		
		$data['items'] = $this->Module()->Get();
		
		echo $this->GetOutput($data);
		
		return TRUE;
	}

	public function User() {

		$this->Init();
		
		$data = $this->Module()->GetData($this->_mRequest);
		
		if (!$this->CheckAdminAccess($data)) {
			
			return FALSE;
		}
		
		$data['strings']['title'] = __('User');
		
		$data['user_id'] = !empty($data['query']['id']) ? $data['query']['id'] : 0;
		
		/**
		 * Custom functionality below this line.
		 */
		
		$data['item'] = $this->Module()->Get($data['user_id']);
		
		echo $this->GetOutput($data);
		
		return TRUE;
	}
}
?>
