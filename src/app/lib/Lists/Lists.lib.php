<?php
if (!defined('_WEB')) {
	exit();
}

class Lists extends Library {

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

	public function GetTypes($i18nLocale = NULL) {

		$data = array();
		
		$i18n_locale = !empty($i18nLocale) ? $i18nLocale : Core::I18n()->GetLocale();
		
		$q = "SELECT M.list_type_id, D.list_type_title
				FROM up_list_types_main AS M
				LEFT JOIN up_list_types_details AS D ON (D.list_type_id = M.list_type_id AND D.i18n_locale = '{$i18n_locale}')
				ORDER BY D.list_type_title";
		
		$rs = Core::Database()->Query($q);
		
		$t = $rs->num_rows;
		
		if (0 < $t) {
			
			while ($r = $rs->fetch_assoc()) {
				
				$data[$r['list_type_id']] = $r['list_type_title'];
			}
		}
		
		return $data;
	}

	public function GetTypeTitle($listTypeId, $i18nLocale = NULL) {

		$i18n_locale = !empty($i18nLocale) ? $i18nLocale : Core::I18n()->GetLocale();
		
		$q = "SELECT list_type_title FROM up_list_types_details WHERE list_type_id = '{$listTypeId}' AND i18n_locale = '{$i18n_locale}' LIMIT 1";
		
		$rs = Core::Database()->Query($q);
		
		$t = $rs->num_rows;
		
		if (1 == $t) {
			
			$r = $rs->fetch_assoc();
			
			return $r['list_type_title'];
		}
		
		return FALSE;
	}

	public function GetListTypeTitle($listId, $i18nLocale = NULL) {

		$i18n_locale = !empty($i18nLocale) ? $i18nLocale : Core::I18n()->GetLocale();
		
		$q = "SELECT T.list_type_title
				FROM up_lists AS L
				LEFT JOIN up_list_types_details AS T ON (T.list_type_id = L.list_type_id AND T.i18n_locale = '{$i18n_locale}')
				WHERE L.list_id = '{$listId}' LIMIT 1";
		
		$rs = Core::Database()->Query($q);
		
		$t = $rs->num_rows;
		
		if (1 == $t) {
			
			$r = $rs->fetch_assoc();
			
			return $r['list_type_title'];
		}
		
		return FALSE;
	}

	public function GetLists($userId = NULL, $listType = NULL, $i18nLocale = NULL) {

		$data = array();
		
		$i18n_locale = !empty($i18nLocale) ? $i18nLocale : Core::I18n()->GetLocale();
		
		$q_user = !empty($userId) ? " AND L.user_id = '{$userId}'" : NULL;
		$q_type = !empty($listType) ? " AND L.list_type_id = '{$listType}'" : NULL;
		
		$q = "SELECT L.list_id, L.user_id, U.user_nickname AS user_name, L.list_type_id, T.list_type_title
				, L.list_title, L.list_notes, L.list_public, L.list_date
				, COUNT(I.list_id) AS total_items
				FROM up_lists AS L
				LEFT JOIN up_acl_users AS U ON (U.user_id = L.user_id)
				LEFT JOIN up_list_types_details AS T ON (T.list_type_id = L.list_type_id AND T.i18n_locale = '{$i18n_locale}')
				LEFT JOIN up_list_items AS I ON (I.list_id = L.list_id)
				WHERE 1
				{$q_user}
				{$q_type}
				GROUP BY L.list_id
				ORDER BY L.list_type_id, L.list_title";
		
		$rs = Core::Database()->Query($q);
		
		$t = $rs->num_rows;
		
		if (0 < $t) {
			
			while ($r = $rs->fetch_assoc()) {
				
				$data[$r['list_id']] = $r;
			}
		}
		
		return $data;
	}

	public function GetListUsers($listType = NULL, $i18nLocale = NULL) {

		$data = array();
		
		$i18n_locale = !empty($i18nLocale) ? $i18nLocale : Core::I18n()->GetLocale();
		
		$q_type = !empty($listType) ? " AND L.list_type_id = '{$listType}'" : NULL;
		
		$q = "SELECT L.user_id, U.user_nickname AS user_name, COUNT(L.list_id) AS total_items
				FROM up_lists AS L
				LEFT JOIN up_acl_users AS U ON (U.user_id = L.user_id)
				WHERE 1
				{$q_type}
				GROUP BY L.user_id
				ORDER BY U.user_nickname";
		
		$rs = Core::Database()->Query($q);
		
		$t = $rs->num_rows;
		
		if (0 < $t) {
			
			while ($r = $rs->fetch_assoc()) {
				
				$data[$r['user_id']] = $r;
			}
		}
		
		return $data;
	}

