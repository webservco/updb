<?php
/**
 * $Id: Iceget.lib.php 168 2014-04-26 07:28:51Z radu $
 *
 * Iceget library.
 *
 * Gets stats data from icecast server.
 *
 * @author     Radu Maier <radu@webserv.co>
 * @copyright  Copyright (c) 2008 - 2014, Radu Maier
 * @license    http://www.opensource.org/licenses/bsd-license.php
 * @package    WEb
 * @subpackage WebIceget
 * @version    $Revision: 168 $
 * @since      Version 5
 * @access     public
 * @filesource
 */

/**
 * Security check.
 */
if (!defined('_WEB'))
{
	/**
	 * Stop script execution.
	 */
	exit();
}

/**
 * Iceget
 *
 * Iceget library.
 *
 * @author     Radu Maier <radu@webserv.co>
 * @package    WEb
 * @subpackage WebIceget
 *
 */
class Iceget extends Library
{
	/**
	 * Stores the icecast stats URL.
	 *
	 * @var string
	 */
	private $_mUrl;

	/**
	 * Stores the retreived stats.
	 *
	 * @var data
	 */
	private $_mData = array();

	/**
	 * Flag for server status.
	 *
	 * @var bool
	 */
	private $_mServerDown = FALSE;

	/**
	 * Implement GetInstance() method.
	 *
	 * This is required for PHP < 5.3 compatibility (no late static binding)
	 *
	 * @param string $className class name to instantiate
	 *
	 * @return object
	 */
	public static function GetInstance($className = __CLASS__)
	{
		/**
		 * Call parent method with current class name as parameter.
		 */
		return parent::_GetInstance($className);
	}

	public function SetUrl($url)
	{
		$this->_mUrl = $url;

		return TRUE;
	}

	/**
	 * Get icecast statistics
	 *
	 * @param string $station
	 *
	 * @return array
	 */
	public function Get($station = NULL)
	{
		if (empty($this->_mData) && (TRUE !== $this->_mServerDown))
		{
			$this->_Fetch();
		}

		if (empty($station))
		{
			return $this->_mData;
		}

		if (array_key_exists($station, $this->_mData))
		{
			return $this->_mData[$station];
		}

		Core::Log()->Write(sprintf(__('Iceget Library: Station "%s" is not broadcasting.', 'WEb'), $station), 'warning');

		return array();
	}

	/**
	 * Fetch icecast stats.
	 *
	 * @return bool
	 */
	private function _Fetch()
	{
		if (empty($this->_mUrl))
		{
			Core::Log()->Write(__('Iceget Library: No Icecast stats URL set.', 'WEb'), 'error');

			return FALSE;
		}

		/**
		 * @var mixed
		 */
		$stats_handle = Core::Browser()->Open($this->_mUrl);

		if (FALSE === $stats_handle)
		{
			Core::Log()->Write(__('Iceget Library: Error opening Icecast stats URL.', 'WEb'), 'error');

			return FALSE;
		}

		/**
		 * @var string
		 */
		$stats_string = stream_get_contents($stats_handle);

		/**
		 * @var object
		 */
		$stats_object = simplexml_load_string($stats_string);

		/**
		 * Raw stats data.
		 *
		 * @var array
		 */
		$stats_raw = Core::Config()->Process($stats_object);

		/**
		 * Initialize actual stats data.
		 *
		 * @var array
		 */
		$stats_data = array();

		if (array_key_exists('station', $stats_raw))
		{
			if (array_key_exists(0, $stats_raw['station']))
			{
				foreach ($stats_raw['station'] as $id => $station)
				{
					/**
					 * @var string
					 */
					$index = ltrim($station['mount'], '/');

					/**
					 * @var array
					 */
					$stats_data[$index] = $station;

					if(!empty($stats_data[$index]['title']))
					{
						/**
						 * @var array
						 */
						$stats_data[$index]['title'] = rtrim($stats_data[$index]['title'], ' -');
					}
				}
			}
			else
			{
				/**
				 * @var string
				 */
				$index = ltrim($stats_raw['station']['mount'], '/');

				/**
				 * @var array
				 */
				$stats_data[$index] = $stats_raw['station'];

				if(!empty($stats_data[$index]['title']))
				{
					/**
					 * @var array
					 */
					$stats_data[$index]['title'] = rtrim($stats_data[$index]['title'], ' -');
				}
			}
		}

		if (!empty($stats_data))
		{
			/**
			 * @var aray
			 */
			$this->_mData = $stats_data;

			/**
			 * @var int
			 */
			$stations = count($stats_data);

			/**
			 * @var string
			 */
			$msg = sprintf(	___('Iceget Library: %s station is broadcasting.', 'Iceget Library: %s stations are broadcasting.', $stations, 'WEb'), $stations);

			Core::Log()->Write($msg, 'info');

			return TRUE;
		}

		/**
		 * @var bool
		 */
		$this->_mServerDown = TRUE;

		Core::Log()->Write(__('Iceget Library: No station is broadcasting.', 'WEb'), 'warning');

		return FALSE;
	}
}
?>