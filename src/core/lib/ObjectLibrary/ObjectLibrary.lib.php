<?php
/**
 * $Id: Object.lib.php 175 2014-04-26 12:02:43Z radu $
 *
 * Object library.
 *
 * All objects extend this library.
 *
 * @author     Radu Maier <radu@webserv.co>
 * @copyright  Copyright (c) 2008 - 2014, Radu Maier
 * @license    http://www.opensource.org/licenses/bsd-license.php
 * @package    WEb
 * @subpackage WebObject
 * @version    $Revision: 175 $
 * @since      Version 5
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
 * Object
 *
 * Object library.
 *
 * @author Radu Maier <radu@webserv.co>
 * @package WEb
 * @subpackage WebObject
 *            
 */
class ObjectLibrary extends Library {

	/**
	 * Object Id.
	 *
	 * If set, object is valid and not temporary.
	 *
	 * @var string
	 */
	protected $_mId;

	/**
	 * User Id
	 *
	 * @var string
	 */
	public $mUserId;

	/**
	 * Object locale.
	 *
	 * @var string
	 */
	public $mI18nLocale;

	/**
	 * Object properties.
	 *
	 * @var array
	 */
	public $mProp = array(
			'list' => NULL,
			'store' => NULL,
			'i18n' => NULL 
	);

	/**
	 * Object tables.
	 *
	 * Child object can override individual options,
	 * except for list.
	 *
	 * @var array
	 */
	protected $_mTable = array(
			'list' => NULL,
			'store' => NULL,
			'i18n' => NULL 
	);

	/**
	 * Primary key
	 *
	 * @var string
	 */
	protected $_mKey;

	/**
	 * Key prefix (use 'bin.' for binary key identification)
	 */
	protected $_mKeyPrefix = NULL;

	/**
	 * Implement GetInstance() method.
	 *
	 * This is required for PHP < 5.3 compatibility (no late static binding)
	 *
	 * @param string $className
	 *        	class name to instantiate
	 *        	
	 * @return object
	 */
	public static function GetInstance($className = __CLASS__) {

		/**
		 * Call parent method with current class name as parameter.
		 */
		return parent::_GetInstance($className);
	}

	protected function _Init() {

		$this->_mKeyPrefix = (1 == Core::Config()->Get('app.database.binkey')) ? 'bin.' : NULL;
	}

	/**
	 * Load configuration file for current library.
	 *
	 * Needs to override the Library method.
	 *
	 * @param string $className
	 *        	name of the class to load configuration for
	 *        	
	 * @return bool
	 */
	protected function _LoadConfig($className) {

		/**
		 * App prefix.
		 */
		$this->_mAppPrefix = Core::Config()->Get('app.prefix');
		
		if (!isset($this->_mTable['list'])) {
			/**
			 *
			 * @var array
			 */
			$this->_mTable['list'] = $this->_mAppPrefix . 'object_' . strtolower($className);
		}
		
		if (!isset($this->_mTable['i18n'])) {
			/**
			 *
			 * @var array
			 */
			$this->_mTable['i18n'] = $this->_mAppPrefix . 'i18n';
		}
		
		/**
		 *
		 * @var string
		 */
		$this->_mKey = $this->_mKeyPrefix . strtolower($className) . '_id';
		
		/**
		 *
		 * @var array
		 */
		$parts = explode('_', $className);
		
		/**
		 *
		 * @var string
		 */
		$object_name = array_key_exists(1, $parts) ? strtolower($parts[1]) : strtolower($parts[0]);
		
		/**
		 *
		 * @var string
		 */
		$path_app = Core::Config()->Get('path.app');
		
		/**
		 *
		 * @var string
		 */
		$cfg_file = $path_app . 'etc/' . Core::Config()->Get('dir.app.obj') . '/' . $object_name . '.cfg.xml';
		
		if (!is_readable($cfg_file)) {
			if (function_exists('__')) {
				/**
				 *
				 * @var string
				 */
				$config_msg = sprintf(__('%s object: Skipping configuration file "%s" (not found).', 'WEb'), $className, $object_name);
				
				Core::Log()->Write($config_msg, 'debug');
			}
			
			return FALSE;
		}
		
		if (function_exists('__')) {
			/**
			 *
			 * @var string
			 */
			$config_msg = sprintf(__('%s object: Loading configuration file "%s".', 'WEb'), $className, $object_name);
			
			Core::Log()->Write($config_msg, 'debug');
		}
		
		return Core::Config()->Set($cfg_file);
	}

