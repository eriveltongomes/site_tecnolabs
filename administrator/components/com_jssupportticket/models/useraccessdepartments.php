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


class JSSupportticketModelUserAccessDepartments extends JSSupportTicketModel{
	
    function __construct(){
		parent::__construct();
	}

    function storeUserAccessDepartments($userdepartmentaccess,$uid,$roleid,$staffid){
        $db = JFactory::getDBO();
        if (!is_numeric($roleid)) return false;
        if (!is_numeric($staffid)) return false;
        if (!is_numeric($uid)) return false;
    	$row = $this->getTable('useraccessdepartments');
        $new_departments=array();
        $query = "SELECT departmentid,roleid,uid FROM `#__js_ticket_acl_user_access_departments` WHERE staffid=".$staffid;
        $db->setQuery($query);
        $old_departments= $db->loadObjectList();
        foreach ($userdepartmentaccess AS $key=>$value) {
            $new_departments[] = $value;
        }
        $error = array();
        foreach ($old_departments AS $olddepid){
            $match = false;
            foreach ($new_departments AS $depid) {
                if ($olddepid->departmentid == $depid && $olddepid->roleid==$roleid && $olddepid->uid==$uid) {
                    $match = true;
                    break;
                }
            }
            if ($match == false){
                $query = "DELETE FROM `#__js_ticket_acl_user_access_departments` WHERE departmentid=" . $olddepid->departmentid." AND staffid=".$staffid;
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
                if ($olddepid->departmentid == $depid && $olddepid->roleid==$roleid && $olddepid->uid==$uid) {
                    $insert = false;
                    break;
                }
            }
            if ($insert) {
                $row->id = "";
                $row->uid= $uid;
                $row->roleid = $roleid;
                $row->staffid = $staffid;
                $row->departmentid = $depid;
                $row->status= 1;
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
