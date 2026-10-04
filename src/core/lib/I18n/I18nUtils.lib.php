<?php
/**
 * $Id: I18nUtils.lib.php 168 2014-04-26 07:28:51Z radu $
 *
 * Internationalization library utilities.
 *
 * @author     Radu Maier <radu@webserv.co>
 * @copyright  Copyright (c) 2008 - 2014, Radu Maier
 * @license    http://www.opensource.org/licenses/bsd-license.php
 * @package    WEb
 * @subpackage WebI18n
 * @version    $Revision: 168 $
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
 * Gettext / dgetttext wrapper.
 *
 * @param string $msg
 * @param string $domain
 *
 * @return string
 */
function __($msg, $domain = NULL) {
	return ($domain) ? dgettext($domain, $msg) : gettext($msg);
}

/**
 * Ngettext / dngettext wrapper.
 *
 * @param string $msgSingular
 * @param string $msgPlural
 * @param string $msgNumber
 * @param string $domain
 */
function ___($msgSingular, $msgPlural, $msgNumber, $domain) {
	return ($domain) ? dngettext($domain, $msgSingular, $msgPlural, $msgNumber) : ngettext($msgSingular, $msgPlural, $msgNumber);
}
?>