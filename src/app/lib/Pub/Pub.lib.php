<?php
if (!defined('_WEB')) {
	exit();
}

class Pub extends Library {

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

	public function GetPubTypes($i18nLocale = NULL) {

		$data = array();
		
		$i18n_locale = !empty($i18nLocale) ? $i18nLocale : Core::I18n()->GetLocale();
		
		$q = "SELECT M.pub_type_id, D.pub_type_title
				FROM up_pub_types_main AS M
				LEFT JOIN up_pub_types_details AS D ON (D.pub_type_id = M.pub_type_id AND D.i18n_locale = '{$i18n_locale}')
				ORDER BY D.pub_type_title";
		
		$rs = Core::Database()->Query($q);
		
		$t = $rs->num_rows;
		
		if (0 < $t) {
			
			while ($r = $rs->fetch_assoc()) {
				
				$data[$r['pub_type_id']] = $r['pub_type_title'];
			}
		}
		
		return $data;
	}

	public function GetPubLayoutTypes($i18nLocale = NULL) {

		$data = array();
		
		$i18n_locale = !empty($i18nLocale) ? $i18nLocale : Core::I18n()->GetLocale();
		
		$q = "SELECT M.pub_layout_type_id, D.pub_layout_type_title
		FROM up_pub_layout_types_main AS M
		LEFT JOIN up_pub_layout_types_details AS D ON
		(D.pub_layout_type_id = M.pub_layout_type_id AND D.i18n_locale = '{$i18n_locale}')
		ORDER BY D.pub_layout_type_title";
		
		$rs = Core::Database()->Query($q);
		
		$t = $rs->num_rows;
		
		if (0 < $t) {
			
			while ($r = $rs->fetch_assoc()) {
				
				$data[$r['pub_layout_type_id']] = $r['pub_layout_type_title'];
			}
		}
		
		return $data;
	}

	public function GetPubDuplicationTypes($i18nLocale = NULL) {

		$data = array();
		
		$i18n_locale = !empty($i18nLocale) ? $i18nLocale : Core::I18n()->GetLocale();
		
		$q = "SELECT M.pub_duplication_type_id, D.pub_duplication_type_title
		FROM up_pub_duplication_types_main AS M
		LEFT JOIN up_pub_duplication_types_details AS D ON
		(D.pub_duplication_type_id = M.pub_duplication_type_id AND D.i18n_locale = '{$i18n_locale}')
		ORDER BY D.pub_duplication_type_title";
		
		$rs = Core::Database()->Query($q);
		
		$t = $rs->num_rows;
		
		if (0 < $t) {
			
			while ($r = $rs->fetch_assoc()) {
				
				$data[$r['pub_duplication_type_id']] = $r['pub_duplication_type_title'];
			}
		}
		
		return $data;
	}

	public function GetPubContentTypes($i18nLocale = NULL) {

		$data = array();
		
		$i18n_locale = !empty($i18nLocale) ? $i18nLocale : Core::I18n()->GetLocale();
		
		$q = "SELECT M.pub_content_type_id, M.contains_data, D.pub_content_type_title
				FROM up_pub_content_types_main AS M
				LEFT JOIN up_pub_content_types_details AS D ON 
				(D.pub_content_type_id = M.pub_content_type_id AND D.i18n_locale = '{$i18n_locale}')
				ORDER BY D.pub_content_type_title";
		
		$rs = Core::Database()->Query($q);
		
		$t = $rs->num_rows;
		
		if (0 < $t) {
			
			while ($r = $rs->fetch_assoc()) {
				
				$data[$r['pub_content_type_id']] = $r;
			}
		}
		
		return $data;
	}

	public function GetPubFormats() {

		$data = array();
		
		$q = "SELECT M.pub_format_id, M.pub_format_name
				FROM up_pub_formats_main AS M
				ORDER BY M.pub_format_id";
		
		$rs = Core::Database()->Query($q);
		
		$t = $rs->num_rows;
		
		if (0 < $t) {
			
			while ($r = $rs->fetch_assoc()) {
				
				$data[$r['pub_format_id']] = $r['pub_format_name'];
			}
		}
		
		return $data;
	}

