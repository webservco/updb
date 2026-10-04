<?php
/**
 * $Id$
 *
 * Edit controller.
 *
 * Module: Pub
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
 * Controller_Content_Edit
 * Edit Controller for Content module.
 * 
 * @author Radu Maier <radu@webserv.co>
 * @package UpdbApp
 * @subpackage ContentModule
 */
class Controller_Pub_Edit extends ControllerApp {

	public function PubAdd() {

		$this->Init();
		
		$data = $this->Module()->GetData($this->_mRequest);
		
		$this->CheckEditorAccess($data);
		
		$data['strings']['title'] = __('Add a publication to the database.');
		
		$data['form'] = $this->Module()->GetPubAddForm($data['user']['Id']);
		
		$data['back_location'] = "Pub/App/PubList/";
		
		echo $this->GetOutput($data, 'pub_edit');
		
		return TRUE;
	}

	public function PubModify() {

		$this->Init();
		
		$data = $this->Module()->GetData($this->_mRequest);
		
		$this->CheckEditorAccess($data);
		
		$data['pub_id'] = !empty($data['query']['pub']) ? $data['query']['pub'] : NULL;
		
		$data['strings']['title'] = __('Edit publication.');
		
		$data['form'] = $this->Module()->GetPubModifyForm($data['user']['Id'], $data['pub_id']);
		
		$data['back_location'] = "Pub/App/PubView/pub/{$data['pub_id']}";
		
		echo $this->GetOutput($data, 'pub_edit');
		
		return TRUE;
	}

	public function PubIssueAdd() {

		$this->Init();
		
		$data = $this->Module()->GetData($this->_mRequest);
		
		$this->CheckEditorAccess($data);
		
		$data['pub_id'] = !empty($data['query']['pub']) ? $data['query']['pub'] : NULL;
		
		$data['strings']['title'] = __('Add issue');
		
		$data['form'] = $this->Module()->GetPubIssueAddForm($data['user']['Id'], $data['pub_id']);
		
		$data['back_location'] = "Pub/App/PubView/pub/{$data['pub_id']}";
		
		echo $this->GetOutput($data, 'pub_edit');
		
		return TRUE;
	}

	public function PubIssueModify() {

		$this->Init();
		
		$data = $this->Module()->GetData($this->_mRequest);
		
		$this->CheckEditorAccess($data);
		
		$data['pub_id'] = !empty($data['query']['pub']) ? $data['query']['pub'] : NULL;
		
		$data['pub_issue_id'] = !empty($data['query']['issue']) ? $data['query']['issue'] : NULL;
		
		$data['strings']['title'] = __('Edit publication issue.');
		
		$data['form'] = $this->Module()->GetPubIssueModifyForm($data['user']['Id'], $data['pub_issue_id'], $data['pub_id']);
		
		$data['back_location'] = "Pub/App/PubIssueView/issue/{$data['pub_issue_id']}";
		
		if (!empty($data['pub_id'])) {
			
			$data['back_location'] .= "/pub/{$data['pub_id']}";
		}
		
		echo $this->GetOutput($data, 'pub_edit');
		
		return TRUE;
	}

	public function PubImagesEdit() {

		$this->Init();
		
		$data = $this->Module()->GetData($this->_mRequest);
		
		$this->CheckEditorAccess($data);
		
		$data['item_id'] = !empty($data['query']['id']) ? $data['query']['id'] : NULL;
		
		$data['item_type'] = 'pub';
		
		$data['pub_images'] = $this->Pub()->GetPubImages($data['item_id'], 'pub');
		
		$data['form'] = $this->Module()->GetPubImagesForm($data['user']['Id'], $data['item_id'], $data['item_type'], $data['path']['www']);
		
		$data['delete_next'] = Core::String()->EncodeUrlString("Pub/Edit/PubImagesEdit/id/{$data['item_id']}");
		
		$data['back_location'] = "Pub/App/PubView/pub/{$data['item_id']}";
		
		echo $this->GetOutput($data, 'pub_images_edit');
		
		return TRUE;
	}

	public function PubIssueImagesEdit() {

		$this->Init();
		
		$data = $this->Module()->GetData($this->_mRequest);
		
		$this->CheckEditorAccess($data);
		
		$data['pub_id'] = !empty($data['query']['pub']) ? $data['query']['pub'] : NULL;
		
		$data['item_id'] = !empty($data['query']['id']) ? $data['query']['id'] : NULL;
		
		$data['item_type'] = 'pub_issue';
		
		$data['pub_images'] = $this->Pub()->GetPubImages($data['item_id'], $data['item_type']);
		
		$data['form'] = $this->Module()->GetPubImagesForm($data['user']['Id'], $data['item_id'], $data['item_type'], $data['path']['www'], $data['pub_id']);
		
		$data['back_location'] = "Pub/App/PubIssueView/issue/{$data['item_id']}";
		
		$delete_next_string = "Pub/Edit/PubIssueImagesEdit/id/{$data['item_id']}";
		
		if (!empty($data['pub_id'])) {
			
			$delete_next_string .= "/pub/{$data['pub_id']}";
			
			$data['back_location'] .= "/pub/{$data['pub_id']}";
		}
		
		$data['delete_next'] = Core::String()->EncodeUrlString($delete_next_string);
		
		echo $this->GetOutput($data, 'pub_images_edit');
		
		return TRUE;
	}

	public function DeletePubImage() {

		$this->Init();
		
		$data = $this->Module()->GetData($this->_mRequest);
		
		$this->CheckEditorAccess($data);
		
		$data['image_id'] = !empty($data['query']['id']) ? $data['query']['id'] : NULL;
		
		$redirect = !empty($data['query']['next']) ? Core::String()->DecodeUrlString($data['query']['next']) : '/';
		
		if (!empty($data['image_id'])) {
			
			$this->Pub()->DeletePubImage($data['image_id'], $data['path']['www']);
			
			$this->Module()->UpdateEditLog($data['user']['Id'], $data['image_id'], 'pub_image', 'delete');
		}
		
		Core::Router()->Redirect($redirect);
		
		exit();
	}

	public function Success() {

		$this->Init();
		
		$data = $this->Module()->GetData($this->_mRequest);
		
		$this->CheckEditorAccess($data);
		
		$data['strings']['message'] = __('Success');
		
		echo $this->GetOutput($data, 'default');
		
		return TRUE;
	}
}
?>