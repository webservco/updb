<?php
/**
 * $Id$
 *
 * Users module.
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
 * Module_Users
 * Model class for Users module.
 * 
 * @author Radu Maier <radu@webserv.co>
 * @package UpdbApp
 * @subpackage UsersModule
 */
class Module_Users extends ModuleApp {

	/**
	 * Handles login functionality.
	 * 
	 * @return mixed
	 */
	public function GetLoginForm($nextUrl = NULL) {

		Core::Form()->SetMethod('post');
		
		$form_config = Core::Config()->Get('users.form.login');
		
		if (empty($form_config)) {
			
			return FALSE;
		}
		
		Core::Form()->SetData($form_config);
		
		/**
		 *
		 * @var array
		 */
		$form['meta'] = Core::Form()->GetMeta();
		
		/**
		 *
		 * @var array
		 */
		$form['errors'] = array();
		
		if (!empty($_POST)) {
            // Honeytrap - brut
            if (!empty($_POST['updb_ht'])) {
                throw new ErrorException('SPAM');
            }

			/**
			 *
			 * @var bool
			 */
			$result_validation = Core::Form()->Validate();
			
			if (FALSE !== $result_validation) {
				/**
				 *
				 * @var array
				 */
				$form['data'] = Core::Form()->GetEscapedFilteredData();
				
				$form['data']['remember'] = 1;
				
				$result_login = Core::User()->Login($form['data']['email'], $form['data']['password'], (bool) $form['data']['remember']);
				
				if (TRUE !== $result_login) {
					Core::Captcha()->GetNewToken();
					
					Core::Router()->Redirect('Users/App/LoginError');
					
					exit();
				
				} else {
					/**
					 * perform any aditional user initialization.
					 */
					
					$redirect = !empty($nextUrl) ? $nextUrl : 'Users/App/Account';
					
					Core::Router()->Redirect($redirect);
					
					exit();
				}
			
			} else {
				Core::Captcha()->GetNewToken();
				
				/**
				 *
				 * @var array
				 */
				$form['data'] = Core::Form()->GetData();
				
				/**
				 *
				 * @var array
				 */
				$form['errors'] = Core::Form()->GetErrors();
			}
		} else {
			/**
			 *
			 * @var array
			 */
			$form['data'] = Core::Form()->GetData();
		}
		
		return $form;
	}

	public function GetRegisterForm() {

		Core::Form()->SetMethod('post');
		
		$form_config = Core::Config()->Get('users.form.register');
		
		if (empty($form_config)) {
			
			return FALSE;
		}
		
		Core::Form()->SetData($form_config);
		
		/*
		 * $countries = Core::Geo()->GetCountries(); if (!empty($countries)) { Core::Form()->SetMeta('country', 'options', $countries); }
		 */
		
		$form['meta'] = Core::Form()->GetMeta();
		
		$form['errors'] = array();
		
		if (!empty($_POST)) {

            // Honeytrap - brut
            if (!empty($_POST['updb_ht'])) {
                throw new ErrorException('SPAM');
            }
			
			$result_validation = Core::Form()->Validate();
			
			if (FALSE !== $result_validation) {
				
				$form['data'] = Core::Form()->GetEscapedFilteredData();
				
				$email_exists = Core::Database()->ValueExists("{$this->_mAppPrefix}acl_users", 'user_email', $form['data']['email']);
				
				$nickname_exists = Core::Database()->ValueExists("{$this->_mAppPrefix}acl_users", 'user_nickname', $form['data']['nickname']);
				
				if ($email_exists) {
					
					$form['errors']['email'][] = sprintf(Core::Config()->Get('form.error.alreadyregistered.text'), $form['meta']['email']['title']);
				}
				
				if ($nickname_exists) {
					
					$form['errors']['nickname'][] = sprintf(Core::Config()->Get('form.error.alreadyregistered.text'), $form['meta']['nickname']['title']);
				}
				
				if ($form['data']['password'] != $form['data']['password_confirm']) {
					
					$form['errors']['password_confirm'][] = Core::Config()->Get('form.error.passworddifferent.text');
				}
				
				if (empty($form['errors'])) {
					
					$user_id = $this->RegisterUser($form['data']);
					
					Core::Router()->Redirect('Users/App/Account');
					
					exit();
				
				} else {
					
					$form['data'] = Core::Form()->GetData();
				}
			} else {
				
				$form['data'] = Core::Form()->GetData();
				
				$form['errors'] = Core::Form()->GetErrors();
			}
		} else {
			
			$form['data'] = Core::Form()->GetData();
		}
		
		return $form;
	}

	public function RegisterUser($formData) {

		$user_salt = Core::Security()->GetSalt($formData['email']);
		
		$user_auth = Core::Security()->GetToken($user_salt, $formData['password']);
		
		$q = "INSERT INTO {$this->_mAppPrefix}acl_users (user_email, user_nickname, user_auth, owner_id, user_date_add) VALUES
		('{$formData['email']}', '{$formData['nickname']}', '{$user_auth}', 1, NOW())";
		
		Core::Database()->Query($q);
		
		$user_id = Core::Database()->GetInsertId();
		
		$this->InsertCustomData($user_id, $formData);
		
		if (empty($user_id)) {
			
			/**
			 * Registration error.
			 */
			
			echo 'Registration error, please try again later.';
			
			exit();
		}
		
		$result_login = Core::User()->Login($formData['email'], $formData['password']);
		
		if (TRUE !== $result_login) {
			/**
			 * Autologin error.
			 */
			
			echo 'Autologin error, please try again later.';
			
			exit();
		}
		
		return $user_id;
	}