	/**
	 * Get object data.
	 *
	 * @param string $id
	 *        	object id
	 *        	
	 * @return boolean
	 */
	public function Get($id) {

		$this->_Init();
		
		/**
		 *
		 * @var array
		 */
		$options['conditions'][] = array(
				$this->_mKey,
				$id 
		);
		
		/**
		 * Check if we have locale properties.
		 */
		if (!empty($this->mProp['i18n'])) {
			/**
			 *
			 * @var array
			 */
			$options['conditions'][] = array(
					'i18n_locale',
					$this->mI18nLocale 
			);
		}
		
		/**
		 * Get data.
		 *
		 * @var mixed
		 */
		$data = Core::Database()->RetrieveOne(array_keys($this->mProp['list']), $this->_mTable['list'], $options);
		
		/**
		 * Check if we have data.
		 */
		if (empty($data)) {
			return FALSE;
		}
		
		/**
		 *
		 * @var array
		 */
		$this->mProp['list'] = $data;
		
		/**
		 * Set object Id.
		 *
		 * @var string
		 */
		$this->_mId = $data[$this->_mKey];
		
		/**
		 * Get locale data.
		 */
		$this->GetLocaleData();
		
		/**
		 * Check if we have an empty store table.
		 */
		if (!empty($this->_mTable['store'])) {
			/**
			 * Get store data.
			 */
			$this->GetStoreData($this->_mId);
		}
		
		return TRUE;
	}

	/**
	 * Get object store data.
	 *
	 * @param misc $id
	 *        	object id
	 *        	
	 * @return mixed
	 */
	public function GetStoreData($id) {

		$this->_Init();
		
		/**
		 * Set table to use.
		 *
		 * @var string
		 */
		$table = (empty($this->_mTable['store'])) ? 'list' : 'store';
		
		/**
		 * Set properties to use.
		 *
		 * @var string
		 */
		$prop = (empty($this->mProp['store'])) ? 'list' : 'store';
		
		/**
		 * Get fields list.
		 *
		 * @var array
		 */
		$fields = array_keys($this->mProp[$prop]);
		
		/**
		 *
		 * @var array
		 */
		$options['conditions'][] = array(
				$this->_mKey,
				$id 
		);
		
		/**
		 * Get data.
		 *
		 * @var mixed
		 */
		$data = Core::Database()->RetrieveOne($fields, $this->_mTable[$table], $options);
		
		/**
		 * Check if we have data.
		 */
		if (!empty($data)) {
			/**
			 *
			 * @var array
			 */
			$this->mProp[$prop] = array_merge($this->mProp[$prop], $data);
		}
		
		return TRUE;
	}

	/**
	 * Get object locale data.
	 *
	 * @param string $i18nLocale        	
	 *
	 * @return mixed
	 */
	public function GetLocaleData($i18nLocale = NULL) {

		$this->_Init();
		
		/**
		 * Check if we have locale properties.
		 */
		if (empty($this->mProp['i18n'])) {
			return FALSE;
		}
		
		/**
		 * Check if we have a valid object.
		 */
		if (empty($this->_mId)) {
			return FALSE;
		}
		
		/**
		 * Check locale
		 */
		if (empty($i18nLocale)) {
			/**
			 *
			 * @var string
			 */
			$i18nLocale = $this->mI18nLocale;
		}
		
		/**
		 *
		 * @var array
		 */
		$options['conditions'][0] = array(
				$this->_mKeyPrefix . 'object_id',
				$this->_mId 
		);
		
		/**
		 *
		 * @var array
		 */
		$options['conditions'][1] = array(
				'i18n_locale',
				$this->mI18nLocale 
		);
		
		foreach ($this->mProp['i18n'] as $prop_key => $prop_value) {
			$options['conditions'][2] = array(
					'i18n_key',
					$prop_key 
			);
			
			/**
			 *
			 * @var array
			 */
			$options['limit'] = 1;
			
			$data = Core::Database()->RetrieveOne('i18n_value', $this->_mTable['i18n'], $options);
			
			$this->mProp['i18n'][$prop_key] = $data['i18n_value'];
		}
		
		return TRUE;
	}

