<?php

/**
 * @Copyright Copyright (C) 2012 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Company:		Buruj Solutions
  + Contact:		www.burujsolutions.com , info@burujsolutions.com
 * Created on:	March 04, 2014
  ^
  + Project: 	JS Tickets
  ^
 */
defined('_JEXEC') or die('Not Allowed');

jimport('joomla.application.component.model');
jimport('joomla.html.html');

class JSSupportticketModelUserPermissions extends JSSupportTicketModel {

    function __construct() {
        parent::__construct();
    }

    //copy from front end
    function storeUserPermissions($userpermissions,$uid,$roleid,$staffid){
            $db = JFactory::getDBO();
            if (!is_numeric($roleid)) return false;
            if (!is_numeric($staffid)) return false;
            if (!is_numeric($uid)) return false;
            $row = $this->getTable('userpermissions');
            $new_permissions=array();
            $query = "SELECT permissionid,roleid,uid FROM `#__js_ticket_acl_user_permissions` WHERE staffid=".$staffid;
            $db->setQuery($query);
            $old_permissions= $db->loadObjectList();
            foreach ($userpermissions AS $key=>$value) {
                $new_permissions[] = $value;
            }
            $error = array();
            foreach ($old_permissions AS $oldperid) {
                $match = false;
                foreach ($new_permissions AS $perid) {
                    if ($oldperid->permissionid == $perid && $oldperid->roleid==$roleid && $oldperid->uid==$uid) {
                        $match = true;
                        break;
                    }
                }
                if ($match == false) {
                    $query = "DELETE FROM `#__js_ticket_acl_user_permissions` WHERE permissionid=" . $oldperid->permissionid." AND staffid=".$staffid ;
                    $db->setQuery($query);
                    if (!$db->execute()) {
                        $err = $this->setError($row->getError());
                        $error[] = $err;
                    }
                }
            }

            foreach ($new_permissions AS $perid) {
                $insert = true;
                foreach ($old_permissions AS $oldperid) {
                    if ($oldperid->permissionid == $perid && $oldperid->roleid==$roleid && $oldperid->uid==$uid ) {
                        $insert = false;
                        break;
                    }
                }
                if ($insert) {
                    $row->id = "";
                    $row->uid = $uid;
                    $row->roleid = $roleid;
                    $row->staffid = $staffid;
                    $row->permissionid = $perid;
                    $row->grant = 1;
                    $row->status=1;
                    if (!$row->store()) {
                        $err = $this->setError($row->getError());
                        $error[] = $err;
                    }
                }
            }

            if (!empty($error)) return false;
            
            return true;
    }
    function getUserPermissions($staffid){
        $permission_by_task=array();
        $db = $this->getDBO();
        if (is_numeric($staffid) == false) return false;
            $query = "SELECT u_per.permissionid AS userpermissionid,per.id,per.permission,per.permissiongroup AS pgroup 
                        FROM `#__js_ticket_acl_permissions` AS per
                        LEFT JOIN `#__js_ticket_acl_user_permissions` AS u_per ON (u_per.staffid=".$staffid." AND u_per.permissionid=per.id )
                        ORDER BY per.permissiongroup,per.id";
            $db->setQuery($query);
            $permission_user = $db->loadObjectList();

            $query = "SELECT u_da.departmentid AS userdepartmentid,dep.id,dep.departmentname AS name
                        FROM `#__js_ticket_departments` AS dep
                        LEFT JOIN `#__js_ticket_acl_user_access_departments` AS u_da ON (u_da.staffid=".$staffid." AND u_da.departmentid=dep.id )
                        ORDER BY dep.id";
            $db->setQuery($query);
            $department_user = $db->loadObjectList();
                        foreach($permission_user AS $roleper){
                            switch($roleper->pgroup){
                                case 1:
                                    $permission_by_task['ticket_section'][]=(object) array('id'=>$roleper->id,'permission'=>$roleper->permission,'pgroup'=>$roleper->pgroup,'userpermissionid'=>$roleper->userpermissionid);
                                 break;   
                                case 2:
                                    $permission_by_task['staff_section'][]=(object) array('id'=>$roleper->id,'permission'=>$roleper->permission,'pgroup'=>$roleper->pgroup,'userpermissionid'=>$roleper->userpermissionid);
                                 break;   
                                case 3:
                                    $permission_by_task['kb_section'][]=(object) array('id'=>$roleper->id,'permission'=>$roleper->permission,'pgroup'=>$roleper->pgroup,'userpermissionid'=>$roleper->userpermissionid);
                                 break;   
                                case 4:
                                    $permission_by_task['faq_section'][]=(object) array('id'=>$roleper->id,'permission'=>$roleper->permission,'pgroup'=>$roleper->pgroup,'userpermissionid'=>$roleper->userpermissionid);
                                 break;   
                                case 5:
                                    $permission_by_task['download_section'][]=(object) array('id'=>$roleper->id,'permission'=>$roleper->permission,'pgroup'=>$roleper->pgroup,'userpermissionid'=>$roleper->userpermissionid);
                                 break;   
                                case 6:
                                    $permission_by_task['announcement_section'][]=(object) array('id'=>$roleper->id,'permission'=>$roleper->permission,'pgroup'=>$roleper->pgroup,'userpermissionid'=>$roleper->userpermissionid);
                                 break;   
                                case 7:
                                    $permission_by_task['mail_section'][]=(object) array('id'=>$roleper->id,'permission'=>$roleper->permission,'pgroup'=>$roleper->pgroup,'userpermissionid'=>$roleper->userpermissionid);
                                 break;   
                            }
                        }
                        
            $result[1] = $permission_user;
            $result[2] = $department_user;
            $result[3] = $permission_by_task;
                
            return $result;
        
    }

    function deleteUserPermissions($staffid){
        if(!is_numeric($staffid)) return false;
        $db = $this->getDBO();
        $query = "DELETE FROM `#__js_ticket_acl_user_access_departments` WHERE staffid = " . $staffid  ;
        $db->setQuery($query);
        if (!$db->execute()) {
            $err = $this->setError($row->getError());
            return false;
        }
        $query = "DELETE FROM `#__js_ticket_acl_user_permissions` WHERE staffid  = " . $staffid  ;
        $db->setQuery($query);
        if (!$db->execute()) {
            $err = $this->setError($row->getError());
            return false;
        }
        return true;
    }
    //end frontend function

}