	public function InsertCustomData($userId, $formData = array()) {

		$data = array(
			'user_id_key' => $userId
		);
		
		unset($formData['email']);
		unset($formData['nickname']);
		unset($formData['password']);
		unset($formData['password_confirm']);
		unset($formData['send']);
		
		if (!empty($formData)) {
			
			foreach ($formData as $key => $value) {
				
				$data["user_{$key}"] = $value;
			}
			
			$qc = "INSERT INTO {$this->_mAppPrefix}acl_users_custom (";
			$qc .= implode(', ', array_keys($data));
			$qc .= ") VALUES (";
			$qc .= "'" . implode("', '", array_values($data)) . "'";
			$qc .= ")";
			
			return Core::Database()->Query($qc) ? TRUE : FALSE;
		
		}
		
		return FALSE;
	}

	public function ModifyCustomData($userId, $formData = array()) {

		if (!empty($formData)) {
			
			foreach ($formData as $key => $value) {
				
				$data["user_{$key}"] = $value;
			}
			
			$qc = "UPDATE {$this->_mAppPrefix}acl_users_custom SET ";
			
			foreach ($data as $key => $value) {
				
				$qm[] = "`{$key}` = '{$value}'";
			}
			
			$qc .= implode(', ', $qm);
			$qc .= " WHERE user_id_key = '{$userId}' LIMIT 1";
			
			return Core::Database()->Query($qc) ? TRUE : FALSE;
		}
		
		return FALSE;
	}

	public function GetResetPasswordForm() {

		Core::Form()->SetMethod('post');
		
		$form_config = Core::Config()->Get('users.form.resetpassword');
		
		if (empty($form_config)) {
			
			return FALSE;
		}
		
		Core::Form()->SetData($form_config);
		
		$form['meta'] = Core::Form()->GetMeta();
		
		$form['errors'] = array();
		
		if (!empty($_POST)) {
			
			$result_validation = Core::Form()->Validate();
			
			if (FALSE !== $result_validation) {
				
				$form['data'] = Core::Form()->GetEscapedFilteredData();
				
				$email_exists = Core::Database()->ValueExists("{$this->_mAppPrefix}acl_users", 'user_email', $form['data']['email']);
				
				if (!$email_exists) {
					
					$form['errors']['email'][] = sprintf(Core::Config()->Get('form.error.notregistered.text'), $form['meta']['email']['title']);
				}
				
				if (empty($form['errors'])) {
					
					$new_pass = rand(11111, 32767);
					
					$user_salt = Core::Security()->GetSalt($form['data']['email']);
					
					$user_auth = Core::Security()->GetToken($user_salt, $new_pass);
					
					$q = "UPDATE {$this->_mAppPrefix}acl_users SET user_auth = '{$user_auth}' WHERE user_email = '{$form['data']['email']}' LIMIT 1";
					
					Core::Database()->Query($q);
					
					$app_name = __(Core::Config()->Get('app.title'), 'App');
					
					$mail_subject = "{$app_name}: Password reset";
					
					$mail_message = "{$app_name} password reset.<br />You new password is <b>{$new_pass}</b>";
					
					Core::Mail()->SendSimple($form['data']['email'], $mail_subject, $mail_message);
					
					Core::Router()->Redirect('Users/App/PasswordDone');
					
					exit();
				
				} else {
					
					$form['data'] = Core::Form()->GetData();
				}
			} else {
				
				$form['data'] = Core::Form()->GetData();
				
				$form['errors'] = Core::Form()->GetErrors();
			}
		} else {
			
			$form['data'] = Core::Form()->GetData();
		}
		
		return $form;
	}