	public function GetPublications($i18nLocale = NULL, $pubType = NULL) {

		$data = array();
		
		$i18n_locale = !empty($i18nLocale) ? $i18nLocale : Core::I18n()->GetLocale();
		
		$q = "SELECT M.pub_id, M.pub_title, T.pub_type_title, C.country_name 
				FROM up_pub_main AS M
				LEFT JOIN up_pub_types_details AS T ON (T.pub_type_id = M.pub_type_id AND T.i18n_locale = '{$i18n_locale}')
				LEFT JOIN up_geo_countries AS C ON (C.country_code = M.country_code)
				WHERE 1
				ORDER BY M.pub_title_sort";
		
		$rs = Core::Database()->Query($q);
		
		$t = $rs->num_rows;
		
		if (0 < $t) {
			
			while ($r = $rs->fetch_assoc()) {
				
				$data[$r['pub_id']] = $r;
				
				$data[$r['pub_id']]['thumbnail'] = $this->GetPubThumbnail($r['pub_id'], 'pub');
				
				$data[$r['pub_id']]['issues_summary'] = $this->GetPubIssuesSummary($r['pub_id']);
			}
		}
		
		return $data;
	}

	public function GetPublication($pubId, $i18nLocale = NULL) {

		$i18n_locale = !empty($i18nLocale) ? $i18nLocale : Core::I18n()->GetLocale();
		
		$q = "SELECT M.pub_id, M.pub_title, T.pub_type_title, C.country_name, D.pub_description
		FROM up_pub_main AS M
		LEFT JOIN up_pub_details AS D ON (D.pub_id = M.pub_id AND D.i18n_locale = '{$i18n_locale}')
		LEFT JOIN up_pub_types_details AS T ON (T.pub_type_id = M.pub_type_id AND T.i18n_locale = '{$i18n_locale}')
		LEFT JOIN up_geo_countries AS C ON (C.country_code = M.country_code)
		WHERE M.pub_id = '{$pubId}'
		LIMIT 1";
		
		$rs = Core::Database()->Query($q);
		
		$t = $rs->num_rows;
		
		if (1 == $t) {
			
			$pub = $rs->fetch_assoc();
			
			/**
			 * add issue count
			 */
			
			return $pub;
		}
		
		return FALSE;
	}

	public function GetPubMain($pubId) {

		$q = "SELECT pub_title, pub_title_sort, pub_type_id, country_code
				FROM up_pub_main
				WHERE pub_id = '{$pubId}' LIMIT 1";
		
		$rs = Core::Database()->Query($q);
		
		$t = $rs->num_rows;
		
		if (1 == $t) {
			
			return $rs->fetch_assoc();
		}
		
		return FALSE;
	}

	public function GetPubDetails($pubId) {

		$data = array();
		
		$q = "SELECT i18n_locale, pub_description FROM up_pub_details WHERE pub_id = '{$pubId}'";
		
		$rs = Core::Database()->Query($q);
		
		$t = $rs->num_rows;
		
		if (0 < $t) {
			
			while ($r = $rs->fetch_assoc()) {
				
				$data[$r['i18n_locale']] = $r;
			}
		}
		
		return $data;
	}

	public function GetPubIssuesSummary($pubId) {

		$q = "SELECT COUNT(J.pub_issue_id) AS total, MIN(I.pub_issue_year) AS start, MAX(I.pub_issue_year) AS end
				FROM up_pub_issues AS J
				LEFT JOIN up_pub_issues_main AS I ON (I.pub_issue_id = J.pub_issue_id)
				WHERE J.pub_id = '{$pubId}'";
		
		$rs = Core::Database()->Query($q);
		
		$t = $rs->num_rows;
		
		return (1 == $t) ? $rs->fetch_assoc() : FALSE;
	
	}

	public function GetPubIssues($pubId, $i18nLocale = NULL) {

		$data = array();
		
		$i18n_locale = !empty($i18nLocale) ? $i18nLocale : Core::I18n()->GetLocale();
		
		$q = "SELECT M.pub_issue_id, P.pub_title, F.pub_format_name, D.pub_duplication_type_title, L.pub_layout_type_title,
		M.pub_issue_title, M.pub_issue_year, M.pub_issue_pages
		FROM
		up_pub_issues AS J
		LEFT JOIN up_pub_issues_main AS M ON (M.pub_issue_id = J.pub_issue_id)
		LEFT JOIN up_pub_main AS P ON (P.pub_id = J.pub_id)
		LEFT JOIN up_pub_formats_main AS F ON (F.pub_format_id = M.pub_format_id)
		LEFT JOIN up_pub_duplication_types_details AS D ON (D.pub_duplication_type_id = M.pub_duplication_type_id AND D.i18n_locale = '{$i18n_locale}')
		LEFT JOIN up_pub_layout_types_details AS L ON (L.pub_layout_type_id = M.pub_layout_type_id AND L.i18n_locale = '{$i18n_locale}')
		WHERE 1
		AND J.pub_id = '{$pubId}'
		ORDER BY M.pub_issue_year, M.pub_issue_title";
		
		$rs = Core::Database()->Query($q);
		
		$t = $rs->num_rows;
		
		if (0 < $t) {
			
			while ($r = $rs->fetch_assoc()) {
				
				$data[$r['pub_issue_id']] = $r;
				
				$data[$r['pub_issue_id']]['thumbnail'] = $this->GetPubThumbnail($r['pub_issue_id'], 'pub_issue');
			}
		}
		
		return $data;
	}

