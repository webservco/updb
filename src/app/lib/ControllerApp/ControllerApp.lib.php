<?php
/**
 * $Id$
 *
 * Application controller library.
 *
 * @author     Radu Maier <radu@webserv.co>
 * @copyright  Copyright (c) 2013, Radu Maier
 * @license    http://www.opensource.org/licenses/bsd-license.php
 * @package    UpdbApp
 * @subpackage ControllerApp
 * @version    $Revision$
 * @since      Version 1
 * @access     public
 * @filesource
 */

/**
 * Security check.
 */
if (!defined('_WEB')) {
	/**
	 * Stop script execution.
	 */
	exit();
}

/**
 * ControllerApp
 * Application controller library.
 * 
 * @author Radu Maier <radu@webserv.co>
 * @package UpdbApp
 * @subpackage ControllerApp
 */
class ControllerApp extends Controller {

	/**
	 * Pub library object.
	 * 
	 * @var mixed
	 */
	private $_mPub = NULL;

	/**
	 * Lists library object.
	 * 
	 * @var mixed
	 */
	private $_mLists = NULL;

	/**
	 * Comments library object.
	 * 
	 * @var mixed
	 */
	private $_mComments = NULL;

    /**
     * @var ?Downloads
     */
    private $_mDownloads = NULL;

	/**
	 * Controller initialization.
	 * 
	 * @return bool
	 */
	public function Init() {

		$this->_mUseSessions = (bool) Core::Config()->Get('options.sessions');
		
		/**
		 * Initialize output.
		 * This is actually not required,
		 * but we need it here so we can use the
		 * Output library configuration data.
		 */
		$this->Output()->Init($this->_mTemplatePrefix);
		
		/**
		 * Use only in multi-language apps.
		 */
		$this->InitLocale();
		
		return TRUE;
	}

	public function InitLocale() {

		$opt_multilanguage = Core::Config()->Get('options.multilanguage');
		
		if (!$opt_multilanguage) {
			
			return FALSE;
		}
		
		if ($this->_mUseSessions && !empty($_SESSION['I18n']['Chosen'])) {
			
			return FALSE;
		}
		
		$app_langs = Core::I18n()->GetAvailableLangs();
		
		$user_lang = !empty($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) : NULL;

        if (!empty($user_lang) && array_key_exists($user_lang, $app_langs)) {

            Core::I18n() -> SetLocale($app_langs[$user_lang]['locale']);

            return TRUE;
        }
		
		return FALSE;
	}

	/**
	 * Get output.
	 * 
	 * @param array $contentData        	
	 * @param string $customTemplate        	
	 * @param boolean $globalTemplate
	 *        	true if output is to search the custom
	 *        	template in root tpl dir instead of module tpl dir
	 * @param string $mainTemplate
	 *        	override default main template (layout.tpl)
	 * @return string
	 */
	public function GetOutput($contentData = array(), $customTemplate = NULL, $globalTemplate = FALSE, $mainTemplate = NULL) {

		/**
		 * Local data.
		 * 
		 * @var array
		 */
		$local_data = array();
		
		/**
		 * The method has a redundancy check, so it's no trouble if it's already called in $this->Init();
		 */
		$this->Output()->Init($this->_mTemplatePrefix);
		
		/**
		 *
		 * @var _string
		 */
		$template_dir_main = '_main/';
		
		$templates_extra = $this->Output()->GetTemplatesExtra();
		
		foreach ($templates_extra as $template_extra) {
			
			/**
			 * Set the extra tpl var to the parsed content template.
			 * 
			 * @var array
			 */
			
			$local_data["tpl_{$template_extra}"] = $this->Output()->Out($template_dir_main . $template_extra, $contentData);
		}
		
		/**
		 * Get secondary template name.
		 * 
		 * @var string
		 */
		$template_name = (FALSE !== $globalTemplate) ? $template_dir_main . $this->_mTemplatePrefix . $customTemplate : $this->Output()->GetSubTemplate($this->_mRequest, $customTemplate);
		
		/**
		 * Set the content tpl var to the parsed content template.
		 * 
		 * @var array
		 */
		$local_data['tpl_content'] = $this->Output()->Out($template_name, $contentData);
		
		/**
		 * Get all data.
		 * 
		 * @var array
		 */
		$data = array_merge($local_data, $contentData);
		
		/**
		 * Set charset
		 */
		header("Content-type: text/html; charset=utf-8");
		
		/**
		 * Get main template name.
		 */
		$main_template = !empty($mainTemplate) ? $this->_mTemplatePrefix . $mainTemplate : $this->Output()->GetTemplate();
		
		if (Core::Useragent()->IsDevice()) {
			
			$main_template .= '_mobile';
			
			$is_phone = Core::Useragent()->IsDevicePhone();
			
			if ($is_phone) {
				
				$main_template .= '_phone';
			}
		}
		
		return $this->Output()->Out($template_dir_main . $main_template, $data);
	}