	/**
	 * Add new object data to database.
	 *
	 * This is a wrapper for the functions
	 * to add store data, and locale data.
	 *
	 * You have to make sure you have data
	 * in the corresponding prop array (list or store).
	 *
	 * @return boolean
	 */
	public function Add() {

		$this->_Init();
		
		/**
		 * Add store data.
		 */
		$this->AddStoreData();
		
		/**
		 * Add locale data.
		 */
		$this->AddLocaleData();
		
		/**
		 * Update object.
		 */
		$this->Get($this->_mId);
		
		return TRUE;
	}

	/**
	 * Insert new object data in database.
	 *
	 * This handles only the store props, not also the locale props.
	 *
	 * You have to make sure you have data
	 * in the corresponding prop array (list or store).
	 *
	 * @return boolean
	 */
	public function AddStoreData() {

		$this->_Init();
		
		/**
		 * Set table to use.
		 *
		 * @var string
		 */
		$table = (empty($this->_mTable['store'])) ? 'list' : 'store';
		
		/**
		 * Set properties to use.
		 *
		 * @var string
		 */
		$prop = (empty($this->mProp['store'])) ? 'list' : 'store';
		
		/**
		 * Set the new data Id
		 *
		 * @var array
		 */
		$data[$this->_mKey] = Core::Database()->GenerateId();
		
		/**
		 * Set the rest of the data.
		 *
		 * @var array
		 */
		$data = array_merge($data, $this->mProp[$prop]);
		
		/**
		 * Set the new data owner.
		 *
		 * @var array
		 */
		$data[$this->_mKeyPrefix . 'owner_id'] = $this->mUserId;
		
		/**
		 * Add data to database.
		 */
		if (FALSE !== Core::Database()->Create($data, $this->_mTable[$table])) {
			/**
			 * Set the object id.
			 *
			 * @var string
			 */
			$this->_mId = $data[$this->_mKey];
			
			return TRUE;
		}
		
		return FALSE;
	}

	/**
	 * Insert new locale data in database.
	 *
	 * This handles only the locale props.
	 *
	 * You have to make sure you have data
	 * in the corresponding prop array (list or store).
	 *
	 * @var string $i18nLocale
	 *     
	 * @return boolean
	 */
	public function AddLocaleData($i18nLocale = NULL) {

		$this->_Init();
		
		/**
		 * Check if we have locale properties.
		 */
		if (empty($this->mProp['i18n'])) {
			return FALSE;
		}
		
		/**
		 * Check if we have a valid object.
		 */
		if (empty($this->_mId)) {
			return FALSE;
		}
		
		/**
		 * Check locale
		 */
		if (empty($i18nLocale)) {
			/**
			 *
			 * @var string
			 */
			$i18nLocale = $this->mI18nLocale;
		}
		
		foreach ($this->mProp['i18n'] as $data_key => $data_value) {
			/**
			 * Set data.
			 *
			 * @var array
			 */
			$data_insert = array(
					$this->_mKeyPrefix . 'object_id' => $this->_mId,
					'i18n_locale' => $i18nLocale,
					'i18n_key' => $data_key,
					'i18n_value' => $data_value,
					$this->_mKeyPrefix . 'owner_id' => $this->mUserId 
			);
			
			Core::Database()->Create($data_insert, $this->_mTable['i18n']);
		}
		
		return TRUE;
	}

	/**
	 * Save object details to database.
	 *
	 * This is a wrapper for the functions
	 * to save store data and locale data.
	 *
	 * Use this to store the modified object data.
	 *
	 * @return boolean
	 */
	public function Save() {

		$this->_Init();
		
		/**
		 * Add store data.
		 */
		$this->SaveStoreData();
		
		/**
		 * Add locale data.
		 */
		$this->SaveLocaleData();
		
		/**
		 * Make sure object is updated with all data.
		 */
		$this->Refresh();
		
		return TRUE;
	}

	/**
	 * Save store object data after modifications.
	 *
	 * This handles only the store props, not also the locale props.
	 *
	 * @return boolean
	 */
	public function SaveStoreData() {

		$this->_Init();
		
		/**
		 * Check if we have a valid object.
		 */
		if (empty($this->_mId)) {
			return FALSE;
		}
		
		/**
		 * Set table to use.
		 *
		 * @var string
		 */
		$table = (empty($this->_mTable['store'])) ? 'list' : 'store';
		
		/**
		 * Set properties to use.
		 *
		 * @var string
		 */
		$prop = (empty($this->mProp['store'])) ? 'list' : 'store';
		
		/**
		 * Filter out main key.
		 *
		 * @var unknown_type
		 */
		$data = array_diff_key($this->mProp[$prop], array(
				$this->_mKey => NULL 
		));
		
		$options = array(
				'conditions' => array(
						$this->_mKey,
						$this->_mId 
				),
				'limit' => 1 
		);
		
		Core::Database()->Update($data, $this->_mTable[$table], $options);
		
		return TRUE;
	}

