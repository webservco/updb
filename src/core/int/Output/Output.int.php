<?php
/**
 * $Id: Output.int.php 168 2014-04-26 07:28:51Z radu $
 *
 * Output interface.
 *
 * All output libraries will implement it
 *
 * @author     Radu Maier <radu@webserv.co>
 * @copyright  Copyright (c) 2008 - 2014, Radu Maier
 * @license    http://www.opensource.org/licenses/bsd-license.php
 * @package    WEb
 * @subpackage WebOutput
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
 * Interface_Output
 *
 * Output interface.
 *
 * @author     Radu Maier <radu@webserv.co>
 * @package    WEb
 * @subpackage WebOutput
 *
 */
interface Interface_Output {
	/**
	 * Load output engine object.
	 *
	 * @return bool
	 */
	public function Init();

	/**
	 * Output method.
	 *
	 * @param string $outTemplate
	 * @param bool $outDisplay
	 *
	 * @return mixed
	 */
	public function Render($outTemplate = NULL, $outDisplay = FALSE);
}
?>