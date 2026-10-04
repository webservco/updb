<?php
if (!defined('_WEB')) {
	exit();
}

class Comments extends Library {

	/**
	 * App prefix.
	 * 
	 * @var string
	 */
	protected $_mAppPrefix = NULL;

	/**
	 * User property to use when displaying user name.
	 * 
	 * @var string
	 */
	protected $_mUserNameProperty = NULL;

	public function Init($appPrefix) {

		$this->_mAppPrefix = $appPrefix;
		
		$comments_settings = Core::Config()->Get("comments.settings");
		
		if (!empty($comments_settings['user_name_property'])) {
			
			$this->_mUserNameProperty = $comments_settings['user_name_property'];
		}
	}

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

	public function GetList($commentTypeId = NULL, $itemId = NULL) {

		$data = array();
		
		$q_comment_type_id = !empty($commentTypeId) ? " AND comment_type_id = '{$commentTypeId}'" : NULL;
		$q_item_id = !empty($itemId) ? " AND item_id = '{$itemId}'" : NULL;
		
		$q = "SELECT comment_id, user_id, comment_type_id, item_id, comment_date, comment_content
				FROM {$this->_mAppPrefix}comments
				WHERE 1
				{$q_comment_type_id}
				{$q_item_id}
				ORDER BY comment_date DESC";
		
		$rs = Core::Database()->Query($q);
		
		$t = $rs->num_rows;
		
		if (0 < $t) {
			
			while ($r = $rs->fetch_assoc()) {
				
				$data[$r['comment_id']] = $r;
				$data[$r['comment_id']]['user_name'] = Core::User()->GetProperty($r['user_id'], $this->_mUserNameProperty);
			}
		}
		
		return $data;
	}

	public function GetFields($commentId, $fields = array()) {

		$q = "SELECT " . implode(', ', $fields) . " FROM {$this->_mAppPrefix}comments
				WHERE comment_id = '{$commentId}' LIMIT 1";
		
		$rs = Core::Database()->Query($q);
		
		$t = $rs->num_rows;
		
		return (1 == $t) ? $rs->fetch_assoc() : FALSE;
	}

	public function Add($userId, $commentTypeId, $itemId = NULL, $data = array()) {

		if (!empty($data)) {
			
			$q_fields = implode(', ', array_keys($data));
			
			$q_data = "'" . implode("', '", $data) . "'";
			
			$q = "INSERT INTO {$this->_mAppPrefix}comments (user_id, comment_type_id, item_id, {$q_fields}) VALUES
			('{$userId}', '{$commentTypeId}', '{$itemId}', {$q_data})";
			
			return Core::Database()->Query($q) ? Core::Database()->GetInsertId() : FALSE;
		}
		
		return FALSE;
	}

	public function Modify($commentId, $data = array()) {

		if (!empty($data)) {
			
			foreach ($data as $key => $value) {
				
				$q_data[] = "{$key} = '{$value}'";
			}
			
			$q_data_string = implode(', ', $q_data);
			
			$q = "UPDATE {$this->_mAppPrefix}comments SET
			{$q_data_string}
			WHERE comment_id = '{$commentId}' LIMIT 1";
			
			return Core::Database()->Query($q) ? Core::Database()->GetAffectedRows() : FALSE;
		}
		
		return FALSE;
	}

	public function Delete($commentId) {

		$q = "DELETE FROM {$this->_mAppPrefix}comments WHERE comment_id = '{$commentId}' LIMIT 1";
		
		return Core::Database()->Query($q) ? Core::Database()->GetAffectedRows() : FALSE;
	}

	public function GetForm($formType, $userId, $commentTypeId, $itemId = NULL, $commentId = NULL) {

		Core::Form()->SetMethod('post');
		
		$form_config = Core::Config()->Get("comments.form.{$formType}");
		
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
				
				if (!empty($commentId)) {
					
					$this->Modify($commentId, $form['data']);
					
					Core::Router()->RemoveRequestQueryItem('comment');
				
				} else {
					
					$this->Add($userId, $commentTypeId, $itemId, $form['data']);
				}
				
				Core::Router()->Redirect();
				
				exit();
			
			} else {
				
				$form['data'] = Core::Form()->GetData();
				
				$form['errors'] = Core::Form()->GetErrors();
			}
		
		} else {
			
			$form['data'] = !empty($commentId) ? $this->GetFields($commentId, array_keys($form['meta'])) : Core::Form()->GetData();
		}
		
		return $form;
	}
}
?>