	public function GetLatestPubIssues($i18nLocale = NULL) {

		$data = array();
		
		$i18n_locale = !empty($i18nLocale) ? $i18nLocale : Core::I18n()->GetLocale();
		
		$q = "SELECT M.pub_issue_id, F.pub_format_name, D.pub_duplication_type_title, L.pub_layout_type_title,
				M.pub_issue_title, M.pub_issue_year, M.pub_issue_pages
				FROM up_pub_issues_main AS M
				LEFT JOIN up_pub_formats_main AS F ON (F.pub_format_id = M.pub_format_id)
				LEFT JOIN up_pub_duplication_types_details AS D ON (D.pub_duplication_type_id = M.pub_duplication_type_id AND D.i18n_locale = '{$i18n_locale}')
				LEFT JOIN up_pub_layout_types_details AS L ON (L.pub_layout_type_id = M.pub_layout_type_id AND L.i18n_locale = '{$i18n_locale}')
				WHERE 1
				ORDER BY M.pub_issue_updated DESC LIMIT 10";
		
		$rs = Core::Database()->Query($q);
		
		$t = $rs->num_rows;
		
		if (0 < $t) {
			
			while ($r = $rs->fetch_assoc()) {
				
				$data[$r['pub_issue_id']] = $r;
				
				$data[$r['pub_issue_id']]['thumbnail'] = $this->GetPubThumbnail($r['pub_issue_id'], 'pub_issue');
			}
		}
		
		return $data;
	}

	public function GetPubList($pubIssueId = NULL) {

		$data = array();
		
		if (!empty($pubIssueId)) {
			$q = "SELECT J.pub_id, P.pub_title
			FROM up_pub_issues AS J
			LEFT JOIN up_pub_main AS P ON (P.pub_id = J.pub_id)
			WHERE J.pub_issue_id = '{$pubIssueId}'
			ORDER BY P.pub_title";
		
		} else {
			
			$q = "SELECT P.pub_id, P.pub_title
			FROM up_pub_main AS P
			WHERE 1
			ORDER BY P.pub_title";
		}
		
		$rs = Core::Database()->Query($q);
		
		$t = $rs->num_rows;
		
		if (0 < $t) {
			
			while ($r = $rs->fetch_assoc()) {
				
				$data[$r['pub_id']] = $r['pub_title'];
			
			}
		}
		
		return $data;
	}

	public function GetPubIssue($pubIssueId, $i18nLocale = NULL) {

		$i18n_locale = !empty($i18nLocale) ? $i18nLocale : Core::I18n()->GetLocale();
		
		$q = "SELECT M.pub_issue_id, F.pub_format_name, DT.pub_duplication_type_title, L.pub_layout_type_title,
		M.pub_issue_title, M.pub_issue_year, M.pub_issue_pages
		, D.pub_issue_description
		FROM up_pub_issues_main AS M
		LEFT JOIN up_pub_issues_details AS D ON (D.pub_issue_id = M.pub_issue_id AND D.i18n_locale = '{$i18n_locale}')
		LEFT JOIN up_pub_formats_main AS F ON (F.pub_format_id = M.pub_format_id)
		LEFT JOIN up_pub_duplication_types_details AS DT ON (DT.pub_duplication_type_id = M.pub_duplication_type_id AND DT.i18n_locale = '{$i18n_locale}')
		LEFT JOIN up_pub_layout_types_details AS L ON (L.pub_layout_type_id = M.pub_layout_type_id AND L.i18n_locale = '{$i18n_locale}')
		WHERE  M.pub_issue_id = '{$pubIssueId}'
		LIMIT 1";
		
		$rs = Core::Database()->Query($q);
		
		$t = $rs->num_rows;
		
		if (0 < $t) {
			
			$issue = $rs->fetch_assoc();
			
			$issue['pub_list'] = $this->GetPubList($issue['pub_issue_id']);
			
			$issue['pub_languages'] = $this->GetPubIssueLanguages($issue['pub_issue_id']);
			
			$issue['content_data'] = array();
			
			$issue['content_values'] = array();
			
			$pub_content_types = $this->GetPubContentTypes();
			
			foreach ($pub_content_types as $pub_content_type) {
				
				if (!empty($pub_content_type['contains_data'])) {
					
					$issue['content_data'][$pub_content_type['pub_content_type_title']] = $this->GetPubIssueContentData($pubIssueId, $pub_content_type['pub_content_type_id']);
				
				} else {
					
					$content_value = $this->GetPubIssueContentValue($pubIssueId, $pub_content_type['pub_content_type_id']);
					
					if (!empty($content_value)) {
						
						$issue['content_values'][] = $pub_content_type['pub_content_type_title'];
					}
				}
			}
			
			return $issue;
		}
		
		return FALSE;
	}

