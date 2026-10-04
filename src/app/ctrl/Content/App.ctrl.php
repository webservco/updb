<?php
/**
 * $Id$
 *
 * App controller.
 *
 * Module: Content
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
 * Controller_Content_App
 * App Controller for Content module.
 * 
 * @author Radu Maier <radu@webserv.co>
 * @package UpdbApp
 * @subpackage ContentModule
 */
class Controller_Content_App extends ControllerApp {

	public function Home() {

		$this->Init();
		
		$data = $this->Module()->GetData($this->_mRequest);
		
		$data['latest_issues'] = $this->Pub()->GetLatestPubIssues();
		
		echo $this->GetOutput($data);
		
		return TRUE;
	}

	public function Restricted() {

		return $this->GetDefaultOutput(__('Restricted'));
	}

	public function Display() {

		$this->Init();
		
		$data = $this->Module()->GetData($this->_mRequest);
		
		$content_id = !empty($data['query']['c']) ? $data['query']['c'] : NULL;
		
		if (!empty($content_id)) {
			
			$content = $this->Module()->GetAppContent($content_id);
			
			return $this->GetDefaultContentOutput($content);
		}
		
		return FALSE;
	}

	public function Contact() {

		return $this->GetDefaultOutput(__('Contact'));
	}

	public function Debug() {

		$this->Init();
		
		$data = $this->Module()->GetData($this->_mRequest);
		
		/**
		 * Debug using the log instead of output in browser (useful for live apps).
		 */
		Core::Log()->Debug('Debugging $data', $data, 'info');
		
		echo $this->GetOutput($data);
		
		return TRUE;
	}

	public function Community() {

		$this->Init();
		
		$data = $this->Module()->GetData($this->_mRequest);
		
		$data['list_types'] = $this->Lists()->GetTypes();
		
		echo $this->GetOutput($data);
		
		return TRUE;
	}

	public function Test() {

		$this->Init();
		
		$data = $this->Module()->GetData($this->_mRequest);
		
		var_dump(Core::Mail()->SendSimple('radu@italiamultimedia.com', 'test WEb smtp mail', 'ehlo?'));
		
		return;
	}
}
?>
