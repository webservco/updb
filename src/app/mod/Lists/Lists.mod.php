<?php
/**
 * $Id$
 *
 * Lists module.
 *
 * @author     Radu Maier <radu@webserv.co>
 * @copyright  Copyright (c) 2008 - 2013, Radu Maier
 * @license    http://www.opensource.org/licenses/bsd-license.php
 * @package    UpdbApp
 * @subpackage ListsModule
 * @version    $Revision$
 * @since      Version 7
 * @access     public
 * @filesource
 */

if (!defined('_WEB')) {
	exit();
}

/**
 * Module_Lists
 * Model class for Lists module.
 * 
 * @author Radu Maier <radu@webserv.co>
 * @package UpdbApp
 * @subpackage ListsModule
 */
class Module_Lists extends ModuleApp {

	public function GetListAddForm($userId, $listTypeId) {

		Core::Form()->SetMethod('post');
		
		$form_config = Core::Config()->Get('lists.form.list');
		
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
				
				if (empty($form['errors'])) {
					
					$list_id = $this->Lists()->Add($userId, $listTypeId, $form['data']['list_title'], $form['data']['list_notes'], $form['data']['list_public']);
					
					if (!empty($list_id)) {
					
					} else {
						
						$form['errors']['_result'][] = __('Error saving data.');
					}
					
					if (!empty($form['errors'])) {
						
						return $form;
					}
					
					Core::Router()->Redirect("Lists/App/Details/list/{$list_id}");
					
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

	public function GetListModifyForm($listId) {

		Core::Form()->SetMethod('post');
		
		$form_config = Core::Config()->Get('lists.form.list');
		
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
				
				if (empty($form['errors'])) {
					
					$this->Lists()->Modify($listId, $form['data']['list_title'], $form['data']['list_notes'], $form['data']['list_public']);
					
					Core::Router()->Redirect("Lists/App/Details/list/{$listId}");
					
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
			
			$list = $this->Lists()->Get($listId);
			
			if (!empty($list)) {
				
				$form['data']['list_title'] = $list['list_title'];
				$form['data']['list_notes'] = $list['list_notes'];
				$form['data']['list_public'] = $list['list_public'];
			}
		}
		
		return $form;
	}

	public function GetItemAddForm($listId, $itemId) {

		Core::Form()->SetMethod('post');
		
		$form_config = Core::Config()->Get('lists.form.item');
		
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
				
				if (empty($form['errors'])) {
					
					if ($this->Lists()->AddItem($listId, $itemId, $form['data']['item_notes'])) {
						
						Core::Router()->Redirect("Lists/App/Details/list/{$listId}");
						
						exit();
					
					} else {
						
						$form['errors']['_result'][] = __('Error saving data.');
					}
				
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

	public function GetItemModifyForm($listId, $itemId) {

		Core::Form()->SetMethod('post');
		
		$form_config = Core::Config()->Get('lists.form.item');
		
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
				
				if (empty($form['errors'])) {
					
					$this->Lists()->ModifyItem($listId, $itemId, $form['data']['item_notes']);
					
					Core::Router()->Redirect("Lists/App/Details/list/{$listId}");
					
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
			
			$item = $this->Lists()->GetItem($listId, $itemId);
			
			if (!empty($item)) {
				
				$form['data']['item_notes'] = $item['item_notes'];
			}
		}
		
		return $form;
	}
}
?>