	public function GetPubIssueMain($pubIssueId) {

		$q = "SELECT pub_format_id, pub_layout_type_id, pub_duplication_type_id, pub_issue_title, pub_issue_year, pub_issue_pages
		FROM up_pub_issues_main
		WHERE pub_issue_id = '{$pubIssueId}' LIMIT 1";
		
		$rs = Core::Database()->Query($q);
		
		$t = $rs->num_rows;
		
		if (1 == $t) {
			
			return $rs->fetch_assoc();
		}
		
		return FALSE;
	}

	public function GetPubIssueDetails($pubIssueId) {

		$data = array();
		
		$q = "SELECT i18n_locale, pub_issue_description FROM up_pub_issues_details WHERE pub_issue_id = '{$pubIssueId}'";
		
		$rs = Core::Database()->Query($q);
		
		$t = $rs->num_rows;
		
		if (0 < $t) {
			
			while ($r = $rs->fetch_assoc()) {
				
				$data[$r['i18n_locale']] = $r;
			}
		}
		
		return $data;
	}

	public function GetPubIssueContentValue($pubIssueId, $pubContentTypeId) {

		$q = "SELECT pub_issue_content_value 
				FROM up_pub_issues_content
				WHERE pub_issue_id = '{$pubIssueId}' AND pub_content_type_id = '{$pubContentTypeId}'
				LIMIT 1";
		
		$rs = Core::Database()->Query($q);
		
		$t = $rs->num_rows;
		
		if (1 == $t) {
			
			$r = $rs->fetch_assoc();
			
			return $r['pub_issue_content_value'];
		}
		
		return FALSE;
	
	}

	public function GetPubIssueContentData($pubIssueId, $pubContentTypeId) {

		$data = array();
		
		$q = "SELECT pub_issue_content_id, pub_issue_content_data
				FROM up_pub_issues_content
				WHERE pub_issue_id = '{$pubIssueId}' AND pub_content_type_id = '{$pubContentTypeId}'";
		
		$rs = Core::Database()->Query($q);
		
		$t = $rs->num_rows;
		
		if (0 < $t) {
			
			while ($r = $rs->fetch_assoc()) {
				
				$data[$r['pub_issue_content_id']] = $r['pub_issue_content_data'];
			}
		}
		
		return $data;
	}

	public function GetPubIssueLanguages($pubIssueId) {

		$data = array();
		
		$q = "SELECT P.language_code, L.language_name
		 		FROM up_pub_issues_languages AS P
				LEFT JOIN up_geo_languages AS L ON (L.language_code = P.language_code)
				WHERE P. pub_issue_id = '{$pubIssueId}'
				ORDER BY L.language_name";
		
		$rs = Core::Database()->Query($q);
		
		$t = $rs->num_rows;
		
		if (0 < $t) {
			
			while ($r = $rs->fetch_assoc()) {
				
				$data[$r['language_code']] = $r['language_name'];
			}
		}
		
		return $data;
	}

	public function DeletePubIssueLanguages($pubIssueId) {

		$q = "DELETE FROM up_pub_issues_languages WHERE pub_issue_id = '{$pubIssueId}'";
		
		return Core::Database()->Query($q) ? Core::Database()->GetAffectedRows() : FALSE;
	}

	public function AddModifyPubIssueLanguages($pubIssueId, $languagesList) {

		$affected_rows = 0;
		
		if (!empty($languagesList)) {
			
			foreach ($languagesList as $language_code) {
				
				$q = "INSERT IGNORE INTO up_pub_issues_languages (pub_issue_id, language_code) VALUES
				('{$pubIssueId}', '{$language_code}')";
				
				if (Core::Database()->Query($q)) {
					
					$affected_rows += Core::Database()->GetAffectedRows();
				}
			}
		}
		
		return $affected_rows;
	}

