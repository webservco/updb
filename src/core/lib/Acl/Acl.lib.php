<?php
/**
 * $Id: Acl.lib.php 175 2014-04-26 12:02:43Z radu $
 *
 * ACL library.
 *
 * @author     Radu Maier <radu@webserv.co>
 * @copyright  Copyright (c) 2008 - 2014, Radu Maier
 * @license    http://www.opensource.org/licenses/bsd-license.php
 * @package    WEb
 * @subpackage WebAcl
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
 * Acl
 * ACL library.
 * 
 * @author Radu Maier <radu@webserv.co>
 * @package WEb
 * @subpackage WebAcl
 */
class Acl extends Library {

	/**
	 * ACL tables.
	 * 
	 * @var array
	 */
	protected $_mTable = array();

	/**
	 * Key prefix (use 'bin.' for binary key identification)
	 */
	protected $_mKeyPrefix = NULL;

	/**
	 * Implement GetInstance() method.
	 * This is required for PHP < 5.3 compatibility (no late static binding)
	 * 
	 * @param string $className
	 *        	class name to instantiate
	 * @return object
	 */
	public static function GetInstance($className = __CLASS__) {

		/**
		 * Call parent method with current class name as parameter.
		 */
		return parent::_GetInstance($className);
	}

	private function _Init() {

		if (empty($this->_mTable)) {
			
			$this->_mKeyPrefix = (1 == Core::Config()->Get('app.database.binkey')) ? 'bin.' : NULL;
			
			$this->_mTable['rules'] = "{$this->_mAppPrefix}acl_rules";
			$this->_mTable['groups'] = "{$this->_mAppPrefix}acl_groups";
			$this->_mTable['users'] = "{$this->_mAppPrefix}acl_users";
			$this->_mTable['relations'] = "{$this->_mAppPrefix}acl_relations";
			$this->_mTable['relations_list'] = "{$this->_mAppPrefix}acl_object_relation";
		}
	}

	public function Check($userId, $aclResource, $aclAsset = NULL) {

		$this->_Init();
		
		/**
		 * Initialize result.
		 * We sart with the global deny option.
		 * 
		 * @var boolean
		 */
		$check = FALSE;
		
		/**
		 * Check if we have an asset.
		 */
		if (!empty($aclAsset)) {
			/**
			 * Check if we have a user rule for the asset.
			 */
			$check_user_asset = $this->CheckAcl($userId, $aclResource, 'user', $aclAsset);
			
			if (FALSE !== $check_user_asset) {
				/**
				 * We have a result
				 */
				return ('ALLOW' == $check_user_asset) ? TRUE : FALSE;
			}
		}
		
		/**
		 * Check if we have a user rule for the resource.
		 */
		$check_user_resource = $this->CheckAcl($userId, $aclResource);
		
		if (FALSE !== $check_user_resource) {
			/**
			 * We have a result
			 */
			return ('ALLOW' == $check_user_resource) ? TRUE : FALSE;
		}
		
		/**
		 * If we got this far, we have no specific rule for the user.
		 * So we get the user's relations and search there.
		 * 
		 * @var array
		 */
		$user_relations = $this->GetAclRelations($userId);
		
		/**
		 * Make sure we have relations.
		 */
		if (!empty($user_relations)) {
			/**
			 * Iterate user realtions in search for a matching rule.
			 */
			foreach ($user_relations as $dump => $relation_data) {
				/**
				 * Check if we have an asset.
				 */
				if (!empty($aclAsset)) {
					/**
					 * Check if we have a group rule for the asset.
					 */
					$check_group_asset = $this->CheckAcl($relation_data[$this->_mKeyPrefix . 'group_id'], $aclResource, 'group', $aclAsset);
					
					if (FALSE !== $check_group_asset) {
						/**
						 * We have a result
						 */
						return ('ALLOW' == $check_group_asset) ? TRUE : FALSE;
					}
				}
				
				/**
				 * Check if we have a group rule for the resource.
				 */
				$check_group_resource = $this->CheckAcl($relation_data[$this->_mKeyPrefix . 'group_id'], $aclResource, 'group');
				
				if (FALSE !== $check_group_resource) {
					/**
					 * We have a result only if it's positive.
					 * If we have a deny rule, we will go further.
					 */
					if ('ALLOW' == $check_group_resource) {
						return TRUE;
					}
				}
			}
		}
		
		/**
		 * Check if we have an asset.
		 */
		if (!empty($aclAsset)) {
			/**
			 * If we got this far with no result,
			 * the only chance left is if the user is the owner of the resource.
			 * 
			 * @var string
			 */
			$resource_owner = $this->GetResourceOwner($aclResource, $aclAsset);
			
			if ($userId == $resource_owner) {
				/**
				 * We have a result
				 */
				return TRUE;
			}
		}
		
		/**
		 * Return the default result.
		 */
		return $check;
	}

