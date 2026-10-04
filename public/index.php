<?php
/**
 * $Id$
 *
 * Application index.
 *
 * @author     Radu Maier <radu@webserv.co>
 * @copyright  Copyright (c) 2013, Radu Maier
 * @license    http://www.opensource.org/licenses/bsd-license.php
 * @package    UpdbApp
 * @subpackage Index
 * @version    $Revision$
 * @since      Version 1
 * @access     public
 * @filesource
 */

/**
 * Check if basic configuration files are accessible.
 */
if (!is_readable('./etc/settings.cfg.xml') || !is_readable('./etc/.env')) {
	/**
	 * Trigger fatal error.
	 */
	die('<html><!-- Fatal: Configuration file is missing. --></html>');
}

/**
 * @var object
 */
$settings = simplexml_load_file('./etc/settings.cfg.xml');

/**
 * @var string
*/
$env = trim(file_get_contents('./etc/.env'));

/**
 * @var string
*/
$cfg['path_core'] = ($settings->$env->path->core) ? trim($settings->$env->path->core) : './sys/';

/**
 * Security token.
 */
define('_WEB', TRUE);

/**
 * Check if PHP version is less than 5.3
*/
if (FALSE != version_compare(PHP_VERSION, '5.3.0', '<')) {
	/**
	 * Load compatibility library.
	 */
	require $cfg['path_core'] . 'lib/Core/CoreUtils.lib.php';
}

/**
 * Load core and configuration libraries.
 */
require $cfg['path_core'] . 'lib/Core/Core.lib.php';
require $cfg['path_core'] . 'lib/Library/Library.lib.php';
require $cfg['path_core'] . 'lib/Config/Config.lib.php';

/**
 * Set environment.
 */
Core::Config()->SetEnv($env);

/**
 * Set paths.
*/
Core::Config()->Set($settings);

/**
 * Register class autoload method.
*/
spl_autoload_register(array('Core', 'Load'));

/**
 * Boot application.
*/
Core::App()->Boot();

/**
 * Run application.
*/
Core::App()->Run();

/**
 * Shutdown application.
*/
Core::App()->Shutdown();

exit();
?>