	public function AddPubMain($data) {

		$q = "INSERT INTO up_pub_main (pub_title, pub_title_sort, pub_type_id, country_code) VALUES
				('{$data['pub_title']}', '{$data['pub_title_sort']}', '{$data['pub_type_id']}', '{$data['country_code']}')";
		
		return Core::Database()->Query($q) ? Core::Database()->GetInsertId() : FALSE;
	}

	public function ModifyPubMain($pubId, $data) {

		$q = "UPDATE up_pub_main SET
				pub_title = '{$data['pub_title']}',
				pub_title_sort = '{$data['pub_title_sort']}',
				pub_type_id = '{$data['pub_type_id']}',
				country_code = '{$data['country_code']}'
				WHERE pub_id = '{$pubId}' LIMIT 1";
		
		return Core::Database()->Query($q) ? Core::Database()->GetAffectedRows() : FALSE;
	}

	public function AddModifyPubDetails($pubId, $i18nLocale, $pubDescription) {

		$q = "INSERT INTO up_pub_details (pub_id, i18n_locale, pub_description) VALUES
				('{$pubId}', '{$i18nLocale}', '{$pubDescription}')
				ON DUPLICATE KEY UPDATE pub_description = '{$pubDescription}'";
		
		return Core::Database()->Query($q) ? Core::Database()->GetAffectedRows() : FALSE;
	}

	public function AddPubIssueMain($data) {

		$q = "INSERT INTO up_pub_issues_main (pub_format_id, pub_layout_type_id, pub_duplication_type_id, pub_issue_title, pub_issue_year, pub_issue_pages) VALUES
				('{$data['pub_format_id']}', '{$data['pub_layout_type_id']}', '{$data['pub_duplication_type_id']}', '{$data['pub_issue_title']}', '{$data['pub_issue_year']}', '{$data['pub_issue_pages']}')";
		
		return Core::Database()->Query($q) ? Core::Database()->GetInsertId() : FALSE;
	}

	public function DeletePubIssuePubs($pubIssueId) {

		$q = "DELETE FROM up_pub_issues WHERE pub_issue_id = '{$pubIssueId}'";
		
		return Core::Database()->Query($q) ? Core::Database()->GetAffectedRows() : FALSE;
	}

	public function AddModifyPubIssuePubs($pubIssueId, $pubList = array()) {

		$affected_rows = 0;
		
		if (!empty($pubList)) {
			
			foreach ($pubList as $pub_id) {
				
				$q = "INSERT IGNORE INTO up_pub_issues (pub_id, pub_issue_id) VALUES
				('{$pub_id}', '{$pubIssueId}')";
				
				if (Core::Database()->Query($q)) {
					
					$affected_rows += Core::Database()->GetAffectedRows();
				}
			}
		}
		
		return $affected_rows;
	}

	public function ModifyPubIssueMain($pubIssueId, $data) {

		$q = "UPDATE up_pub_issues_main SET
				pub_format_id = '{$data['pub_format_id']}',
				pub_layout_type_id = '{$data['pub_layout_type_id']}',
				pub_duplication_type_id = '{$data['pub_duplication_type_id']}',
				pub_issue_title = '{$data['pub_issue_title']}',
				pub_issue_year = '{$data['pub_issue_year']}',
				pub_issue_pages = '{$data['pub_issue_pages']}'
				WHERE pub_issue_id = '{$pubIssueId}' LIMIT 1";
		
		return Core::Database()->Query($q) ? Core::Database()->GetAffectedRows() : FALSE;
	}

	public function AddModifyPubIssueDetails($pubIssueId, $i18nLocale, $pubIssueDescription) {

		$q = "INSERT INTO up_pub_issues_details (pub_issue_id, i18n_locale, pub_issue_description) VALUES
				('{$pubIssueId}', '{$i18nLocale}', '{$pubIssueDescription}')
				ON DUPLICATE KEY UPDATE pub_issue_description = '{$pubIssueDescription}'";
		
		return Core::Database()->Query($q) ? Core::Database()->GetAffectedRows() : FALSE;
	}

	public function DeletePubIssueContent($pubIssueId, $pubContentTypeId) {

		$q = "DELETE FROM up_pub_issues_content WHERE pub_issue_id = '{$pubIssueId}' AND pub_content_type_id = '{$pubContentTypeId}'";
		
		return Core::Database()->Query($q) ? Core::Database()->GetAffectedRows() : FALSE;
	}

