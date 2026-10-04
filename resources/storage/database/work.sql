-- GetTotalPubIssues()
SELECT COUNT(pub_issue_id) AS total FROM up_pub_issues_main;

-- GetExistingPubTypes($i18nLocale = NULL)
SELECT P.pub_type_id AS id, T.pub_type_title AS title, COUNT(P.pub_type_id) AS total
FROM up_pub_main AS P
LEFT JOIN up_pub_types_details AS T ON (T.pub_type_id = P.pub_type_id AND T.i18n_locale = 'en_US')
GROUP BY P.pub_type_id
;

-- GetExistingPubCountries()
SELECT P.country_code AS id, C.country_name AS title, COUNT(P.country_code) AS total
FROM up_pub_main AS P
LEFT JOIN up_geo_countries AS C ON (C.country_code = P.country_code)
GROUP BY P.country_code
;

-- GetExistingPubIssueFormats()
SELECT I.pub_format_id AS id, F.pub_format_name AS title, COUNT(pub_issue_id) AS total
FROM up_pub_issues_main AS I
LEFT JOIN up_pub_formats_main AS F ON (F.pub_format_id = I.pub_format_id)
GROUP BY I.pub_format_id
;

-- GetExistingPubIssueDuplicationTypes($i18nLocale = NULL)
SELECT I.pub_duplication_type_id AS id, D.pub_duplication_type_title AS title, COUNT(I.pub_issue_id) AS total
FROM up_pub_issues_main AS I
LEFT JOIN up_pub_duplication_types_details AS D ON (D.pub_duplication_type_id = I.pub_duplication_type_id AND D.i18n_locale = 'en_US')
GROUP BY I.pub_duplication_type_id
;

-- GetExistingPubIssueYears()
SELECT I.pub_issue_year AS id, COUNT(I.pub_issue_id) AS total
FROM up_pub_issues_main AS I
GROUP BY I.pub_issue_year
;

-- GetExistingPubIssueLanguages()
SELECT I.language_code AS id, L.language_name AS title, COUNT(I.pub_issue_id) as total
FROM up_pub_issues_languages AS I
LEFT JOIN up_geo_languages AS L ON (L.language_code = I.language_code)
GROUP BY I.language_code
;

-- GetExistingPubIssueContentData($i18nLocale = NULL)
SELECT I.pub_content_type_id AS id, C.pub_content_type_title AS title, I.pub_issue_content_data AS content, COUNT(I.pub_issue_content_data) AS total
FROM up_pub_issues_content AS I
LEFT JOIN up_pub_content_types_main AS M ON (M.pub_content_type_id = I.pub_content_type_id)
LEFT JOIN up_pub_content_types_details AS C ON (C.pub_content_type_id = M.pub_content_type_id AND C.i18n_locale = 'en_US')
WHERE M.contains_data = '1'
GROUP BY I.pub_issue_content_data
ORDER BY title, content
;

-- GetExistingPubIssueContentValues($i18nLocale = NULL)
SELECT I.pub_content_type_id AS id, C.pub_content_type_title AS title, COUNT(I.pub_issue_content_value) AS total
FROM up_pub_issues_content AS I
LEFT JOIN up_pub_content_types_main AS M ON (M.pub_content_type_id = I.pub_content_type_id)
LEFT JOIN up_pub_content_types_details AS C ON (C.pub_content_type_id = M.pub_content_type_id AND C.i18n_locale = 'en_US')
WHERE M.contains_data = '0' AND I.pub_issue_content_value = '1'
GROUP BY I.pub_content_type_id
ORDER BY title
;


-- GetItemLists($itemId, $i18nLocale = NULL)
SELECT L.list_id, L.user_id, U.user_nickname AS user_name, L.list_title, T.list_type_title, I.item_notes
FROM up_list_items AS I
LEFT JOIN up_lists AS L ON (L.list_id = I.list_id)
LEFT JOIN up_acl_users AS U ON (U.user_id = L.user_id)
LEFT JOIN up_list_types_details AS T ON (T.list_type_id = L.list_type_id AND T.i18n_locale = 'en_US')
WHERE I.item_id = '6'
ORDER BY user_name;

-- GetUserLists($userId, $itemId)
SELECT L.list_id, L.list_title
, IF(I.item_id IS NOT NULL, '1', '0') AS item_in_list
FROM up_lists AS L
LEFT JOIN up_list_items AS I ON (I.list_id = L.list_id AND I.item_id = '5')
WHERE L.user_id = '1'
ORDER BY L.list_title

-- GetListTypeStats($i18nLocale = NULL)
SELECT L.list_type_id AS id, T.list_type_title AS title, COUNT(L.list_type_id) AS total
FROM up_lists AS L
LEFT JOIN up_list_types_details AS T ON (T.list_type_id = L.list_type_id AND T.i18n_locale = 'en_US')
GROUP BY id
ORDER BY title
;
