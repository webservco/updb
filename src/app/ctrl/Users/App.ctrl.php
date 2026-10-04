<?php
/**
 * $Id$
 *
 * App controller.
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
 * Controller_Users_App
 * App Controller for Users module.
 * 
 * @author Radu Maier <radu@webserv.co>
 * @package UpdbApp
 * @subpackage UsersModule
 */
class Controller_Users_App extends ControllerApp {

	/**
	 * Account.
	 * 
	 * @return mixed
	 */
	public function Account() {

		$this->Init();
		
		$data = $this->Module()->GetData($this->_mRequest);
		
		/**
		 * Custom functionality below this line.
		 */
		
		$data['strings']['title'] = __('Account');
		
		if (!empty($data['user']['Logged'])) {
			
			$template = 'account';
		
		} else {
			
			$data['strings']['title'] = __('Login');
			
			$data['form'] = $this->Module()->GetLoginForm($data['info']['url_next']);
			
			$template = 'login';
		}
		
		echo $this->GetOutput($data, $template);
		
		return TRUE;
	}

	public function Register() {

		$this->Init();
		
		$data = $this->Module()->GetData($this->_mRequest);
		
		/**
		 * Custom functionality below this line.
		 */
		
		$data['strings']['title'] = __('Register');
		
		if (!empty($data['user']['Logged'])) {
			
			$template = 'account';
		
		} else {
			
			$data['form'] = $this->Module()->GetRegisterForm();
			
			$template = 'register';
		}
		
		echo $this->GetOutput($data, $template);
		
		return TRUE;
	}

	public function ResetPassword() {

		$this->Init();
		
		$data = $this->Module()->GetData($this->_mRequest);
		
		/**
		 * Custom functionality below this line.
		 */
		
		$data['strings']['title'] = __('Reset password');
		
		if (!empty($data['user']['Logged'])) {
			
			$template = 'account';
		
		} else {
			
			$data['form'] = $this->Module()->GetResetPasswordForm();
			
			$template = 'reset_password';
		}
		
		echo $this->GetOutput($data, $template);
		
		return TRUE;
	}

	public function ChangePassword() {

		$this->Init();
		
		$data = $this->Module()->GetData($this->_mRequest);
		
		/**
		 * Custom functionality below this line.
		 */
		
		$data['strings']['title'] = __('Reset password');
		
		if (empty($data['user']['Logged'])) {
			
			$template = 'account';
		
		} else {
			
			$data['form'] = $this->Module()->GetChangePasswordForm($data['user']['Id'], $data['user']['Prop']['user_email']);
			
			$template = 'change_password';
		}
		
		echo $this->GetOutput($data, $template);
		
		return TRUE;
	}

	public function PasswordDone() {

		$this->Init();
		
		$data = $this->Module()->GetData($this->_mRequest);
		
		/**
		 * Custom functionality below this line.
		 */
		
		$data['strings']['title'] = __('Change password');
		
		echo $this->GetOutput($data, 'password_done');
		
		return TRUE;
	}

	/**
     * USER_EDIT_DISABLED Not currently used (no data to edit).)
     */
    public function Edit() {

		$this->Init();
		
		$data = $this->Module()->GetData($this->_mRequest);
		
		/**
		 * Custom functionality below this line.
		 */
		
		$data['strings']['title'] = __('Edit account');
		
		if (empty($data['user']['Logged'])) {
			
			$template = 'account';
		
		} else {
			
			$data['form'] = $this->Module()->GetEditForm($data['user']['Id'], $data['user']['Prop']);

			$template = 'edit';
		}
		
		echo $this->GetOutput($data, $template);
		
		return TRUE;
	}

	/**
	 * Login error.
	 * 
	 * @return mixed
	 */
	public function LoginError() {

		$this->Init();
		
		$data = $this->Module()->GetData($this->_mRequest);
		
		/**
		 * Custom functionality below this line.
		 */
		
		$data['strings']['title'] = __('Login error');
		
		$data['strings']['error_message'] = __('Login error') . ': ' . __('Please make sure your credentials are correct.');
		
		echo $this->GetOutput($data, 'login');
	}

	/**
	 * Logout.
	 * 
	 * @return mixed
	 */
	public function Logout() {

		/**
		 * No initialization or data needed.
		 */
		
		Core::User()->Logout();
		
		Core::Router()->Redirect('Users/App/LogoutDone');
		
		exit();
	}

	public function LogoutDone() {

		$this->Init();
		
		$data = $this->Module()->GetData($this->_mRequest);
		
		/**
		 * Custom functionality below this line.
		 */
		
		$data['strings']['message'] = __('You have been succesfully logged out.');
		
		echo $this->GetOutput($data, 'logout');
	}

	/**
	 * Display captcha image.
	 * 
	 * @return mixed
	 */
	public function Captcha() {

		/**
		 * Controller initializations needed so we have needed configs available for captcha lib.
		 */
		$this->Init();
		
		/**
		 * Custom functionality below this line.
		 */
		
		echo Core::Captcha()->GetImage();
		
		return TRUE;
	}
}
?>