	public function AddPubIssueContentValue($pubIssueId, $pubContentTypeId, $pubIssueContentValue) {

		$q = "INSERT INTO up_pub_issues_content (pub_issue_id, pub_content_type_id, pub_issue_content_value)
			VALUES ('{$pubIssueId}', '{$pubContentTypeId}', '{$pubIssueContentValue}')";
		
		return Core::Database()->Query($q) ? Core::Database()->GetAffectedRows() : FALSE;
	}

	public function AddPubIssueContentData($pubIssueId, $pubContentTypeId, $pubIssueContentData) {

		$affected_rows = 0;
		
		if (is_array($pubIssueContentData)) {
			
			foreach ($pubIssueContentData as $data) {
				
				if (!empty($data)) {
					
					$q = "INSERT INTO up_pub_issues_content (pub_issue_id, pub_content_type_id, pub_issue_content_data)
					VALUES ('{$pubIssueId}', '{$pubContentTypeId}', '{$data}')";
					
					Core::Database()->Query($q);
					
					$affected_rows += Core::Database()->GetAffectedRows();
				}
			}
		} else {
			
			if (empty($pubIssueContentData)) {
				
				return $affected_rows;
			}
			
			$pub_issue_content_data = array_map('trim', explode(',', $pubIssueContentData));
			
			return $this->AddPubIssueContentData($pubIssueId, $pubContentTypeId, $pub_issue_content_data);
		}
		
		return $affected_rows;
	}

	public function GetPubImages($itemId, $itemType) {

		$data = array();
		
		$q = "SELECT image_id, image_file, image_thumb, image_order FROM up_pub_images
			WHERE item_id = '{$itemId}' AND item_type = '{$itemType}'
			ORDER BY image_order";
		
		$rs = Core::Database()->Query($q);
		
		$t = $rs->num_rows;
		
		if (0 < $t) {
			
			while ($r = $rs->fetch_assoc()) {
				
				$data[$r['image_id']] = $r;
			}
		}
		
		return $data;
	}

	public function GetPubThumbnail($itemId, $itemType) {

		$q = "SELECT image_thumb FROM up_pub_images WHERE item_id = '{$itemId}' AND item_type = '{$itemType}' ORDER BY image_order ASC LIMIT 1";
		
		$rs = Core::Database()->Query($q);
		
		$t = $rs->num_rows;
		
		if (1 == $t) {
			
			$r = $rs->fetch_assoc();
			
			return $r['image_thumb'];
		}
		
		return FALSE;
	}

	public function GetPubImage($imageId) {

		$q = "SELECT item_id, item_type, image_file, image_thumb, image_order FROM up_pub_images WHERE image_id = '{$imageId}' LIMIT 1";
		
		$rs = Core::Database()->Query($q);
		
		$t = $rs->num_rows;
		
		if (1 == $t) {
			
			return $rs->fetch_assoc();
		}
		
		return FALSE;
	}

	public function GetNextPubImageOrder($itemId, $itemType) {

		$q = "SELECT COALESCE(MAX(image_order), 0) + 1 AS next FROM up_pub_images WHERE item_id = '{$itemId}' AND item_type = '{$itemType}'";
		
		$rs = Core::Database()->Query($q);
		
		$t = $rs->num_rows;
		
		if (1 == $t) {
			
			$r = $rs->fetch_assoc();
			
			return $r['next'];
		}
		
		return FALSE;
	}

	public function AddPubImage($itemId, $itemType, $imageFile, $imageThumb, $imageOrder) {

		$q = "INSERT INTO up_pub_images (item_id, item_type, image_file, image_thumb, image_order) VALUES
			('{$itemId}', '{$itemType}', '{$imageFile}', '{$imageThumb}', '{$imageOrder}')";
		
		return Core::Database()->Query($q) ? Core::Database()->GetInsertId() : FALSE;
	}

	public function ChangePubImageOrder($imageId, $imageOrder) {

		$q = "UPDATE up_pub_images SET image_order = '{$imageOrder}' WHERE image_id = '{$imageId}' LIMIT 1";
		
		return Core::Database()->Query($q) ? Core::Database()->GetAffectedRows() : FALSE;
	}

	public function DeletePubImage($imageId, $pathWww) {

		$image = $this->GetPubImage($imageId);
		
		if (!empty($image)) {
			
			$q = "DELETE FROM up_pub_images WHERE image_id = '{$imageId}' LIMIT 1";
			
			if (Core::Database()->Query($q)) {
				
				if (!unlink("{$pathWww}usr/media/{$image['item_type']}/{$image['image_file']}")) {
					
					return FALSE;
				}
				
				if (!unlink("{$pathWww}usr/media/{$image['item_type']}_t/{$image['image_thumb']}")) {
					
					return FALSE;
				}
				
				return Core::Database()->GetAffectedRows();
			}
		}
		
		return FALSE;
	}