	public function CheckAdminAccess($data) {

		$allow = FALSE;
		
		$redirect = 'Content/App/Restricted';
		
		if (!empty($data['user']['Logged'])) {
			
			if (!empty($data['info']['location'])) {
				
				if (Core::Acl()->Check($data['user']['Id'], $data['info']['location'])) {
					
					$allow = TRUE;
				}
			}
		
		} else {
			
			$next = Core::String()->EncodeUrlString($data['info']['location']);
			
			$redirect = "Users/App/Account/next/{$next}";
		}
		
		if (TRUE !== $allow) {
			
			Core::Router()->Redirect($redirect);
			
			exit();
		}
		
		return TRUE;
	}

	public function CheckEditorAccess($data) {

		$allow = FALSE;
		
		$redirect = 'Content/App/Restricted';
		
		if (!empty($data['user']['Logged'])) {
			
			if (!empty($data['info']['location'])) {
				
				if (Core::Acl()->Check($data['user']['Id'], $data['info']['location'])) {
					
					$allow = TRUE;
				}
			}
		
		} else {
			
			$next = Core::String()->EncodeUrlString($data['info']['location']);
			
			$redirect = "Users/App/Account/next/{$next}";
		}
		
		if (TRUE !== $allow) {
			
			Core::Router()->Redirect($redirect);
			
			exit();
		}
		
		return TRUE;
	}

	public function CheckUserAccess($user) {

		if (empty($user['Logged'])) {
			
			Core::Router()->Redirect("Users/App/Account");
			
			exit();
		}
		
		return TRUE;
	}

	public function CheckUserListAccess($user, $listId) {

		if (empty($user['Logged'])) {
			
			Core::Router()->Redirect("Users/App/Account");
			
			exit();
		}
		
		$check = $this->Lists()->GetUser($listId);
		
		if ($user['Id'] != $check) {
			
			Core::Router()->Redirect("Content/App/Restricted");
			
			exit();
		}
		
		return TRUE;
	}

	public function GetDefaultOutput($title = NULL) {

		$this->Init();
		
		$data = $this->Module()->GetData($this->_mRequest);
		
		if (!empty($title)) {
			
			$data['strings']['title'] = $title;
		}
		
		echo $this->GetOutput($data);
		
		return TRUE;
	}

	public function GetDefaultContentOutput($content = array()) {

		$this->Init();
		
		$data = $this->Module()->GetData($this->_mRequest);
		
		if (!empty($content['content_title'])) {
			
			$data['strings']['title'] = $content['content_title'];
		}
		
		if (!empty($content['content_data'])) {
			
			$data['strings']['data'] = $content['content_data'];
		}
		
		echo $this->GetOutput($data, 'db_content');
		
		return TRUE;
	}

	/**
	 * Pub library object.
	 * 
	 * @return mixed
	 */
	public function Pub() {

		if (NULL == $this->_mPub) {
			
			$this->_mPub = Core::Get('Library_Pub');
		}
		
		return $this->_mPub;
	}

	/**
	 * Lists library object.
	 * 
	 * @return mixed
	 */
	public function Lists() {

		if (NULL == $this->_mLists) {
			
			$this->_mLists = Core::Get('Library_Lists');
		}
		
		return $this->_mLists;
	}

	public function Comments() {

		if (NULL == $this->_mComments) {
			
			$this->_mComments = Core::Get('Library_Comments');
			
			$app_prefix = Core::Config()->Get('app.prefix');
			
			$this->_mComments->Init($app_prefix);
		}
		
		return $this->_mComments;
	}

	public function GetComments($commentTypeId, $itemId, $data) {

		$comments['show'] = TRUE;
		
		$comments['list'] = $this->Comments()->GetList($commentTypeId, $itemId);
		
		$comments['next'] = Core::String()->EncodeUrlString("{$data['info']['location']}{$data['info']['path']}");
		
		if (!empty($data['user']['Logged'])) {
			
			$comments['id'] = !empty($data['query']['comment']) ? $data['query']['comment'] : NULL;
			
			$comments['form'] = $this->Comments()->GetForm('user', $data['user']['Id'], $commentTypeId, $itemId, $comments['id']);
		
		}
		
		return $comments;
	}

    public function Downloads() {

        if (NULL == $this->_mDownloads) {

            $this->_mDownloads = Core::Get('Library_Downloads');

            $app_prefix = Core::Config()->Get('app.prefix');

            $this->_mDownloads->Init($app_prefix);
        }

        return $this->_mDownloads;
    }

    public function GetDownload($data, $downloadId, $pubIssueId) {

        $item = null;

        if (!empty($data['user']['Logged'])) {
            $item = $this->Downloads()->GetItem($downloadId, $pubIssueId);
        }

        return $item;
    }

    public function GetDownloads($pubIssueId, $data) {

        $items = [];

        if (!empty($data['user']['Logged'])) {
            $items['show'] = TRUE;
            $items['list'] = $this->Downloads()->GetList($pubIssueId);
        }

        return $items;
    }

    public function RequireReferer() {
        $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : null;
        if ($host === null) {
            throw new ErrorException('Access denied.');
        }

        $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;
        if ($referer === null) {
            throw new ErrorException('Access denied.');
        }

        $refererHost = parse_url($referer, PHP_URL_HOST);
        if (!is_string($refererHost)) {
            throw new ErrorException('Access denied.');
        }

        if ($host !== $refererHost) {
            throw new ErrorException('Access denied.');
        }

        return true;
    }
}
?>