	/**
	 * Save object locale data after modifications.
	 *
	 * This handles only the locale props.
	 *
	 * @param string $i18nLocale        	
	 *
	 * @return boolean
	 */
	public function SaveLocaleData($i18nLocale = NULL) {

		$this->_Init();
		
		/**
		 * Check if we have locale properties.
		 */
		if (empty($this->mProp['i18n'])) {
			return FALSE;
		}
		
		/**
		 * Check if we have a valid object.
		 */
		if (empty($this->_mId)) {
			return FALSE;
		}
		
		/**
		 * Check locale
		 */
		if (empty($i18nLocale)) {
			/**
			 *
			 * @var string
			 */
			$i18nLocale = $this->mI18nLocale;
		}
		
		/**
		 *
		 * @var array
		 */
		$options['conditions'][0] = array(
				$this->_mKeyPrefix . 'object_id',
				$this->_mId 
		);
		
		/**
		 *
		 * @var array
		 */
		$options['conditions'][1] = array(
				'i18n_locale',
				$i18nLocale 
		);
		
		/**
		 *
		 * @var array
		 */
		$options['limit'] = 1;
		
		foreach ($this->mProp['i18n'] as $data_key => $data_value) {
			/**
			 * Set data.
			 *
			 * @var array
			 */
			$data_save = array(
					'i18n_value' => $data_value 
			);
			
			/**
			 *
			 * @var array
			 */
			$options['conditions'][2] = array(
					'i18n_key',
					$data_key 
			);
			
			Core::Database()->Update($data_save, $this->_mTable['i18n'], $options);
		}
		
		return TRUE;
	}

	/**
	 * Save single object data.
	 *
	 * @param string $propType        	
	 * @param string $propName        	
	 * @param string $i18nLocale        	
	 *
	 * @return bool
	 */
	public function SaveOne($propType, $propName, $i18nLocale = NULL) {

		$this->_Init();
		
		if ('i18n' == $propType) {
			return $this->SaveOneLocaleData($propType, $propName, $i18nLocale);
		}
		
		return $this->SaveOneStoreData($propType, $propName);
	}

	/**
	 * Save single object store data.
	 *
	 * @param string $propType        	
	 * @param string $propName        	
	 *
	 * @return bool
	 */
	public function SaveOneStoreData($propType, $propName) {

		$this->_Init();
		
		/**
		 * Check if we have a valid object.
		 */
		if (empty($this->_mId)) {
			return FALSE;
		}
		
		/**
		 * Set table to use.
		 *
		 * @var string
		 */
		$table = (empty($this->_mTable['store'])) ? 'list' : 'store';
		
		/**
		 * Set properties to use.
		 *
		 * @var string
		 */
		$prop = (empty($this->mProp['store'])) ? 'list' : 'store';
		
		/**
		 * Filter out main key.
		 *
		 * @var unknown_type
		 */
		$data = array(
				$propName,
				$this->mProp[$prop][$propName] 
		);
		
		$options = array(
				'conditions' => array(
						$this->_mKey,
						$this->_mId 
				),
				'limit' => 1 
		);
		
		Core::Database()->Update($data, $this->_mTable[$table], $options);
		
		return TRUE;
	}