	public function Get($listId, $i18nLocale = NULL) {

		$i18n_locale = !empty($i18nLocale) ? $i18nLocale : Core::I18n()->GetLocale();
		
		$q = "SELECT L.list_id, L.user_id, U.user_nickname AS user_name, L.list_type_id, T.list_type_title
				, L.list_title, L.list_notes, L.list_public, L.list_date
				FROM up_lists AS L
				LEFT JOIN up_acl_users AS U ON (U.user_id = L.user_id)
				LEFT JOIN up_list_types_details AS T ON (T.list_type_id = L.list_type_id AND T.i18n_locale = '{$i18n_locale}')
				WHERE list_id = '{$listId}' LIMIT 1";
		
		$rs = Core::Database()->Query($q);
		
		$t = $rs->num_rows;
		
		if (1 == $t) {
			
			$list = $rs->fetch_assoc();
			
			$list['items'] = $this->GetItems($listId);
			
			return $list;
		}
	}

	public function GetTitle($listId) {

		$q = "SELECT list_title FROM up_lists WHERE list_id = '{$listId}' LIMIT 1";
		
		$rs = Core::Database()->Query($q);
		
		$t = $rs->num_rows;
		
		if (1 == $t) {
			
			$r = $rs->fetch_assoc();
			
			return $r['list_title'];
		}
		
		return FALSE;
	}

	public function GetUser($listId) {

		$q = "SELECT user_id FROM up_lists WHERE list_id = '{$listId}' LIMIT 1";
		
		$rs = Core::Database()->Query($q);
		
		$t = $rs->num_rows;
		
		if (1 == $t) {
			
			$r = $rs->fetch_assoc();
			
			return $r['user_id'];
		}
		
		return FALSE;
	}

	public function Add($userId, $listTypeId, $listTitle, $listNotes = NULL, $listPublic = TRUE) {

		$q = "INSERT INTO up_lists (user_id, list_type_id, list_title, list_notes, list_public) VALUES
		('{$userId}', '{$listTypeId}', '{$listTitle}', '{$listNotes}', '{$listPublic}')";
		
		return Core::Database()->Query($q) ? Core::Database()->GetInsertId() : FALSE;
	}

	public function Modify($listId, $listTitle, $listNotes = NULL, $listPublic = TRUE) {

		$q = "UPDATE up_lists SET
				list_title = '{$listTitle}'
				, list_notes = '{$listNotes}'
				, list_public = '{$listPublic}'
				WHERE list_id = '{$listId}' LIMIT 1";
		
		return Core::Database()->Query($q) ? Core::Database()->GetAffectedRows() : FALSE;
	}

	public function Delete($listId) {

		$affected_rows = 0;
		
		$q = "DELETE FROM up_lists WHERE list_id = '{$listId}'";
		
		if (Core::Database()->Query($q)) {
			
			$affected_rows += Core::Database()->GetAffectedRows();
		}
		
		$affected_rows += $this->DeleteItems($listId);
		
		return $affected_rows;
	}

	public function GetItems($listId) {

		$data = array();
		
		$q = "SELECT I.list_id, I.item_id, I.item_notes, I.item_date
		,CONCAT(P.pub_issue_title, ' (', P.pub_issue_year, ')') AS item_title
		FROM up_list_items AS I
		LEFT JOIN up_pub_issues_main AS P ON (P.pub_issue_id = I.item_id)
		WHERE I.list_id = '{$listId}'
		ORDER BY P.pub_issue_year, P.pub_issue_title";
		
		$rs = Core::Database()->Query($q);
		
		$t = $rs->num_rows;
		
		if (0 < $t) {
			
			while ($r = $rs->fetch_assoc()) {
				
				$data[$r['item_id']] = $r;
			}
		}
		
		return $data;
	}

	public function GetItemTitle($itemId) {

		$q = "SELECT CONCAT(P.pub_issue_title, ' (', P.pub_issue_year, ')') AS item_title 
				FROM up_pub_issues_main AS P WHERE P.pub_issue_id = '{$itemId}' LIMIT 1";
		
		$rs = Core::Database()->Query($q);
		
		$t = $rs->num_rows;
		
		if (1 == $t) {
			
			$r = $rs->fetch_assoc();
			
			return $r['item_title'];
		}
		
		return FALSE;
	}