	public function GetTotalPubs() {

		$q = "SELECT COUNT(pub_id) AS total FROM up_pub_main";
		
		$rs = Core::Database()->Query($q);
		
		$t = $rs->num_rows;
		
		if (1 == $t) {
			$r = $rs->fetch_assoc();
			
			return $r['total'];
		}
		
		return FALSE;
	}

	public function GetTotalPubIssues() {

		$q = "SELECT COUNT(pub_issue_id) AS total FROM up_pub_issues_main";
		
		$rs = Core::Database()->Query($q);
		
		$t = $rs->num_rows;
		
		if (1 == $t) {
			$r = $rs->fetch_assoc();
			
			return $r['total'];
		}
		
		return FALSE;
	}

	public function GetExistingPubTypes($i18nLocale = NULL) {

		$data = array();
		
		$i18n_locale = !empty($i18nLocale) ? $i18nLocale : Core::I18n()->GetLocale();
		
		$q = "SELECT P.pub_type_id AS id, T.pub_type_title AS title, COUNT(P.pub_type_id) AS total
				FROM up_pub_main AS P
				LEFT JOIN up_pub_types_details AS T ON (T.pub_type_id = P.pub_type_id AND T.i18n_locale = '{$i18n_locale}')
				GROUP BY P.pub_type_id";
		
		$rs = Core::Database()->Query($q);
		
		$t = $rs->num_rows;
		
		if (0 < $t) {
			
			while ($r = $rs->fetch_assoc()) {
				
				$data[$r['id']] = $r;
			}
		}
		
		return $data;
	}

	public function GetExistingPubCountries() {

		$data = array();
		
		$q = "SELECT P.country_code AS id, C.country_name AS title, COUNT(P.country_code) AS total
				FROM up_pub_main AS P
				LEFT JOIN up_geo_countries AS C ON (C.country_code = P.country_code)
				GROUP BY P.country_code";
		
		$rs = Core::Database()->Query($q);
		
		$t = $rs->num_rows;
		
		if (0 < $t) {
			
			while ($r = $rs->fetch_assoc()) {
				
				$data[$r['id']] = $r;
			}
		}
		
		return $data;
	}

	public function GetExistingPubIssueFormats() {

		$data = array();
		
		$q = "SELECT I.pub_format_id AS id, F.pub_format_name AS title, COUNT(pub_issue_id) AS total
				FROM up_pub_issues_main AS I
				LEFT JOIN up_pub_formats_main AS F ON (F.pub_format_id = I.pub_format_id)
				GROUP BY I.pub_format_id";
		
		$rs = Core::Database()->Query($q);
		
		$t = $rs->num_rows;
		
		if (0 < $t) {
			
			while ($r = $rs->fetch_assoc()) {
				
				$data[$r['id']] = $r;
			}
		}
		
		return $data;
	}

	public function GetExistingPubIssueLayoutTypes($i18nLocale = NULL) {

		$data = array();
		
		$i18n_locale = !empty($i18nLocale) ? $i18nLocale : Core::I18n()->GetLocale();
		
		$q = "SELECT I.pub_layout_type_id AS id, D.pub_layout_type_title AS title, COUNT(I.pub_issue_id) AS total
				FROM up_pub_issues_main AS I
				LEFT JOIN up_pub_layout_types_details AS D ON (D.pub_layout_type_id = I.pub_layout_type_id AND D.i18n_locale = '{$i18n_locale}')
				GROUP BY I.pub_layout_type_id";
		
		$rs = Core::Database()->Query($q);
		
		$t = $rs->num_rows;
		
		if (0 < $t) {
			
			while ($r = $rs->fetch_assoc()) {
				
				$data[$r['id']] = $r;
			}
		}
		
		return $data;
	}

	public function GetExistingPubIssueDuplicationTypes($i18nLocale = NULL) {

		$data = array();
		
		$i18n_locale = !empty($i18nLocale) ? $i18nLocale : Core::I18n()->GetLocale();
		
		$q = "SELECT I.pub_duplication_type_id AS id, D.pub_duplication_type_title AS title, COUNT(I.pub_issue_id) AS total
				FROM up_pub_issues_main AS I
				LEFT JOIN up_pub_duplication_types_details AS D ON (D.pub_duplication_type_id = I.pub_duplication_type_id AND D.i18n_locale = '{$i18n_locale}')
				GROUP BY I.pub_duplication_type_id";
		
		$rs = Core::Database()->Query($q);
		
		$t = $rs->num_rows;
		
		if (0 < $t) {
			
			while ($r = $rs->fetch_assoc()) {
				
				$data[$r['id']] = $r;
			}
		}
		
		return $data;
	}

