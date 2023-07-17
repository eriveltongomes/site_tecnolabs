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

defined('_JEXEC') or die('Not Allowed');
jimport('joomla.application.component.model');
jimport('joomla.html.html');


class JSSupportticketModelRoleAccessDepartments extends JSSupportTicketModel
{
	function __construct(){
		parent::__construct();
	}
	function storeRoleAccessDepartments($departmentaccess,$roleid){
		$db = JFactory::getDBO();
		if (!is_numeric($roleid)) return false;
		$row = $this->getTable('roleaccessdepartments');
		$new_departments=array();
		$query = "SELECT departmentid FROM `#__js_ticket_acl_role_access_departments` WHERE roleid = " . $roleid;
		$db->setQuery($query);
		$old_departments = $db->loadObjectList();
		foreach ($departmentaccess AS $key=>$value) {
			$new_departments[] = $value;
		}
		$error = array();
		foreach ($old_departments AS $olddepid) {
			$match = false;
			foreach ($new_departments AS $depid) {
				if ($olddepid->departmentid == $depid) {
					$match = true;
					break;
				}
			}
			if ($match == false) {
				$query = "DELETE FROM `#__js_ticket_acl_role_access_departments` WHERE roleid = " . $roleid . " AND departmentid=" . $olddepid->departmentid;
				$db->setQuery($query);
				if (!$db->execute()) {
					$err = $this->setError($row->getError());
					$error[] = $err;
				}
			}
		}
	
		foreach ($new_departments AS $depid) {
			$insert = true;
			foreach ($old_departments AS $olddepid) {
				if ($olddepid->departmentid == $depid) {
					$insert = false;
					break;
				}
			}
			if ($insert) {
				$row->id = "";
				$row->roleid = $roleid;
				$row->departmentid = $depid;
				$row->status= 1;
				$row->created=date('Y-m-d H:i:s');
				if (!$row->store()) {
					$err = $this->setError($row->getError());
					$error[] = $err;
				}
			}
		}
		if (!empty($error)) return false;
		
		return true;
	}
}
?>
