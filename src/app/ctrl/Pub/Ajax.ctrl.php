<?php
/**
 * $Id$
 *
 * Ajax controller.
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
 * Controller_Content_Ajax
 * Ajax Controller for Content module.
 * 
 * @author Radu Maier <radu@webserv.co>
 * @package UpdbApp
 * @subpackage ContentModule
 */
class Controller_Pub_Ajax extends ControllerApp {

	public function Edit() {

		$this->Init();
		
		$data = $this->Module()->GetData($this->_mRequest);
		
		$this->CheckEditorAccess($data);
		
		if (!empty($_POST['action'])) {
			
			switch ($_POST['action']) {
				
				case 'images_order' :
					
					$images = !empty($_POST['images']) ? $_POST['images'] : NULL;
					
					if (!empty($images)) {
						
						foreach ($images as $index => $image_id) {
							
							$image_order = $index + 1;
							
							$this->Pub()->ChangePubImageOrder($image_id, $image_order);
						}
						
						echo 'ok';
					} else {
						echo 'ko';
					}
					
					break;
				
				default :
					
					break;
			}
		}
	}
}
?>