	public function GetChangePasswordForm($userId, $userEmail) {

		Core::Form()->SetMethod('post');
		
		$form_config = Core::Config()->Get('users.form.changepassword');
		
		if (empty($form_config)) {
			
			return FALSE;
		}
		
		Core::Form()->SetData($form_config);
		
		$form['meta'] = Core::Form()->GetMeta();
		
		$form['errors'] = array();
		
		if (!empty($_POST)) {
			
			$result_validation = Core::Form()->Validate();
			
			if (FALSE !== $result_validation) {
				
				$form['data'] = Core::Form()->GetEscapedFilteredData();
				
				$verify_password_old = $this->VerifyPassword($userEmail, $form['data']['password_old']);
				
				if (!$verify_password_old) {
					
					$form['errors']['password_old'][] = Core::Config()->Get('form.error.passwordwrong.text');
				}
				
				if ($form['data']['password_old'] == $form['data']['password_new']) {
					$form['errors']['password_new'][] = Core::Config()->Get('form.error.passwordsame.text');
				}
				
				if (empty($form['errors'])) {
					
					$user_salt = Core::Security()->GetSalt($userEmail);
					
					$user_auth = Core::Security()->GetToken($user_salt, $form['data']['password_new']);
					
					$q = "UPDATE {$this->_mAppPrefix}acl_users SET user_auth = '{$user_auth}' WHERE user_email = '{$userEmail}' LIMIT 1";
					
					Core::Database()->Query($q);
					
					$app_name = __(Core::Config()->Get('app.title'), 'App');
					
					$mail_subject = "{$app_name}: Password changed";
					
					$password_new = stripslashes($form['data']['password_new']);
					
					$mail_message = "{$app_name} password changed.<br />You new password is <b>{$password_new}</b>";
					
					Core::Mail()->SendSimple($userEmail, $mail_subject, $mail_message);
					
					Core::Router()->Redirect('Users/App/PasswordDone');
					
					exit();
				
				} else {
					
					$form['data'] = Core::Form()->GetData();
				}
			} else {
				
				$form['data'] = Core::Form()->GetData();
				
				$form['errors'] = Core::Form()->GetErrors();
			}
		} else {
			
			$form['data'] = Core::Form()->GetData();
		}
		
		return $form;
	}

	/**
	 * Verify if the input password is correct.
	 * 
	 * @param string $userEmail        	
	 * @param string $password        	
	 *
	 * @return boolean
	 */
	public function VerifyPassword($userEmail, $password) {

		$user_salt = Core::Security()->GetSalt($userEmail);
		
		$user_auth_verify = Core::Security()->GetToken($user_salt, $password);
		
		$q = "SELECT user_auth FROM {$this->_mAppPrefix}acl_users WHERE user_email = '{$userEmail}' LIMIT 1";
		
		$rs = Core::Database()->Query($q);
		
		$t = $rs->num_rows;
		
		if (1 == $t) {
			
			$r = $rs->fetch_assoc();
			
			$user_auth_db = $r['user_auth'];
		}
		
		if (!empty($user_auth_verify) && !empty($user_auth_db)) {
			
			return $user_auth_verify == $user_auth_db;
		}
		
		return FALSE;
	}

    /**
     * USER_EDIT_DISABLED Not currently used (no data to edit).)
     */
    public function GetEditForm($userId, $userProp) {

		Core::Form()->SetMethod('post');
		
		$form_config = Core::Config()->Get("users.form.register");

		unset($form_config['nickname']);
		unset($form_config['email']);
		unset($form_config['password']);
		unset($form_config['password_confirm']);
		unset($form_config['send']);
		
		if (empty($form_config)) {
			
			return FALSE;
		}
		
		Core::Form()->SetData($form_config);
		
		/* No idea why this is here.
        $countries = Core::Geo()->GetCountries();
		
		if (!empty($countries)) {
			
			Core::Form()->SetMeta('country', 'options', $countries);
		}
		*/
		
		$form['meta'] = Core::Form()->GetMeta();
		
		$form['errors'] = array();
		
		if (!empty($_POST)) {
			
			$result_validation = Core::Form()->Validate();
			
			if (FALSE !== $result_validation) {
				
				$form['data'] = Core::Form()->GetEscapedFilteredData();
				
				if (empty($form['errors'])) {
					
					$this->ModifyCustomData($userId, $form['data']);
					
					Core::User()->Reload();
					
					Core::Router()->Redirect('Users/App/Account');
					
					exit();
				
				} else {
					
					$form['data'] = Core::Form()->GetData();
				}
			} else {
				
				$form['data'] = Core::Form()->GetData();
				
				$form['errors'] = Core::Form()->GetErrors();
			}
		} else {
			
			$form['data'] = Core::Form()->GetData();
			
			/**
			 * Set existing data.
			 */
			foreach ($form['data'] as $field_id => $data) {
				
				if (isset($userProp["user_{$field_id}"])) {
					
					$form['data'][$field_id] = $userProp["user_{$field_id}"];
				}
			}
		}
		
		return $form;
	}

	public function Get($userId = NULL, $i18nLocale = NULL) {

		$data = array();
		
		$i18n_locale = !empty($i18nLocale) ? $i18nLocale : Core::I18n()->GetLocale();
		
		$q_conditions = NULL;
		
		$q_limit = NULL;
		
		if (!empty($userId)) {
			
			$q_conditions .= "AND user_id = '{$userId}'";
			
			$q_limit = "LIMIT 1";
		}
		
		$q = "SELECT *
		FROM {$this->_mAppPrefix}acl_object_user
		WHERE 1
		{$q_conditions}
		ORDER BY user_name_last, user_name_first, user_nickname
		{$q_limit}";
		
		$rs = Core::Database()->Query($q);
		
		$t = $rs->num_rows;
		
		if (0 < $t) {
			
			if ((1 == $t) && !empty($userId)) {
				
				return $rs->fetch_assoc();
			}
			
			while ($r = $rs->fetch_assoc()) {
				
				$data[$r['user_id']] = $r;
			}
		}
		
		return $data;
	}
}
?>
