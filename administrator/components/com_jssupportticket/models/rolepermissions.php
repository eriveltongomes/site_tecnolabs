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

class JSSupportticketModelRolePermissions extends JSSupportTicketModel {

    function __construct() {
        parent::__construct();
    }

    function storeRolePermissions($permissions,$roleid){
        $db = JFactory::getDBO();
        if (!is_numeric($roleid)) return false;
        $row = $this->getTable('rolepermissions');
        $new_permissions=array();
        $query = "SELECT permissionid FROM `#__js_ticket_acl_role_permissions` WHERE roleid = " . $roleid;
        $db->setQuery($query);
        $old_permissions= $db->loadObjectList();
        foreach ($permissions AS $key=>$value) {
            $new_permissions[] = $value;
        }
                    
        $error = array();
        foreach ($old_permissions AS $oldperid) {
            $match = false;
            foreach ($new_permissions AS $perid) {
                if ($oldperid->permissionid == $perid) {
                    $match = true;
                    break;
                }
            }
            if ($match == false) {
                $query = "DELETE FROM `#__js_ticket_acl_role_permissions` WHERE roleid = " . $roleid . " AND permissionid=" . $oldperid->permissionid;
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
                if ($oldperid->permissionid == $perid) {
                    $insert = false;
                    break;
                }
            }
            if ($insert) {
                $row->id = "";
                $row->roleid = $roleid;
                $row->permissionid = $perid;
                $row->grant = 1;
                $row->status=1;
                if (!$row->store()) {
                    $err = $this->setError($row->getError());
                    $error[] = $err;
                }
            }
        }
        if (!empty($error)) 
            return false;
        else
            return true;
    }
    
    function getRolePermissionsAdmin($roleid) {
        $db = $this->getDBO();
        if (is_numeric($roleid) == false)
            return false;

        $query = "SELECT r_per.permissionid AS rolepermissionid,per.id,per.permission,per.permissiongroup AS pgroup 
						FROM `#__js_ticket_acl_permissions` AS per
                        LEFT JOIN `#__js_ticket_acl_role_permissions` AS r_per ON (r_per.roleid=" . $roleid . " AND r_per.permissionid=per.id )
					ORDER BY per.permissiongroup,per.id";
        $db->setQuery($query);
        $permission_role = $db->loadObjectList();

        $query = "SELECT r_da.departmentid AS roledepartmentid,dep.id,dep.departmentname AS name
						FROM `#__js_ticket_departments` AS dep
						LEFT JOIN `#__js_ticket_acl_role_access_departments` AS r_da ON (r_da.roleid=" . $roleid . " AND r_da.departmentid=dep.id )
						ORDER BY dep.id";
        $db->setQuery($query);
        $department_role = $db->loadObjectList();


        $result[1] = $permission_role;
        $result[2] = $department_role;

        return $result;
    }