	/**
	 * Checl ACL.
	 * This is the main method.
	 * It performs a single check and returns
	 * 'DENY', 'ALLOW', or FALSE
	 * 
	 * @param string $aclRequest        	
	 * @param string $aclResource        	
	 * @param string $aclType        	
	 * @param string $aclAsset        	
	 *
	 * @return mixed
	 */
	public function CheckAcl($aclRequest, $aclResource, $aclType = 'user', $aclAsset = NULL) {

		$this->_Init();
		
		/**
		 *
		 * @var array
		 */
		$options['conditions'][] = array(
			'rule_type',
			$aclType
		);
		
		/**
		 *
		 * @var array
		 */
		$options['conditions'][] = array(
			$this->_mKeyPrefix . 'rule_value',
			$aclRequest
		);
		
		/**
		 *
		 * @var array
		 */
		$options['conditions'][] = array(
			'rule_resource',
			$aclResource
		);
		
		if (!empty($aclAsset)) {
			/**
			 *
			 * @var array
			 */
			$options['conditions'][] = array(
				$this->_mKeyPrefix . 'rule_asset',
				$aclAsset
			);
		}
		
		/**
		 *
		 * @var array
		 */
		$options['limit'] = 1;
		
		/**
		 *
		 * @var mixed
		 */
		$data = Core::Database()->RetrieveOne('rule_action', $this->_mTable['rules'], $options);
		
		if (!empty($data)) {
			return $data['rule_action'];
		}
		
		return FALSE;
	}

	public function GetAclRelations($userId = NULL) {

		$this->_Init();
		
		/**
		 *
		 * @var array
		 */
		$values = array(
			$this->_mKeyPrefix . 'group_id',
			'group_name',
			'group_level'
		);
		
		/**
		 *
		 * @var array
		 */
		$options = array();
		
		if (!empty($userId)) {
			/**
			 *
			 * @var array
			 */
			$options['conditions'][] = array(
				$this->_mKeyPrefix . 'user_id',
				$userId
			);
		}
		
		/**
		 *
		 * @var mixed
		 */
		$data = Core::Database()->Retrieve($values, $this->_mTable['relations_list'], $options);
		
		return $data;
	
	}

	/**
	 * Get resource owner
	 * 
	 * @param string $aclResource        	
	 * @param string $aclAsset        	
	 */
	public function GetResourceOwner($aclResource, $aclAsset) {

		$this->_Init();
		
		/**
		 *
		 * @var array
		 */
		$asset = explode('/', $aclResource);
		
		/**
		 *
		 * @var array
		 */
		$asset = array_diff($asset, array(
			''
		));
		
		/**
		 *
		 * @var string
		 */
		$table = strtolower($asset[0]);
		
		/**
		 *
		 * @var string
		 */
		$primary_key = $this->_mKeyPrefix . Core::Database()->GetPrimaryKey($table);
		
		/**
		 *
		 * @var array
		 */
		$options['conditions'][] = array(
			$primary_key,
			$aclAsset
		);
		
		/**
		 *
		 * @var array
		 */
		$options['limit'] = 1;
		
		$data = Core::Database()->RetrieveOne($this->_mKeyPrefix . 'owner_id', $table, $options);
		
		if (empty($data)) {
			return FALSE;
		}
		
		return $data[$this->_mKeyPrefix . 'owner_id'];
	}

	/**
	 * Check relation (userbelongs to a group)
	 * 
	 * @param int $userId        	
	 * @param int $groupId        	
	 * @return boolean
	 */
	public function CheckAclRelation($userId, $groupId) {

		$this->_Init();
		
		$q = "SELECT relation_id FROM {$this->_mTable['relations']} WHERE user_id = '{$userId}' AND group_id = '{$groupId}' LIMIT 1";
		
		$rs = Core::Database()->Query($q);
		
		$t = $rs->num_rows;
		
		return (1 == $t) ? TRUE : FALSE;
	}
}
?>