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

class JSSupportticketModelRoles extends JSSupportTicketModel {

    function __construct() {
        parent::__construct();
    }

    function storeRole($data) {
        $user = JSSupportticketCurrentUser::getInstance();
        if(!$user->getIsAdmin()){
            $permission = ($data['id'] == '') ? 'Add Role' : 'Edit Role';
            $per = $user->checkUserPermission($permission);
            if ($per == false) 
                return PERMISSION_ERROR;
        }
        $row = $this->getTable('roles');
        if (!$row->bind($data)) {
            $this->setError($row->getError());
            return SAVE_ERROR;
        }
        if (!$row->check()) {
            $this->setError($row->getError());
            return SAVE_ERROR;
        }
        if (!$row->store()) {
            $this->getJSModel('systemerrors')->updateSystemErrors($row->getError());
            $this->setError($row->getError());
            return SAVE_ERROR;
        }
        $roledepdataArray = array();
        if(!empty($data['roledepdata'])){
            $roledepdataArray = $data['roledepdata'];
        }
        $store_role_departments = $this->getJSModel('roleaccessdepartments')->storeRoleAccessDepartments($roledepdataArray,$row->id);
        if ($store_role_departments == false) {
            $row->delete($row->id);
            return SAVE_ERROR;
        }
        $roleperdataArray = array();
        if(!empty($data['roleperdata'])){
            $roleperdataArray = $data['roleperdata'];
        }
        $store_role_permissons = $this->getJSModel('rolepermissions')->storeRolePermissions($roleperdataArray, $row->id);
        if ($store_role_permissons == false) {
            $row->delete($row->id);
            return SAVE_ERROR;
        }
        JSSupportticketMessage::$recordid = $row->id;
        return SAVED;
    }

    function getAllRoles($search_role, $limitstart, $limit) {
        $db = $this->getDBO();
        $data = array();
        if ($search_role){
            $search_role = trim($search_role);
            $wherequery = " WHERE role.name LIKE " . $db->Quote("%$search_role%");
        }
        $query = "SELECT COUNT(id) FROM `#__js_ticket_acl_roles` AS role ";
        if (isset($wherequery))
            $query.=$wherequery;

        $db->setQuery($query);
        $total = $db->loadResult();
        if ($total <= $limitstart)
            $limitstart = 0;


        $query = "SELECT * FROM `#__js_ticket_acl_roles` AS role ";
        if (isset($wherequery))
            $query.=$wherequery;

        $db->setQuery($query, $limitstart, $limit);
        $result = $db->loadObjectList();
        $data[0] = $result;
        $data[1] = $total;
        if ($search_role)
            $data[2] = $search_role;
        return $data;
    }

    function getRoleForForm($id) {
        $db = $this->getDBO();
        $permission_by_task = array();
        $app = JFactory::getApplication();
        $role = "";
        if($id){
            if (is_numeric($id) == false) return false;
            $query = "SELECT role.*
            FROM `#__js_ticket_acl_roles` AS role
            WHERE role.id = ".$id;
            $db->setQuery($query);
            $role = $db->loadObject();

            $query = "SELECT r_per.permissionid AS rolepermissionid,per.id,per.permission,per.permissiongroup AS pgroup 
            FROM `#__js_ticket_acl_permissions` AS per
                        LEFT JOIN `#__js_ticket_acl_role_permissions` AS r_per ON (r_per.roleid=".$id." AND r_per.permissionid=per.id )
            ORDER BY per.permissiongroup,per.id";
            $db->setQuery($query);
            $permission_role = $db->loadObjectList();

            $query = "SELECT r_da.departmentid AS roledepartmentid,dep.id,dep.departmentname AS name
                        FROM `#__js_ticket_departments` AS dep
                        LEFT JOIN `#__js_ticket_acl_role_access_departments` AS r_da ON (r_da.roleid=".$id." AND r_da.departmentid=dep.id )
                        ORDER BY dep.id";
            $db->setQuery($query);
            $department_role = $db->loadObjectList();            
        }else{
            $query = "SELECT per.id,per.permission,per.permissiongroup AS pgroup
            FROM `#__js_ticket_acl_permissions` AS per
            WHERE per.status= 1 ORDER BY per.permissiongroup,id";
            $db->setQuery($query);
            $permission_role = $db->loadObjectList();

                        $query = "SELECT dep.id,dep.departmentname AS name
            FROM `#__js_ticket_departments` AS dep
            ORDER BY dep.id";
            $db->setQuery($query);
            $department_role = $db->loadObjectList();
        }
        if(!$app->isClient('site')){
            foreach($permission_role AS $roleper){
                    $rolepermissionid="";if(isset($roleper->rolepermissionid)){$rolepermissionid=$roleper->rolepermissionid; } 
                switch($roleper->pgroup){
                    case 1:
                        $permission_by_task['ticket_section'][]=(object) array('id'=>$roleper->id,'permission'=>$roleper->permission,'pgroup'=>$roleper->pgroup,'rolepermissionid'=>$rolepermissionid);
                     break;   
                    case 2:
                        $permission_by_task['staff_section'][]=(object) array('id'=>$roleper->id,'permission'=>$roleper->permission,'pgroup'=>$roleper->pgroup,'rolepermissionid'=>$rolepermissionid);
                     break;   
                    case 3:
                        $permission_by_task['kb_section'][]=(object) array('id'=>$roleper->id,'permission'=>$roleper->permission,'pgroup'=>$roleper->pgroup,'rolepermissionid'=>$rolepermissionid);
                     break;   
                    case 4:
                        $permission_by_task['faq_section'][]=(object) array('id'=>$roleper->id,'permission'=>$roleper->permission,'pgroup'=>$roleper->pgroup,'rolepermissionid'=>$rolepermissionid);
                     break;   
                    case 5:
                        $permission_by_task['download_section'][]=(object) array('id'=>$roleper->id,'permission'=>$roleper->permission,'pgroup'=>$roleper->pgroup,'rolepermissionid'=>$rolepermissionid);
                     break;   
                    case 6:
                        $permission_by_task['announcement_section'][]=(object) array('id'=>$roleper->id,'permission'=>$roleper->permission,'pgroup'=>$roleper->pgroup,'rolepermissionid'=>$rolepermissionid);
                     break;
                    case 7:
                        $permission_by_task['mail_section'][]=(object) array('id'=>$roleper->id,'permission'=>$roleper->permission,'pgroup'=>$roleper->pgroup,'rolepermissionid'=>$rolepermissionid);
                     break;
                }
            }
            $result[3] = $permission_by_task;
        }        
        $result[0] = $role;
        $result[1] = $permission_role;
        $result[2] = $department_role;
        
        return $result;
    }