    function getRolePermissionsAdminAjax($roleid) {
        $db = $this->getDBO();
        if (is_numeric($roleid) == false)
            return false;
        $result = $this->getRolePermissionsAdmin($roleid);
        $deptext = JText::_("Department section");
        $depid = "rad_alldepartmentaccess";
        $depclass = "rad_departmentaccess";

        $return_value = "<div class='js-col-md-12'><div class='js-per-subheading'>";
        $return_value .= "<span class='head-text'>".$deptext."</span>";
        $return_value .= "<span class='head-checkbox'>";
        $dcheck = (!$roleid) ? 'checked=checked' : '';
        $return_value .= "<input type='checkbox' id='$depid' $dcheck onclick=selectdeseletsection('$depid','$depclass') /><label for='$depid'>" . JText::_('Select / Deselect All') . "</label>\n";
        $return_value .= "</span></div>";
        $return_value .= "<div class='js-per-wrapper'>";
        foreach ($result[2] AS $dep) {
            $return_value .= "<div class='js-col-md-4 js-per-datawrapper'><div class='js-per-data'>";
            $dchecked_or_not = "";
            if ($roleid) {  //default role permission case 
                if (isset($dep->roledepartmentid)) {
                    $dchecked_or_not = ($dep->roledepartmentid == $dep->id) ? "checked=checked" : "";
                }
            } else {
                $dchecked_or_not = "checked=checked";
            }
            $return_value .= "<input type='checkbox' id='roledepdata_$dep->name' class='$depclass' name='roledepdata[$dep->name]' value='$dep->id'  $dchecked_or_not/>";
            $return_value .= "<label for='roledepdata_$dep->name'>" . JText::_($dep->name) . "</label>\n";
            $return_value .= "</div></div>";
        }
        $return_value .= "</div>";
        $pgroup = "";
        foreach ($result[1] AS $per) {
            if ($pgroup != $per->pgroup) {
                $pgroup = $per->pgroup;
                switch ($pgroup) {
                    case 1:
                        $text = JText::_('Ticket section');
                        $id = "t_s_allrolepermision";
                        $class = "t_s_rolepermission";
                        $section = 'ticke';
                        break;
                    case 2:
                        $text = JText::_('Staff section');
                        $id = "s_s_allrolepermision";
                        $class = "s_s_rolepermission";
                        $section = 'staff';
                        break;
                    case 3:
                        $text = JText::_('Knowledge base section');
                        $id = "kb_s_allrolepermision";
                        $class = "kb_s_rolepermission";
                        $section = 'kb';
                        break;
                    case 4:
                        $text = JText::_('FAQ section');
                        $id = "f_s_allrolepermision";
                        $class = "f_s_rolepermission";
                        $section = 'faqs';
                        break;
                    case 5:
                        $text = JText::_('Download section');
                        $id = "d_s_allrolepermision";
                        $class = "d_s_rolepermission";
                        $section = 'downloads';
                        break;
                    case 6:
                        $text = JText::_('Announcement section');
                        $id = "a_s_allrolepermision";
                        $class = "a_s_rolepermission";
                        $section = 'announcement';
                        break;
                    case 7:
                        $text = JText::_('Mail section');
                        $id = "ms_s_allrolepermision";
                        $class = "ms_s_rolepermission";
                        $section = 'mail';
                        break;
                }
                $return_value .= "<div class='js-per-subheading'>";
                $return_value .= "<span class='head-text'>".$text."</span>";
                $return_value .= "<span class='head-checkbox'>";
                $rchecked = (!$roleid) ? 'checked=checked' : '';
                $return_value .= "<input  type='checkbox' id='$id' $rchecked onclick=selectdeseletsection('$id','$class') /><label for='$id'> " . JText::_('Select / Deselect All') . "</label>\n";
                $return_value .= "</span></div>";
            }

            $return_value .= "<div class='js-per-wrapper'>";
                    $return_value .= "<div class='js-col-md-4 js-per-datawrapper'><div class='js-per-data'>";
                $checked_or_not = "";
                if ($roleid) {  //default role permissions 
                    if (isset($per->rolepermissionid)) {
                        $checked_or_not = ($per->rolepermissionid == $per->id) ? "checked='checked'" : "";
                    }
                } else { //add case
                    $checked_or_not = "checked='checked'";
                }
                $ch_id = $per->permission . '_' . $section;

                $return_value .= "<input type='checkbox' id='$ch_id'  class='$class' name='roleperdata[$per->permission]' value='$per->id' $checked_or_not />\n";
                $return_value .= "<label for='$ch_id'>" . JText::_($per->permission) . "</label>\n";
                $return_value .= "</div></div>";
            $return_value .= "</div>";
        }
        $return_value .= "</div><div id='js-tk-per-ajax-bottom-border'></div>";
        return $return_value;
    }