	public function GetItem($listId, $itemId) {

		$q = "SELECT item_notes FROM up_list_items WHERE list_id = '{$listId}' AND item_id = '{$itemId}' LIMIT 1";
		
		$rs = Core::Database()->Query($q);
		
		$t = $rs->num_rows;
		
		return (1 == $t) ? $rs->fetch_assoc() : FALSE;
	}

	public function AddItem($listId, $itemId, $itemNotes) {

		$q = "INSERT INTO up_list_items (list_id, item_id, item_notes) VALUES
				('{$listId}', '{$itemId}', '{$itemNotes}')";
		
		return Core::Database()->Query($q) ? Core::Database()->GetAffectedRows() : FALSE;
	}

	public function ModifyItem($listId, $itemId, $itemNotes) {

		$q = "UPDATE up_list_items SET
				item_notes = '{$itemNotes}'
				WHERE list_id = '{$listId}' AND item_id = '{$itemId}'";
		
		return Core::Database()->Query($q) ? Core::Database()->GetAffectedRows() : FALSE;
	}

	public function DeleteItem($listId, $itemId) {

		$q = "DELETE FROM up_list_items WHERE list_id = '{$listId}' AND item_id = '{$itemId}' LIMIT 1";
		
		return Core::Database()->Query($q) ? Core::Database()->GetAffectedRows() : FALSE;
	}

	public function DeleteItems($listId) {

		$q = "DELETE FROM up_list_items WHERE list_id = '{$listId}'";
		
		return Core::Database()->Query($q) ? Core::Database()->GetAffectedRows() : FALSE;
	}

	public function GetItemLists($itemId, $i18nLocale = NULL) {

		$data = array();
		
		$i18n_locale = !empty($i18nLocale) ? $i18nLocale : Core::I18n()->GetLocale();
		
		$q = "SELECT L.list_id, L.user_id, U.user_nickname AS user_name, L.list_title, T.list_type_title, I.item_notes
				FROM up_list_items AS I
				LEFT JOIN up_lists AS L ON (L.list_id = I.list_id)
				LEFT JOIN up_acl_users AS U ON (U.user_id = L.user_id)
				LEFT JOIN up_list_types_details AS T ON (T.list_type_id = L.list_type_id AND T.i18n_locale = '{$i18n_locale}')
				WHERE I.item_id = '{$itemId}'
				ORDER BY user_name";
		
		$rs = Core::Database()->Query($q);
		
		$t = $rs->num_rows;
		
		if (0 < $t) {
			
			while ($r = $rs->fetch_assoc()) {
				
				$data[$r['list_id']] = $r;
			}
		}
		
		return $data;
	
	}

	public function GetUserLists($userId, $itemId, $i18nLocale = NULL) {

		$data = array();
		
		$i18n_locale = !empty($i18nLocale) ? $i18nLocale : Core::I18n()->GetLocale();
		
		$q = "SELECT L.list_id, L.list_title, T.list_type_title
		, IF(I.item_id IS NOT NULL, '1', '0') AS item_in_list
		FROM up_lists AS L
		LEFT JOIN up_list_items AS I ON (I.list_id = L.list_id AND I.item_id = '{$itemId}')
		LEFT JOIN up_list_types_details AS T ON (T.list_type_id = L.list_type_id AND T.i18n_locale = '{$i18n_locale}')
		WHERE L.user_id = '{$userId}'
		ORDER BY L.list_title";
		
		$rs = Core::Database()->Query($q);
		
		$t = $rs->num_rows;
		
		if (0 < $t) {
			
			while ($r = $rs->fetch_assoc()) {
				
				$data[$r['list_id']] = $r;
			}
		}
		
		return $data;
	}

	public function GetListStats($i18nLocale = NULL) {

		$stats = array();
		
		$stats['types'] = $this->GetListTypeStats($i18nLocale);
		
		return $stats;
	}

	public function GetListTypeStats($i18nLocale = NULL) {

		$data = array();
		
		$i18n_locale = !empty($i18nLocale) ? $i18nLocale : Core::I18n()->GetLocale();
		
		$q = "SELECT L.list_type_id AS id, T.list_type_title AS title, COUNT(L.list_type_id) AS total
				FROM up_lists AS L
				LEFT JOIN up_list_types_details AS T ON (T.list_type_id = L.list_type_id AND T.i18n_locale = '{$i18n_locale}')
				GROUP BY id
				ORDER BY title";
		
		$rs = Core::Database()->Query($q);
		
		$t = $rs->num_rows;
		
		if (0 < $t) {
			
			while ($r = $rs->fetch_assoc()) {
				
				$data[$r['id']] = $r;
			}
		}
		
		return $data;
	}
}
?>