	/**
	 * Save single object locale data.
	 *
	 * @param string $propType        	
	 * @param string $propName        	
	 * @param string $i18nLocale        	
	 *
	 * @return bool
	 */
	public function SaveOneLocaleData($propType, $propName, $i18nLocale = NULL) {

		$this->_Init();
		
		/**
		 * Check if we have a valid object.
		 */
		if (empty($this->_mId)) {
			return FALSE;
		}
		
		/**
		 * Check if we have locale properties.
		 */
		if (empty($this->mProp['i18n'])) {
			return FALSE;
		}
		
		/**
		 * Check locale
		 */
		if (empty($i18nLocale)) {
			/**
			 *
			 * @var string
			 */
			$i18nLocale = $this->mI18nLocale;
		}
		
		/**
		 *
		 * @var array
		 */
		$options['conditions'][0] = array(
				$this->_mKeyPrefix . 'object_id',
				$this->_mId 
		);
		
		/**
		 *
		 * @var array
		 */
		$options['conditions'][1] = array(
				'i18n_locale',
				$i18nLocale 
		);
		
		/**
		 *
		 * @var array
		 */
		$options['limit'] = 1;
		
		/**
		 * Set data.
		 *
		 * @var array
		 */
		$data_save = array(
				'i18n_value' => $this->mProp['i18n'][$propName] 
		);
		
		/**
		 *
		 * @var array
		 */
		$options['conditions'][2] = array(
				'i18n_key',
				$propName 
		);
		
		Core::Database()->Update($data_save, $this->_mTable['i18n'], $options);
		
		return TRUE;
	}

	/**
	 * Remove object details from database.
	 *
	 * This is a wrapper for the functions
	 * to delete store data and locale data.
	 *
	 * @return boolean
	 */
	public function Remove() {

		$this->_Init();
		
		/**
		 * Add store data.
		 */
		$this->RemoveStoreData();
		
		/**
		 * Add locale data.
		 */
		$this->RemoveLocaleData();
		
		/**
		 * Reset object.
		 */
		$this->Reset();
		
		return TRUE;
	}

	/**
	 * Remove object store details from database.
	 *
	 * @return boolean
	 */
	public function RemoveStoreData() {

		$this->_Init();
		
		/**
		 * Check if we have a valid object.
		 */
		if (empty($this->_mId)) {
			return FALSE;
		}
		
		/**
		 * Set table to use.
		 *
		 * @var string
		 */
		$table = (empty($this->_mTable['store'])) ? 'list' : 'store';
		
		$options = array(
				'conditions' => array(
						$this->_mKey,
						$this->_mId 
				),
				'limit' => 1 
		);
		
		Core::Database()->Delete($this->_mTable[$table], $options);
		
		return TRUE;
	}

	/**
	 * Remove object locale details from database.
	 *
	 * @return boolean
	 */
	public function RemoveLocaleData($i18nLocale = NULL) {

		$this->_Init();
		
		/**
		 * Check if we have locale properties.
		 */
		if (empty($this->mProp['i18n'])) {
			return FALSE;
		}
		
		/**
		 * Check if we have a valid object.
		 */
		if (empty($this->_mId)) {
			return FALSE;
		}
		
		/**
		 *
		 * @var array
		 */
		$options['conditions'][] = array(
				$this->_mKeyPrefix . 'object_id',
				$this->_mId 
		);
		
		if (!empty($i18nLocale)) {
			/**
			 *
			 * @var array
			 */
			$options['conditions'][] = array(
					'i18n_locale',
					$i18nLocale 
			);
		}
		
		Core::Database()->Delete($this->_mTable['i18n'], $options);
		
		return TRUE;
	}

	/**
	 * Refresh object data.
	 *
	 * Object must have been already initialized using the Get method.
	 *
	 * This method simply calls the Get metohd again using the current object Id.
	 *
	 * @return boolean
	 */
	public function Refresh() {

		$this->_Init();
		
		/**
		 * Check if we have a valid object.
		 */
		if (empty($this->_mId)) {
			return FALSE;
		}
		
		$this->Get($this->_mId);
		
		return TRUE;
	}

	/**
	 * Reset object data.
	 *
	 * @return boolean
	 */
	public function Reset() {

		$this->_Init();
		
		/**
		 * Unset object Id.
		 */
		$this->_mId = NULL;
		
		/**
		 *
		 * @var array
		 */
		$newProp = array();
		
		foreach ($this->mProp as $prop_key => $prop_value) {
			/**
			 *
			 * @var array
			 */
			$newProp[$prop_key] = array();
			
			if (is_array($prop_value) && !empty($prop_value)) {
				foreach ($prop_value as $prop_value_key => $prop_value_data) {
					/**
					 *
					 * @var array
					 */
					$newProp[$prop_key][$prop_value_key] = NULL;
				}
			}
		}
		
		/**
		 * Unset object properties.
		 *
		 * @var array
		 */
		$this->mProp = $newProp;
		
		return TRUE;
	}
}
?>