    function deleteRoleAdmin() {
        $cids = JFactory::getApplication()->input->get('cid', array(0), null, 'array');
        $row = $this->getTable('roles');
        $deleteall = 1;
        foreach ($cids as $cid) {
            if ($this->roleCanDelete($cid) == true) {
                if (!$row->delete($cid)) {
                    $this->getJSModel('systemerrors')->updateSystemErrors($row->getErrorMsg());
                    $this->setError($row->getErrorMsg());
                    return DELETE_ERROR;
                }
                $d_r_p = $this->getJSModel('rolepermissions')->deleteRolePermissions($cid);
                if ($d_r_p == false)
                    return DELETE_ERROR;
            } else
                $deleteall++;
        }
        if($deleteall == 1){
            return DELETED;
        }else{
            $deleteall = $deleteall-1;
            JSSupportticketMessage::$recordid = $deleteall;
            return DELETE_ERROR;
        }
    }

    function deleteRole($id){
        if(!is_numeric($id)) return false;
        $user = JSSupportticketCurrentUser::getInstance();
        $per = $user->checkUserPermission('Delete Role');
        if ($per == false) 
            return PERMISSION_ERROR;
        $row = $this->getTable('roles');
        if($this->roleCanDelete($id) == true){
            if (!$row->delete($id)){
                $this->setError($row->getErrorMsg());
                return DELETE_ERROR;
            }
            $d_r_p=$this->getJSModel('rolepermissions')->deleteRolePermissions($id);
            if($d_r_p==false) return DELETE_ERROR;
            else return DELETED;
        }else{ return IN_USE; }
        
    }

    function roleCanDelete($id) {//staffMemberCanDelete
        if (is_numeric($id) == false)
            return false;
        $db = $this->getDBO();
        $query = "SELECT( 
                    (SELECT COUNT(id) FROM `#__js_ticket_acl_user_access_departments` WHERE roleid = " . $id . ")   
                    + 
                    (SELECT COUNT(id) FROM `#__js_ticket_acl_user_permissions` WHERE roleid = " . $id . ")   
                    ) AS total";
        $db->setQuery($query);
        $total = $db->loadResult();
        if ($total > 0)
            return false;
        else
            return true;
    }

    function getRoles($title) {
        $db = JFactory::getDBO();
        $query = "SELECT  id, name FROM `#__js_ticket_acl_roles` WHERE status = 1 ORDER BY name ASC ";
        try{
            $db->setQuery($query);
            $rows = $db->loadObjectList();
            $role = array();
            if ($title)
                $role[] = array('value' => '', 'text' => $title);
            foreach ($rows as $row) {
                $role[] = array('value' => $row->id, 'text' => JText::_($row->name));
            }
            return $role;
        }
        catch (RuntimeException $e){
            echo $db->stderr();
            return false;
        }
        
    }

}
