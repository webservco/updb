<?php
/**
 * $Id$
 *
 * Pub module.
 *
 * @author     Radu Maier <radu@webserv.co>
 * @copyright  Copyright (c) 2008 - 2013, Radu Maier
 * @license    http://www.opensource.org/licenses/bsd-license.php
 * @package    UpdbApp
 * @subpackage PubModule
 * @version    $Revision$
 * @since      Version 7
 * @access     public
 * @filesource
 */

if (!defined('_WEB')) {
	exit();
}

/**
 * Module_Pub
 * Model class for Pub module.
 * 
 * @author Radu Maier <radu@webserv.co>
 * @package UpdbApp
 * @subpackage PubModule
 */
class Module_Pub extends ModuleApp {

	public function GetPubAddForm($userId) {

		Core::Form()->SetMethod('post');
		
		$form_config = Core::Config()->Get('pub.form.pub');
		
		if (empty($form_config)) {
			
			return FALSE;
		}
		
		$app_langs = Core::I18n()->GetAvailableLangs();
		
		Core::Form()->SetData($form_config);
		
		$pub_types = $this->Pub()->GetPubTypes();
		
		if (!empty($pub_types)) {
			
			Core::Form()->SetMeta('pub_type_id', 'options', $pub_types);
		}
		
		$countries = Core::Geo()->GetCountries();
		
		if (!empty($countries)) {
			
			Core::Form()->SetMeta('country_code', 'options', $countries);
		}
		
		$form['meta'] = Core::Form()->GetMeta();
		
		$form['errors'] = array();
		
		if (!empty($_POST)) {
			
			$result_validation = Core::Form()->Validate();
			
			if (FALSE !== $result_validation) {
				
				$form['data'] = Core::Form()->GetEscapedFilteredData();
				
				if (empty($form['data']['pub_title_sort'])) {
					
					$form['data']['pub_title_sort'] = $form['data']['pub_title'];
				}
				
				if (empty($form['errors'])) {
					
					$pub_id = $this->Pub()->AddPubMain($form['data']);
					
					if (!empty($pub_id)) {
						
						foreach ($app_langs as $lang => $locale) {
							
							$pub_description = $form['data']["pub_description_{$lang}"];
							
							if (!$this->Pub()->AddModifyPubDetails($pub_id, $locale, $pub_description)) {
								
								$form['errors']['_result'][] = __('Error saving details data.');
							}
						}
						
						$this->UpdateEditLog($userId, $pub_id, 'pub', 'add');
					
					} else {
						
						$form['errors']['_result'][] = __('Error saving main data.');
					}
					
					if (!empty($form['errors'])) {
						
						return $form;
					}
					
					Core::Router()->Redirect("Pub/App/PubView/pub/{$pub_id}");
					
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

	public function GetPubModifyForm($userId, $pubId) {

		Core::Form()->SetMethod('post');
		
		$form_config = Core::Config()->Get('pub.form.pub');
		
		if (empty($form_config)) {
			
			return FALSE;
		}
		
		$app_langs = Core::I18n()->GetAvailableLangs();
		
		Core::Form()->SetData($form_config);
		
		$pub_types = $this->Pub()->GetPubTypes();
		
		if (!empty($pub_types)) {
			
			Core::Form()->SetMeta('pub_type_id', 'options', $pub_types);
		}
		
		$countries = Core::Geo()->GetCountries();
		
		if (!empty($countries)) {
			
			Core::Form()->SetMeta('country_code', 'options', $countries);
		}
		
		$form['meta'] = Core::Form()->GetMeta();
		
		$form['errors'] = array();
		
		if (!empty($_POST)) {
			
			$result_validation = Core::Form()->Validate();
			
			if (FALSE !== $result_validation) {
				
				$form['data'] = Core::Form()->GetEscapedFilteredData();
				
				if (empty($form['errors'])) {
					
					if (empty($form['data']['pub_title_sort'])) {
						
						$form['data']['pub_title_sort'] = $form['data']['pub_title'];
					}
					
					$this->Pub()->ModifyPubMain($pubId, $form['data']);
					
					foreach ($app_langs as $lang => $locale) {
						
						$pub_description = $form['data']["pub_description_{$lang}"];
						
						$this->Pub()->AddModifyPubDetails($pubId, $locale, $pub_description);
					}
					
					$this->UpdateEditLog($userId, $pubId, 'pub', 'modify');
					
					if (!empty($form['errors'])) {
						
						return $form;
					}
					
					Core::Router()->Redirect("Pub/App/PubView/pub/{$pubId}");
					
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
			
			$pub_main = $this->Pub()->GetPubMain($pubId);
			
			$pub_details = $this->Pub()->GetPubDetails($pubId);
			
			if (!empty($pub_main)) {
				
				foreach ($pub_main as $key => $value) {
					
					$form['data'][$key] = $value;
				}
			}
			
			foreach ($app_langs as $lang => $locale) {
				
				if (!empty($pub_details[$locale]['pub_description'])) {
					
					$form['data']["pub_description_{$lang}"] = $pub_details[$locale]['pub_description'];
				}
			}
		}
		
		return $form;
	}

	public function GetPubIssueAddForm($userId, $pubId = NULL) {

		Core::Form()->SetMethod('post');
		
		$form_config = Core::Config()->Get('pub.form.pub_issue');
		
		if (empty($form_config)) {
			
			return FALSE;
		}
		
		$app_langs = Core::I18n()->GetAvailableLangs();
		
		$pub_content_types = $this->Pub()->GetPubContentTypes();
		
		foreach ($pub_content_types as $pub_content_type) {
			
			$type = !empty($pub_content_type['contains_data']) ? 'text' : 'checkbox';
			
			$description = !empty($pub_content_type['contains_data']) ? "Comma separated list of values, eg \"Animal Rights, Strongly Opposed Records, Veganism\"." : "yes/no";
			
			$form_config["pub_issue_content_{$pub_content_type['pub_content_type_id']}"] = array(
				'type' => $type,
				'title' => $pub_content_type['pub_content_type_title'],
				'description' => $description,
				'required' => 0,
				'filters' => 'StripTags',
				'conditions' => NULL
			);
		}
		
		Core::Form()->SetData($form_config);
		
		$pub_list = $this->Pub()->GetPubList();
		
		if (!empty($pub_list)) {
			
			Core::Form()->SetMeta('pub_list', 'options', $pub_list);
		}
		
		$pub_format_list = $this->Pub()->GetPubFormats();
		
		if (!empty($pub_format_list)) {
			
			Core::Form()->SetMeta('pub_format_id', 'options', $pub_format_list);
		}
		
		$pub_layout_types = $this->Pub()->GetPubLayoutTypes();
		
		if (!empty($pub_layout_types)) {
			
			Core::Form()->SetMeta('pub_layout_type_id', 'options', $pub_layout_types);
		}
		
		$pub_duplication_types = $this->Pub()->GetPubDuplicationTypes();
		
		if (!empty($pub_duplication_types)) {
			
			Core::Form()->SetMeta('pub_duplication_type_id', 'options', $pub_duplication_types);
		}
		
		$language_list = Core::Geo()->GetLanguages(1);
		
		if (!empty($language_list)) {
			
			Core::Form()->SetMeta('pub_languages', 'options', $language_list);
		}
		
		$form['meta'] = Core::Form()->GetMeta();
		
		$form['errors'] = array();
		
		if (!empty($_POST)) {
			
			$result_validation = Core::Form()->Validate();
			
			if (FALSE !== $result_validation) {
				
				$form['data'] = Core::Form()->GetEscapedFilteredData();
				
				if (empty($form['errors'])) {
					
					$pub_issue_id = $this->Pub()->AddPubIssueMain($form['data']);
					
					if (!empty($pub_issue_id)) {
						
						$this->Pub()->AddModifyPubIssuePubs($pub_issue_id, $form['data']['pub_list']);
						
						$this->Pub()->AddModifyPubIssueLanguages($pub_issue_id, $form['data']['pub_languages']);
						
						foreach ($app_langs as $lang => $locale) {
							
							$pub_issue_description = !empty($form['data']["pub_issue_description_{$lang}"]) ? $form['data']["pub_issue_description_{$lang}"] : NULL;
							
							if (!$this->Pub()->AddModifyPubIssueDetails($pub_issue_id, $locale, $pub_issue_description)) {
								
								$form['errors']['_result'][] = __('Error saving details data.');
							}
						}
						
						foreach ($pub_content_types as $pub_content_type) {
							
							if (isset($form['data']["pub_issue_content_{$pub_content_type['pub_content_type_id']}"])) {
								
								if (!empty($pub_content_type['contains_data'])) {
									
									$data = isset($form['data']["pub_issue_content_{$pub_content_type['pub_content_type_id']}"]) ? $form['data']["pub_issue_content_{$pub_content_type['pub_content_type_id']}"] : NULL;
									
									$pub_issue_content_data = array_map('trim', explode(',', $data));
									
									if (!$this->Pub()->AddPubIssueContentData($pub_issue_id, $pub_content_type['pub_content_type_id'], $pub_issue_content_data)) {
										
										$form['errors']['_result'][] = __('Error saving content data.');
									}
								
								} else {
									
									if (!$this->Pub()->AddPubIssueContentValue($pub_issue_id, $pub_content_type['pub_content_type_id'], $form['data']["pub_issue_content_{$pub_content_type['pub_content_type_id']}"])) {
										
										$form['errors']['_result'][] = __('Error saving content data.');
									}
								
								}
							}
						}
						
						$this->UpdateEditLog($userId, $pub_issue_id, 'pub_issue', 'add');
					
					} else {
						$form['errors']['_result'][] = __('Error saving main data.');
					}
					
					$redirect = "Pub/App/PubIssueView/issue/{$pub_issue_id}";
					
					if (!empty($pubId)) {
						
						$redirect .= "/pub/{$pubId}";
					}
					
					Core::Router()->Redirect($redirect);
					
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
			
			if (!empty($pubId)) {
				
				$form['data']['pub_list'][$pubId] = 1;
			}
		}
		
		return $form;
	}

	public function GetPubIssueModifyForm($userId, $pubIssueId, $pubId) {

		Core::Form()->SetMethod('post');
		
		$form_config = Core::Config()->Get('pub.form.pub_issue');
		
		if (empty($form_config)) {
			
			return FALSE;
		}
		
		$app_langs = Core::I18n()->GetAvailableLangs();
		
		$pub_content_types = $this->Pub()->GetPubContentTypes();
		
		foreach ($pub_content_types as $pub_content_type) {
			
			$type = !empty($pub_content_type['contains_data']) ? 'text' : 'checkbox';
			
			$description = !empty($pub_content_type['contains_data']) ? "Comma separated list of values, eg \"Animal Rights, Strongly Opposed Records, Veganism\"." : "yes/no";
			
			$form_config["pub_issue_content_{$pub_content_type['pub_content_type_id']}"] = array(
				'type' => $type,
				'title' => $pub_content_type['pub_content_type_title'],
				'description' => $description,
				'required' => 0,
				'filters' => 'StripTags',
				'conditions' => NULL
			);
		}
		
		Core::Form()->SetData($form_config);
		
		$pub_list = $this->Pub()->GetPubList();
		
		if (!empty($pub_list)) {
			
			Core::Form()->SetMeta('pub_list', 'options', $pub_list);
		}
		
		$language_list = Core::Geo()->GetLanguages(1);
		
		if (!empty($language_list)) {
			
			Core::Form()->SetMeta('pub_languages', 'options', $language_list);
		}
		
		$pub_format_list = $this->Pub()->GetPubFormats();
		
		if (!empty($pub_format_list)) {
			
			Core::Form()->SetMeta('pub_format_id', 'options', $pub_format_list);
		}
		
		$pub_layout_types = $this->Pub()->GetPubLayoutTypes();
		
		if (!empty($pub_layout_types)) {
			
			Core::Form()->SetMeta('pub_layout_type_id', 'options', $pub_layout_types);
		}
		
		$pub_duplication_types = $this->Pub()->GetPubDuplicationTypes();
		
		if (!empty($pub_duplication_types)) {
			
			Core::Form()->SetMeta('pub_duplication_type_id', 'options', $pub_duplication_types);
		}
		
		$form['meta'] = Core::Form()->GetMeta();
		
		$form['errors'] = array();
		
		if (!empty($_POST)) {
			
			$result_validation = Core::Form()->Validate();
			
			if (FALSE !== $result_validation) {
				
				$form['data'] = Core::Form()->GetEscapedFilteredData();
				
				if (empty($form['errors'])) {
					
					$this->Pub()->ModifyPubIssueMain($pubIssueId, $form['data']);
					
					$this->Pub()->DeletePubIssuePubs($pubIssueId);
					
					$this->Pub()->AddModifyPubIssuePubs($pubIssueId, $form['data']['pub_list']);
					
					$this->Pub()->DeletePubIssueLanguages($pubIssueId);
					
					$this->Pub()->AddModifyPubIssueLanguages($pubIssueId, $form['data']['pub_languages']);
					
					foreach ($app_langs as $lang => $locale) {
						
						$pub_issue_description = !empty($form['data']["pub_issue_description_{$lang}"]) ? $form['data']["pub_issue_description_{$lang}"] : NULL;
						
						$this->Pub()->AddModifyPubIssueDetails($pubIssueId, $locale, $pub_issue_description);
					}
					
					foreach ($pub_content_types as $pub_content_type) {
						
						if (isset($form['data']["pub_issue_content_{$pub_content_type['pub_content_type_id']}"])) {
							
							$this->Pub()->DeletePubIssueContent($pubIssueId, $pub_content_type['pub_content_type_id']);
							
							if (!empty($pub_content_type['contains_data'])) {
								
								$data = isset($form['data']["pub_issue_content_{$pub_content_type['pub_content_type_id']}"]) ? $form['data']["pub_issue_content_{$pub_content_type['pub_content_type_id']}"] : NULL;
								
								$pub_issue_content_data = array_map('trim', explode(',', $data));
								
								$this->Pub()->AddPubIssueContentData($pubIssueId, $pub_content_type['pub_content_type_id'], $pub_issue_content_data);
							
							} else {
								
								$this->Pub()->AddPubIssueContentValue($pubIssueId, $pub_content_type['pub_content_type_id'], $form['data']["pub_issue_content_{$pub_content_type['pub_content_type_id']}"]);
							}
						}
					}
					
					$this->UpdateEditLog($userId, $pubIssueId, 'pub_issue', 'modify');
					
					if (!empty($form['errors'])) {
						
						return $form;
					}
					
					$redirect = "Pub/App/PubIssueView/issue/{$pubIssueId}";
					
					if (!empty($pubId)) {
						
						$redirect .= "/pub/{$pubId}";
					}
					
					Core::Router()->Redirect($redirect);
					
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
			
			$pub_list = $this->Pub()->GetPubList($pubIssueId);
			
			if (!empty($pub_list)) {
				
				foreach ($pub_list as $pub_id => $pub_title) {
					
					$form['data']['pub_list'][$pub_id] = 1;
				}
			}
			
			$pub_languages = $this->Pub()->GetPubIssueLanguages($pubIssueId);
			
			if (!empty($pub_languages)) {
				
				foreach ($pub_languages as $language_code => $language_name) {
					
					$form['data']['pub_languages'][$language_code] = 1;
				}
			}
			
			$pub_issue_main = $this->Pub()->GetPubIssueMain($pubIssueId);
			
			$pub_issue_details = $this->Pub()->GetPubIssueDetails($pubIssueId);
			
			if (!empty($pub_issue_main)) {
				
				foreach ($pub_issue_main as $key => $value) {
					
					$form['data'][$key] = $value;
				}
			}
			
			foreach ($app_langs as $lang => $locale) {
				
				if (!empty($pub_issue_details[$locale]['pub_issue_description'])) {
					
					$form['data']["pub_issue_description_{$lang}"] = $pub_issue_details[$locale]['pub_issue_description'];
				}
			}
			
			foreach ($pub_content_types as $pub_content_type) {
				
				if (!empty($pub_content_type['contains_data'])) {
					
					$pub_issue_content = $this->Pub()->GetPubIssueContentData($pubIssueId, $pub_content_type['pub_content_type_id']);
					
					if (!empty($pub_issue_content)) {
						
						$form['data']["pub_issue_content_{$pub_content_type['pub_content_type_id']}"] = implode(', ', $pub_issue_content);
					}
				} else {
					
					$form['data']["pub_issue_content_{$pub_content_type['pub_content_type_id']}"] = $this->Pub()->GetPubIssueContentValue($pubIssueId, $pub_content_type['pub_content_type_id']);
				}
			}
		}
		
		return $form;
	}

	public function GetPubImagesForm($userId, $itemId, $itemType, $pathWww, $pubId = NULL) {

		if (!empty($_FILES['images'])) {
			
			$file_types = array(
				'image/jpeg' => 'jpg',
				'image/png' => 'png'
			);
			
			$upload_dir = "{$pathWww}usr/media/{$itemType}/";
			$upload_dir_t = "{$pathWww}usr/media/{$itemType}_t/";
			
			$next_order = $this->Pub()->GetNextPubImageOrder($itemId, $itemType);
			
			if (is_array($_FILES['images']['name'])) {
				
				$t = count($_FILES['images']['name']);
				
				for ($i = 0; $i < $t; $i ++) {
					
					if (0 == $_FILES['images']['error'][$i]) {
						
						if (array_key_exists($_FILES['images']['type'][$i], $file_types)) {
							
							$file_name = "{$itemId}_" . md5($_FILES['images']['name'][$i]) . ".{$file_types[$_FILES['images']['type'][$i]]}";
							
							move_uploaded_file($_FILES['images']['tmp_name'][$i], $upload_dir . $file_name);
							
							$thumbnail_name = Core::Graphics()->GetThumbnail($upload_dir, $file_name, $upload_dir_t);
							
							$image_id = $this->Pub()->AddPubImage($itemId, $itemType, $file_name, $thumbnail_name, $next_order);
							
							$this->UpdateEditLog($userId, $image_id, 'pub_image', 'add');
							
							$next_order ++;
						}
					}
				}
			} else {
				
				$file_name = "{$itemId}_" . md5($_FILES['images']['name']) . ".{$file_types[$_FILES['images']['type']]}";
				
				move_uploaded_file($_FILES['images']['tmp_name'], $upload_dir . $file_name);
				
				$thumbnail_name = Core::Graphics()->GetThumbnail($upload_dir, $file_name, $upload_dir_t);
				
				$image_id = $this->Pub()->AddPubImage($itemId, $itemType, $file_name, $thumbnail_name, $next_order);
				
				$this->UpdateEditLog($userId, $image_id, 'pub_image', 'add');
			}
			
			$redirect_action = 'pub_issue' == $itemType ? 'PubIssueImagesEdit' : 'PubImagesEdit';
			
			$redirect_extra = ('pub_issue' == $itemType && !empty($pubId)) ? "/pub/{$pubId}" : NULL;
			
			Core::Router()->Redirect("Pub/Edit/{$redirect_action}/id/{$itemId}{$redirect_extra}");
			
			exit();
		}
		
		return FALSE;
	}
}
?>
