<?php
/**
 * @Copyright Copyright (C) 2012 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Company:		Buruj Solutions
 + Contact:		www.burujsolutions.com , info@burujsolutions.com
 * Created on:	May 03, 2012
 ^
 + Project: 	JS Tickets
 ^ 
*/

defined('_JEXEC') or die('Restricted access');

class JSSupportticketModelACL extends JSSupportTicketModel
{
	function __construct() {
		parent::__construct();
	}

	function checkUserPermissionForTask($task,$uid){
		$db = JFactory::getDBO();
		if (is_numeric($uid) == false) return false;
		$query ="select per.id
				FROM `#__js_ticket_acl_permissions` as per
				WHERE per.permission=".$db->Quote($task);
		$db->setQuery($query);
		$permission_id = $db->loadResult();
		if($permission_id){
			$query ="select count(up.id)
					FROM `#__js_ticket_acl_user_permissions` as up
					WHERE up.permissionid=".$permission_id." AND up.uid=".$uid;
			$db->setQuery($query);
			$allow_permission = $db->loadResult();
			if($allow_permission > 0) return true;
			else return false;
		}else{
			return false;	
		}
	}

	function getUserPermissionsByView($uid,$viewName){
		if($viewName){
			switch ($viewName) {
				case 'tickets':$viewgroup = 1; break; 
				case 'user':$viewgroup = 2; break; 
				case 'knowledgebase': $viewgroup = 3; break; 
				case 'faqs': $viewgroup = 4; break; 
				case 'downloads': $viewgroup = 5; break; 
				case 'announcements': $viewgroup = 6; break;
			} 
		}else return false;
		if (is_numeric($uid) == false) return false;
		
		$db = JFactory::getDBO();
		$query = "SELECT per.permission,per.permissiongroup,IF(u_per.permissionid,1,0) AS allow
				  FROM `#__js_ticket_acl_permissions` AS per
				  LEFT JOIN `#__js_ticket_acl_user_permissions` AS u_per
				  ON per.id=u_per.permissionid AND u_per.uid=".$uid;
		$db->setQuery($query);
		$result = $db->loadObjectList();
		$per = array();
		foreach($result AS $res){
			if($viewgroup == $res->permissiongroup)
				$per[$res->permission] = $res->allow;
		}
		return $per;
	}
} ?>