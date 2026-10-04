<?php
/**
 * $Id$
 *
 * Content module.
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
 * Module_Content
 * Model class for Content module.
 * 
 * @author Radu Maier <radu@webserv.co>
 * @package UpdbApp
 * @subpackage ContentModule
 */
class Module_Content extends ModuleApp {

	public function GetAppContent($contentId, $i18nLocale = NULL) {

		$i18n_locale = !empty($i18nLocale) ? $i18nLocale : Core::I18n()->GetLocale();
		
		$q = "SELECT content_title, content_data FROM up_content WHERE content_id = '{$contentId}' AND i18n_locale = '{$i18n_locale}' LIMIT 1";
		
		$rs = Core::Database()->Query($q);
		
		$t = $rs->num_rows;
		
		if (1 == $t) {
			
			return $rs->fetch_assoc();
		}
		
		return FALSE;
	}
}
?>