    function getRolePermissions($roleid){
        $permission_by_task=array();
        $db = $this->getDBO();
        if (is_numeric($roleid) == false) return false;

            $query = "SELECT r_per.permissionid AS rolepermissionid,per.id,per.permission,per.permissiongroup AS pgroup 
                                    FROM `#__js_ticket_acl_permissions` AS per
            LEFT JOIN `#__js_ticket_acl_role_permissions` AS r_per ON (r_per.roleid=".$roleid." AND r_per.permissionid=per.id )
                            ORDER BY per.permissiongroup,per.id";
            $db->setQuery($query);
            $permission_role = $db->loadObjectList();

            $query = "SELECT r_da.departmentid AS roledepartmentid,dep.id,dep.departmentname AS name
                                    FROM `#__js_ticket_departments` AS dep
                                    LEFT JOIN `#__js_ticket_acl_role_access_departments` AS r_da ON (r_da.roleid=".$roleid." AND r_da.departmentid=dep.id )
                                    ORDER BY dep.id";
            $db->setQuery($query);
            $department_role = $db->loadObjectList();


            foreach($permission_role AS $roleper){
                switch($roleper->pgroup){
                    case 1:
                        $permission_by_task['ticket_section'][]=(object) array('id'=>$roleper->id,'permission'=>$roleper->permission,'pgroup'=>$roleper->pgroup,'rolepermissionid'=>$roleper->rolepermissionid);
                     break;   
                    case 2:
                        $permission_by_task['staff_section'][]=(object) array('id'=>$roleper->id,'permission'=>$roleper->permission,'pgroup'=>$roleper->pgroup,'rolepermissionid'=>$roleper->rolepermissionid);
                     break;   
                    case 3:
                        $permission_by_task['kb_section'][]=(object) array('id'=>$roleper->id,'permission'=>$roleper->permission,'pgroup'=>$roleper->pgroup,'rolepermissionid'=>$roleper->rolepermissionid);
                     break;   
                    case 4:
                        $permission_by_task['faq_section'][]=(object) array('id'=>$roleper->id,'permission'=>$roleper->permission,'pgroup'=>$roleper->pgroup,'rolepermissionid'=>$roleper->rolepermissionid);
                     break;   
                    case 5:
                        $permission_by_task['download_section'][]=(object) array('id'=>$roleper->id,'permission'=>$roleper->permission,'pgroup'=>$roleper->pgroup,'rolepermissionid'=>$roleper->rolepermissionid);
                     break;   
                    case 6:
                        $permission_by_task['announcement_section'][]=(object) array('id'=>$roleper->id,'permission'=>$roleper->permission,'pgroup'=>$roleper->pgroup,'rolepermissionid'=>$roleper->rolepermissionid);
                     break;   
                    case 7:
                        $permission_by_task['mail_section'][]=(object) array('id'=>$roleper->id,'permission'=>$roleper->permission,'pgroup'=>$roleper->pgroup,'rolepermissionid'=>$roleper->rolepermissionid);
                     break;   
                }
            }
        $result[1] = $permission_role;
        $result[2] = $department_role;
        $result[3] = $permission_by_task;
        return $result;
    }
    
