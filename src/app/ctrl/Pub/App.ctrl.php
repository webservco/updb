<?php
/**
 * $Id$
 *
 * App controller.
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
 * Controller_Pub_App
 * App Controller for Pub module.
 * 
 * @author Radu Maier <radu@webserv.co>
 * @package UpdbApp
 * @subpackage PubModule
 */
class Controller_Pub_App extends ControllerApp {

	public function PubList() {

		$this->Init();
		
		$data = $this->Module()->GetData($this->_mRequest);
		
		$data['strings']['title'] = __('Publications list');
		
		$data['publications'] = $this->Pub()->GetPublications();
		
		echo $this->GetOutput($data);
		
		return TRUE;
	}

	public function PubView() {

		$this->Init();
		
		$data = $this->Module()->GetData($this->_mRequest);
		
		$data['strings']['title'] = __('Issues list');
		
		$data['pub_id'] = !empty($data['query']['pub']) ? $data['query']['pub'] : NULL;
		
		$data['pub'] = $this->Pub()->GetPublication($data['pub_id']);
		
		$data['pub_issues'] = $this->Pub()->GetPubIssues($data['pub_id']);
		
		$data['pub_images'] = $this->Pub()->GetPubImages($data['pub_id'], 'pub');
		
		$data['comments'] = $this->GetComments(1, $data['pub_id'], $data);
		
		echo $this->GetOutput($data);
		
		return TRUE;
	}

    public function PubIssueDownload() {
        $this->Init();

        $data = $this->Module()->GetData($this->_mRequest);

        $this->RequireReferer();

        $this->CheckUserAccess($data['user']);

        $data = $this->Module()->GetData($this->_mRequest);

        $data['pub_issue_id'] = !empty($data['query']['issue']) ? (int) $data['query']['issue'] : NULL;

        $data['download_id'] = !empty($data['query']['download']) ? (int) $data['query']['download'] : NULL;

        if ($data['download_id'] !== NULL && $data['pub_issue_id'] !== null) {
            $data['download'] = $this->GetDownload($data, $data['download_id'], $data['pub_issue_id']);
        }

        $this->Downloads()->StoreDownloadLog(
            $data['download_id'],
            $data['user']['Id'],
            $data['user']['StatsCurrent']['ip'],
            $data['user']['StatsCurrent']['agent'],
        );

        $filePath = sprintf(
            '%s/var/fanzines/%s',
            $data['path']['app'],
            $data['download']['file_name'],
        );
        if (!is_readable($filePath)) {
            throw new ErrorException('File not found');
        }

        header('Accept-Ranges: bytes');
        header('Cache-Control: public');
        header('Connection: close');
        header('Content-Description: File Transfer');
        header(sprintf('Content-Disposition: attachment; filename="%s"', $data['download']['file_name']));
        header(sprintf('Content-Length: %s', filesize($filePath)));
        header('Content-Transfer-Encoding: binary');
        header(sprintf('Content-Type: %s', $data['download']['content_type']));
        header(sprintf('Last-Modified: %s', gmdate('D, d M Y H:i:s') . ' GMT'));
        header(sprintf('ETag: %s', md5_file($filePath)));

        readfile($filePath);
        exit;
    }

	public function PubIssueView() {

		$this->Init();
		
		$data = $this->Module()->GetData($this->_mRequest);
		
		$data['pub_id'] = !empty($data['query']['pub']) ? $data['query']['pub'] : NULL;
		
		$data['pub_issue_id'] = !empty($data['query']['issue']) ? $data['query']['issue'] : NULL;
		
		$data['pub_issue'] = $this->Pub()->GetPubIssue($data['pub_issue_id']);
		
		if (!empty($data['pub_issue']['pub_issue_id'])) {
			
			$data['pub_issue_images'] = $this->Pub()->GetPubImages($data['pub_issue_id'], 'pub_issue');
			
			$data['strings']['title'] = $data['pub_issue']['pub_issue_title'];
		}
		
		$data['comments'] = $this->GetComments(2, $data['pub_issue_id'], $data);

        $data['downloads'] = $this->GetDownloads($data['pub_issue_id'], $data);

        //echo '<pre>'; var_dump($data['downloads']); exit; /** @todo */
		
		$data['item_lists'] = $this->Lists()->GetItemLists($data['pub_issue_id']);
		
		if (!empty($data['user']['Logged'])) {
			
			$data['user_lists'] = $this->Lists()->GetUserLists($data['user']['Id'], $data['pub_issue_id']);
		
		}
		
		echo $this->GetOutput($data);
		
		return TRUE;
	}
}
?>