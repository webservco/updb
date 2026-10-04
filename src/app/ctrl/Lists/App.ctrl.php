<?php
/**
 * $Id$
 *
 * App controller.
 *
 * Module: Lists
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
 * Controller_Lists_App
 * App Controller for Lists module.
 * 
 * @author Radu Maier <radu@webserv.co>
 * @package UpdbApp
 * @subpackage ListsModule
 */
class Controller_Lists_App extends ControllerApp {

	public function All() {

		$this->Init();
		
		$data = $this->Module()->GetData($this->_mRequest);
		
		$this->CheckUserAccess($data['user']);
		
		$data['list_type_id'] = !empty($data['query']['type']) ? $data['query']['type'] : NULL;
		
		$data['strings']['title'] = __("Lists");
		
		if (!empty($data['list_type_id'])) {
			
			$data['list_type_title'] = $this->Lists()->GetTypeTitle($data['list_type_id']);
			
			$data['strings']['title'] .= " - {$data['list_type_title']}";
		}
		
		$data['lists'] = $this->Lists()->GetLists(NULL, $data['list_type_id']);
		
		$data['list_types'] = $this->Lists()->GetTypes();
		
		echo $this->GetOutput($data);
		
		return TRUE;
	}

	public function View() {

		$this->Init();
		
		$data = $this->Module()->GetData($this->_mRequest);
		
		$this->CheckUserAccess($data['user']);
		
		$data['user_id'] = !empty($data['query']['user']) ? $data['query']['user'] : NULL;
		
		$data['user_name'] = Core::User()->GetProperty($data['user_id'], 'user_nickname');
		
		if (!empty($data['user_name'])) {
			
			$data['strings']['title'] = sprintf(__("%s's lists"), $data['user_name']);
			
			$data['lists'] = $this->Lists()->GetLists($data['user_id']);
			
			$data['list_types'] = $this->Lists()->GetTypes();
			
			$data['list_owner'] = ($data['user_id'] == $data['user']['Id']) ? TRUE : FALSE;
		}
		
		echo $this->GetOutput($data);
		
		return TRUE;
	}

	public function Details() {

		$this->Init();
		
		$data = $this->Module()->GetData($this->_mRequest);
		
		$this->CheckUserAccess($data['user']);
		
		$data['list_id'] = !empty($data['query']['list']) ? $data['query']['list'] : NULL;
		
		if (!empty($data['list_id'])) {
			
			$data['list'] = $this->Lists()->Get($data['list_id']);
			
			$data['list_owner'] = ($data['list']['user_id'] == $data['user']['Id']) ? TRUE : FALSE;
		}
		
		echo $this->GetOutput($data);
		
		return TRUE;
	}

	public function Add() {

		$this->Init();
		
		$data = $this->Module()->GetData($this->_mRequest);
		
		$this->CheckUserAccess($data['user']);
		
		$data['list_type_id'] = !empty($data['query']['type']) ? $data['query']['type'] : 1;
		
		$data['list_type_title'] = $this->Lists()->GetTypeTitle($data['list_type_id']);
		
		$data['strings']['title'] = $data['list_type_title'] . ' - ' . __('Create a new list');
		
		$data['form'] = $this->Module()->GetListAddForm($data['user']['Id'], $data['list_type_id']);
		
		echo $this->GetOutput($data, 'list_edit');
		
		return TRUE;
	}

	public function Modify() {

		$this->Init();
		
		$data = $this->Module()->GetData($this->_mRequest);
		
		$data['list_id'] = !empty($data['query']['list']) ? $data['query']['list'] : NULL;
		
		$this->CheckUserListAccess($data['user'], $data['list_id']);
		
		if (!empty($data['list_id'])) {
			
			$data['list_type_title'] = $this->Lists()->GetListTypeTitle($data['list_id']);
			
			$data['strings']['title'] = $data['list_type_title'] . ' - ' . __('Modify list');
			
			$data['form'] = $this->Module()->GetListModifyForm($data['list_id']);
			
			$data['back_location'] = "Lists/App/Details/list/{$data['list_id']}";
		}
		
		echo $this->GetOutput($data, 'list_edit');
		
		return TRUE;
	}

	public function DeleteList() {

		$this->Init();
		
		$data = $this->Module()->GetData($this->_mRequest);
		
		$data['list_id'] = !empty($data['query']['list']) ? $data['query']['list'] : NULL;
		
		$this->CheckUserListAccess($data['user'], $data['list_id']);
		
		if (!empty($data['list_id'])) {
			
			$this->Lists()->Delete($data['list_id']);
		}
		
		Core::Router()->Redirect("Lists/App/View/user/{$data['user']['Id']}");
		
		exit();
	}

	public function AddItem() {

		$this->Init();
		
		$data = $this->Module()->GetData($this->_mRequest);
		
		$data['list_id'] = !empty($data['query']['list']) ? $data['query']['list'] : NULL;
		
		$data['item_id'] = !empty($data['query']['item']) ? $data['query']['item'] : NULL;
		
		$this->CheckUserListAccess($data['user'], $data['list_id']);
		
		if (!empty($data['list_id']) && !empty($data['item_id'])) {
			
			$data['items'] = $this->Lists()->GetItems($data['list_id']);
			
			if (array_key_exists($data['item_id'], $data['items'])) {
				
				Core::Router()->Redirect("Lists/App/Details/list/{$data['list_id']}");
				
				exit();
			}
			
			$data['list_title'] = $this->Lists()->GetTitle($data['list_id']);
			
			$data['item_title'] = $this->Lists()->GetItemTitle($data['item_id']);
			
			$data['strings']['title'] = __('Add item');
			
			$data['form'] = $this->Module()->GetItemAddForm($data['list_id'], $data['item_id']);
		}
		
		echo $this->GetOutput($data, 'item_edit');
		
		return TRUE;
	}

	public function ModifyItem() {

		$this->Init();
		
		$data = $this->Module()->GetData($this->_mRequest);
		
		$data['list_id'] = !empty($data['query']['list']) ? $data['query']['list'] : NULL;
		
		$data['item_id'] = !empty($data['query']['item']) ? $data['query']['item'] : NULL;
		
		$this->CheckUserListAccess($data['user'], $data['list_id']);
		
		if (!empty($data['list_id']) && !empty($data['item_id'])) {
			
			$data['list_title'] = $this->Lists()->GetTitle($data['list_id']);
			
			$data['item_title'] = $this->Lists()->GetItemTitle($data['item_id']);
			
			$data['strings']['title'] = __('Modify item');
			
			$data['form'] = $this->Module()->GetItemModifyForm($data['list_id'], $data['item_id']);
			
			$data['back_location'] = "Lists/App/Details/list/{$data['list_id']}";
		}
		
		echo $this->GetOutput($data, 'item_edit');
		
		return TRUE;
	}

	public function DeleteItem() {

		$this->Init();
		
		$data = $this->Module()->GetData($this->_mRequest);
		
		$data['list_id'] = !empty($data['query']['list']) ? $data['query']['list'] : NULL;
		
		$data['item_id'] = !empty($data['query']['item']) ? $data['query']['item'] : NULL;
		
		$this->CheckUserListAccess($data['user'], $data['list_id']);
		
		if (!empty($data['list_id']) && !empty($data['item_id'])) {
			
			$this->Lists()->DeleteItem($data['list_id'], $data['item_id']);
		}
		
		Core::Router()->Redirect("Lists/App/Details/list/{$data['list_id']}");
		
		exit();
	}
}
?>