    function getRolePermissionsAjax($roleid,$c_p_grant){
        if (!is_numeric($roleid)) return false;
        $db = $this->getDBO();
        $result=$this->getRolePermissions($roleid);
        
        $deptext=JText::_("Department Section");
        $depid="rad_alldepartmentaccess";
        $depclass="rad_departmentaccess";
        
        $change_permission_grant = ($c_p_grant == 1) ? '' : 'disabled=disabled';
        
        $return_value = "<div class='js-per-wrapper'>";
        if(isset($result[2]) && is_array($result[2])){
            $return_value .= "<div class='js-per-subheading'>";
            $return_value .= "<span class='head-text'>".$deptext."</span>";
            $return_value .= "<span class='head-checkbox'>";
            $dcheck=(!$roleid)?'checked=checked':''; 
            $return_value .= "<input type='checkbox' $change_permission_grant id='$depid' $dcheck onclick=selectdeseletsection('$depid','$depclass') /><label for='$depid'>".JText::_('Select / Deselect All')."</label>\n";
            $return_value .= "</span>";
            $return_value .= "</div>";
           
            
            foreach($result[2] AS $dep){
                    $return_value .= "<div class='js-col-md-4 js-per-datawrapper'><div class='js-per-data'>";
                $dchecked_or_not="";
                if($roleid) {  //default role permission case 
                    if(isset($dep->roledepartmentid)) { 
                        $dchecked_or_not=($dep->roledepartmentid==$dep->id) ?  "checked=checked" : "";  
                    }
                 }else{  
                    $dchecked_or_not="checked=checked" ;
                 } 
                $return_value .= "<input type='checkbox' $change_permission_grant id='roledepdata_$dep->name'  class='$depclass' name='roledepdata[$dep->name]' value='$dep->id'  $dchecked_or_not/>";          
                $return_value .= "<label for='roledepdata_$dep->name'>".JText::_($dep->name)."</label>";
                $return_value .= "</div></div>";
                
            }
        } 
        if(isset($result[3]) && is_array($result[3])){
            $return_value .= "<div class='js-per-wrapper'>";
                $permission_keys=  array_keys($result[3]);
                foreach($permission_keys AS $permissin_by_section){
                    switch($permissin_by_section){
                        case 'ticket_section';
                            $text=JText::_('Ticket section');
                            $id="t_s_allrolepermision";
                            $class="t_s_rolepermission";
                            $section='ticke';
                        break;
                        case 'staff_section';
                            $text=JText::_('Staff section');
                            $id="s_s_allrolepermision";
                            $class="s_s_rolepermission";
                            $section='staff';

                        break;
                        case 'kb_section';
                            $text=JText::_('Knowledge base section');
                            $id="kb_s_allrolepermision";
                            $class="kb_s_rolepermission";
                            $section='kb';

                        break;
                        case 'faq_section';
                            $text=JText::_('FAQ section');
                            $id="f_s_allrolepermision";
                            $class="f_s_rolepermission";
                            $section='faqs';

                        break;
                        case 'download_section';
                            $text=JText::_('Download section');
                            $id="d_s_allrolepermision";
                            $class="d_s_rolepermission";
                            $section='downloads';

                        break;
                        case 'announcement_section';
                            $text=JText::_('Announcement section');
                            $id="a_s_allrolepermision";
                            $class="a_s_rolepermission";
                            $section='announcement';
                        break;
                        case 'mail_section';
                            $text=JText::_('Mail section');
                            $id="m_s_allrolepermision";
                            $class="m_s_rolepermission";
                            $section='mail';
                        break;
                    }
                    $return_value .= "<div class='js-per-subheading'>";
                    $return_value .= "<span class='head-text'>".$text."</span>";
                    $return_value .= "<span class='head-checkbox'>";
                    $rchecked=(!$roleid) ? 'checked=checked':'' ;
                    $return_value .= "<input  $change_permission_grant type='checkbox' id='$id' $rchecked onclick=selectdeseletsection('$id','$class') /> <label for='$id'>".JText::_('Select / Deselect All')."</label>\n";
                    $return_value .= "</span>";
                    $return_value .= "</div>";
                    
                foreach($result[3][$permissin_by_section] AS $per){
                            $checked_or_not=""; 
                    $return_value .= "<div class='js-col-md-4 js-per-datawrapper'><div class='js-per-data'>";
                    if($roleid) {  //default role permissions 
                        if(isset($per->rolepermissionid)) { $checked_or_not=($per->rolepermissionid==$per->id) ?  "checked='checked'" : "";  }
                    }else{ //add case
                        $checked_or_not="checked='checked'" ;
                    } 
                    $ch_id=$per->permission.'_'.$section;
                    $return_value .= "<input type='checkbox' $change_permission_grant id='$ch_id'  class='$class' name='roleperdata[$per->permission]' value='$per->id' $checked_or_not />\n";
                    $return_value .= "<label for='$ch_id'>".JText::_($per->permission)."</label>\n";
                    $return_value .= "</div></div>";

                }
            }
            $return_value .= "</div>";
            $return_value .= "<div id='js-tk-per-ajax-bottom-border'></div>";
        } 
        return $return_value;
    }

    function deleteRolePermissions($roleid) {
        if(!is_numeric($roleid)) return false;
        $db = $this->getDBO();
        $query = "DELETE FROM `#__js_ticket_acl_role_access_departments` WHERE roleid = ".$roleid;
        $db->setQuery($query);
        if (!$db->execute()) {
            $err = $this->setError($row->getError());
            return false;
        }
        $query = "DELETE FROM `#__js_ticket_acl_role_permissions` WHERE roleid = " . $roleid;
        $db->setQuery($query);
        if (!$db->execute()) {
            $err = $this->setError($row->getError());
            return false;
        }
        return true;
    }
}