	public function GetExistingPubIssueYears() {

		$data = array();
		
		$q = "SELECT I.pub_issue_year AS id, COUNT(I.pub_issue_id) AS total
				FROM up_pub_issues_main AS I
				GROUP BY I.pub_issue_year";
		
		$rs = Core::Database()->Query($q);
		
		$t = $rs->num_rows;
		
		if (0 < $t) {
			
			while ($r = $rs->fetch_assoc()) {
				
				$data[$r['id']] = $r['total'];
			}
		}
		
		return $data;
	}

	public function GetExistingPubIssueLanguages() {

		$data = array();
		
		$q = "SELECT I.language_code AS id, L.language_name AS title, COUNT(I.pub_issue_id) as total
				FROM up_pub_issues_languages AS I
				LEFT JOIN up_geo_languages AS L ON (L.language_code = I.language_code)
				GROUP BY I.language_code";
		
		$rs = Core::Database()->Query($q);
		
		$t = $rs->num_rows;
		
		if (0 < $t) {
			
			while ($r = $rs->fetch_assoc()) {
				
				$data[$r['id']] = $r;
			}
		}
		
		return $data;
	}

	public function GetExistingPubIssueContentData($i18nLocale = NULL) {

		$data = array();
		
		$i18n_locale = !empty($i18nLocale) ? $i18nLocale : Core::I18n()->GetLocale();
		
		$q = "SELECT I.pub_content_type_id AS id, C.pub_content_type_title AS title, I.pub_issue_content_data AS content, COUNT(I.pub_issue_content_data) AS total
				FROM up_pub_issues_content AS I
				LEFT JOIN up_pub_content_types_main AS M ON (M.pub_content_type_id = I.pub_content_type_id)
				LEFT JOIN up_pub_content_types_details AS C ON (C.pub_content_type_id = M.pub_content_type_id AND C.i18n_locale = '{$i18n_locale}')
				WHERE M.contains_data = '1'
				GROUP BY I.pub_issue_content_data
				ORDER BY title, content";
		
		$rs = Core::Database()->Query($q);
		
		$t = $rs->num_rows;
		
		if (0 < $t) {
			
			while ($r = $rs->fetch_assoc()) {
				
				$data[$r['id']]['id'] = $r['id'];
				$data[$r['id']]['title'] = $r['title'];
				$data[$r['id']]['content'][$r['content']] = $r['total'];
			}
		}
		
		return $data;
	}

	public function GetExistingPubIssueContentValues($i18nLocale = NULL) {

		$data = array();
		
		$i18n_locale = !empty($i18nLocale) ? $i18nLocale : Core::I18n()->GetLocale();
		
		$q = "SELECT I.pub_content_type_id AS id, C.pub_content_type_title AS title, COUNT(I.pub_issue_content_value) AS total
				FROM up_pub_issues_content AS I
				LEFT JOIN up_pub_content_types_main AS M ON (M.pub_content_type_id = I.pub_content_type_id)
				LEFT JOIN up_pub_content_types_details AS C ON (C.pub_content_type_id = M.pub_content_type_id AND C.i18n_locale = '{$i18n_locale}')
				WHERE M.contains_data = '0' AND I.pub_issue_content_value = '1'
				GROUP BY I.pub_content_type_id
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

	public function GetPubStats($i18nLocale = NULL) {

		$stats = array();
		
		$stats['total_pubs'] = $this->GetTotalPubs();
		
		$stats['total_pub_issues'] = $this->GetTotalPubIssues();
		
		$stats['type'] = $this->GetExistingPubTypes($i18nLocale);
		
		$stats['country'] = $this->GetExistingPubCountries();
		
		$stats['format'] = $this->GetExistingPubIssueFormats();
		
		$stats['layout'] = $this->GetExistingPubIssueLayoutTypes($i18nLocale);
		
		$stats['duplication'] = $this->GetExistingPubIssueDuplicationTypes($i18nLocale);
		
		$stats['year'] = $this->GetExistingPubIssueYears();
		
		$stats['language'] = $this->GetExistingPubIssueLanguages();
		
		$stats['content']['data'] = $this->GetExistingPubIssueContentData($i18nLocale);
		
		$stats['content']['values'] = $this->GetExistingPubIssueContentValues($i18nLocale);
		
		return $stats;
	}
}
?>
