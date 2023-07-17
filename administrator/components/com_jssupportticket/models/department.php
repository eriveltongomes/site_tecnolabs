<?php
/**
 * @Copyright Copyright (C) 2015 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Company:		Buruj Solutions
 + Contact:		www.burujsolutions.com , info@burujsolutions.com
 * Created on:	May 22, 2015
  ^
  + Project: 	JS Tickets
  ^
 */
defined('_JEXEC') or die('Not Allowed');

jimport('joomla.application.component.model');
jimport('joomla.html.html');

class JSSupportticketModelDepartment extends JSSupportTicketModel {

    function __construct() {
        parent::__construct();
    }

    function getAllDepartments($searchdepartment,$limitstart,$limit){//$searchdepartment, $searchtype,$limitstart,$limit
       /* $type[] = array('value'=>'' ,'text'=>  JText::_('Select type'));
        $type[] = array('value'=>1 ,'text'=>  JText::_('Public'));
        $type[] = array('value'=>0 ,'text'=>  JText::_('Private'));
        $lists['type'] = JHTML::_('select.genericList', $type, 'filter_type', 'class="inputbox tk_department_select" ', 'value', 'text', $searchtype);*/
        $lists = array();
        $db = $this->getDbo();
        //For Total Record
        $wherequery="";
        if(isset($searchdepartment) && $searchdepartment != ''){
            $searchdepartment = trim($searchdepartment);
            $wherequery .= " AND dep.departmentname LIKE ".$db->quote('%'.$searchdepartment.'%');
        }
        /*if(isset($searchtype) && $searchtype != ''){
            if(!is_numeric($searchtype)) return false;
            $wherequery .= " AND dep.ispublic =".$searchtype;
        }*/


        $query = "SELECT COUNT(id) From `#__js_ticket_departments` AS dep WHERE dep.status <> -1 ";
        $query.=$wherequery;
        $db->setQuery($query);
        $total =$db->loadResult();

        // ,dep.ispublic
        $query = "SELECT dep.id,dep.isdefault, dep.departmentname,dep.status, dep.created, dep.update,
        (SELECT COUNT(staff.id) From `#__js_ticket_staff` AS staff WHERE staff.departmentid=dep.id) AS user,
        (SELECT email.email From `#__js_ticket_email` AS email WHERE email.id=dep.emailid) AS outgoingemail,
        (SELECT staff.username From `#__js_ticket_staff` AS staff WHERE staff.id=dep.managerid) AS manager
        From`#__js_ticket_departments`AS dep WHERE dep.status <> -1";
        $query.=$wherequery;

        $db->setQuery($query,$limitstart,$limit);
        $departments = $db->loadObjectList();

        if($searchdepartment) $lists['searchdepartment'] = $searchdepartment;
        $result[0] = $departments;
        $result[1] = $total;
        $result[2] = $lists;
        return $result;
    }

    function getDepartmentForForm($id){
        if($id) if(!is_numeric($id)) return false;
        $db = $this->getDbo();
        if($id){
            $query = "SELECT * FROM `#__js_ticket_departments` WHERE id =".$id;
            $db->setQuery($query);
            $department = $db->loadObject();
        }
        $emailid = '';
        if(isset ($department)){
            $emailtemplateid = $department->emailtemplateid;
            $emailid = $department->emailid;
        }
        // $type = array(array('value' => null, 'text' => JText::_('Type')), array('value' => 0, 'text' => JText::_('Private')), array('value' => 1, 'text' => JText::_('Public')));
        $status = array(array('value' => null, 'text' => JText::_('Status')),array('value' => 0, 'text' => JText::_('Disabled')),array('value' => 1, 'text' => JText::_('Active')));
        $isdefault = array(array('value' => 0, 'text' => JText::_('Not default')),array('value' => 1, 'text' => JText::_('Default')));
        if(isset($department)){
            $lists['isdefault'] = JHTML::_('select.genericList', $isdefault, 'isdefault', 'class="inputbox js-ticket-form-field-input" ' . '', 'value', 'text',$department->isdefault);
            $lists['status'] = JHTML::_('select.genericList', $status, 'status', 'class="inputbox js-ticket-form-field-input " ' . '', 'value', 'text',$department->status);
            //$lists['type'] = JHTML::_('select.genericList', $type, 'ispublic', 'class="inputbox required " ' . '', 'value', 'text',$department->ispublic);
        }else{
            $lists['isdefault'] = JHTML::_('select.genericList', $isdefault, 'isdefault', 'class="inputbox js-ticket-form-field-input " ' . '', 'value', 'text',0);
            $lists['status'] = JHTML::_('select.genericList', $status, 'status', 'class="inputbox js-ticket-form-field-input " ' . '', 'value', 'text',1);
            //$lists['type'] = JHTML::_('select.genericList', $type, 'ispublic', 'class="inputbox required " ' . '', 'value', 'text','');
        }

        $emaillist = $this->getJSModel('email')->getEmailList(JText::_('Select email'));
        $lists['emaillist'] =JHTML::_('select.genericList', $emaillist, 'emailid', 'class="inputbox js-ticket-form-field-select required" '. '', 'value', 'text',$emailid);

        if(isset($department))
            $result[0] = $department;
        $result[1] = $lists;
        return $result;
    }

    function getDepartments(){
        $db = $this->getDBO();
        $query = "SELECT * FROM `#__js_ticket_departments` WHERE status = 1 ";//AND ispublic = 1
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        $departments = array();
        $departments[] =  array('value' => null,  'text' => JText::_('Select Department'));
        foreach($rows as $row){
                $departments[] = array('value' => $row->id, 'text' => JText::_($row->departmentname));
        }
        return $departments;
    }

    // function getDepartments($title) {   // with title without title
    //     $db = JFactory::getDBO();
    //     $query = "SELECT  id, departmentname FROM `#__js_ticket_departments` WHERE status = 1 ORDER BY departmentname ASC ";
    //     $db->setQuery($query);
    //     $rows = $db->loadObjectList();
    //     if ($db->getErrorNum()) {
    //         echo $db->stderr();
    //         return false;
    //     }
    //     $department = array();
    //     if ($title)
    //         $department[] = array('value' => JText::_(''), 'text' => $title);
    //     foreach ($rows as $row) {
    //         $department[] = array('value' => $row->id, 'text' => $row->departmentname);
    //     }
    //     return $department;
    // }

    function checkDepartmentSetting($id){
        if(!is_numeric($id)) return false;
        $db = JFactory::getDBO();
        $query = "Select dep.ticketautoresponce
                    From `#__js_ticket_tickets` AS ticket
                    JOIN `#__js_ticket_departments` AS dep ON dep.id = ticket.departmentid
                    where ticket.id = ".$id;
        $db->setQuery( $query );
        $depsetting = $db->loadResult();
        return $depsetting;
    }

    function getDepartmentEmail($id){
        if(!is_numeric($id)) return false;
        $db = JFactory::getDBO();
        $query = "Select email.email
                    From `#__js_ticket_tickets` AS ticket
                    JOIN `#__js_ticket_departments` AS dep ON dep.id = ticket.departmentid
                    JOIN `#__js_ticket_email` AS email ON email.id = dep.emailid
                    where ticket.id = ".$id;
        $db->setQuery( $query );
        $emailaddress = $db->loadObject();
        $email_address=$emailaddress->email;
        return $email_address;
    }

    function getDepartmentSignature($id){
        if(!is_numeric($id)) return false;
        $db = JFactory::getDBO();
        $query = "Select dep.departmentsignature
                    From `#__js_ticket_tickets` AS ticket
                    JOIN `#__js_ticket_departments` AS dep ON dep.id = ticket.departmentid
                    where ticket.id = ".$id;
        $db->setQuery( $query );
        $departmentsignature = $db->loadResult();
        $departmentsignature = str_replace(Chr(13),'<br>', $departmentsignature);
        return $departmentsignature;
    }

    function getDepartmentSignatureForNewTicket($id){
        if(!is_numeric($id)) return false;
        $db = JFactory::getDBO();
        $query = "Select dep.canappendsignature,dep.departmentsignature,dep.ticketautoresponce
                    From `#__js_ticket_tickets` AS ticket
                    JOIN `#__js_ticket_departments` AS dep ON dep.id = ticket.departmentid
                    where ticket.id = ".$id;
        $db->setQuery( $query );
        $departmentsignature = $db->loadObject();
        return $departmentsignature;
    }

    function getFormData($id) {
        $db = $this->getDbo();
        $email;
        if (isset($id)) {
            if (!is_numeric($id))
                return false;
            $query = "SELECT * FROM `#__js_ticket_departments` WHERE id =" . $id;
            $db->setQuery($query);
            $department = $db->loadObject();
        }
        if (isset($department)) {
            $emailtemplateid = $department->emailtemplateid;
            $emailid = $department->emailid;
        }
        $emaillist = $this->getJSModel('email')->getEmailList(JText::_('Select email'));
        $lists['emaillist'] = JHTML::_('select.genericList', $emaillist, 'emailid', 'class="inputbox required" ' . '', 'value', 'text');

        $result[0] = $department;
        $result[2] = $lists;
        return $result;
    }

    function storeDepartment($data) {
        $user = JSSupportticketCurrentUser::getInstance();
        if(!$user->getIsAdmin()){
            $permission = ($data['id'] == '') ? 'Add Department' : 'Edit Department';
            $per = $user->checkUserPermission($permission);
            if ($per == false)
                return PERMISSION_ERROR;
        }
        if($data['id']>0){
            unset($data['created']);
        }
        $data['departmentsignature'] = $this->getJSModel('jssupportticket')->getHtmlInput('departmentsignature');
        $row = $this->getTable('departments');
        if (!$row->bind($data)) {
            $this->setError($row->getError());
            return SAVE_ERROR;
        }
        if (!$row->check()) {
            $this->setError($row->getError());
            return SAVE_ERROR;
        }
        //Make all other department not default if its default
        if(isset($data['isdefault']) && $data['isdefault'] == 1){
          $this->makeAllDepartmentNotDefault();
        }
        if (!$row->store()) {
            $this->getJSModel('systemerrors')->updateSystemErrors($row->getError());
            $this->setError($row->getError());
            return SAVE_ERROR;
        }
        JSSupportticketMessage::$recordid = $row->id;
        return SAVED;
    }

    private function makeAllDepartmentNotDefault(){
        $db = JFactory::getDbo();
        $query = "UPDATE `#__js_ticket_departments` SET isdefault = 0";
        $db->setQuery($query);
        $db->execute();
    }

    function deleteDepartment($id){
        if(!is_numeric($id)) return false;
        $user = JSSupportticketCurrentUser::getInstance();
        if(!$user->getIsAdmin()){
            $per = $user->checkUserPermission('Delete Department');
            if ($per == false)
                return PERMISSION_ERROR;
        }
        $db = $this->getDBO();
        if($this->departmentCanDelete($id) == true){
            $query = "DELETE department FROM `#__js_ticket_departments` AS department WHERE department.id = ".$id;
            $db->setQuery($query);
            if (!$db->execute()) {
                $this->getJSModel('systemerrors')->updateSystemErrors($db->getErrorMsg());
                $this->setError($db->getErrorMsg());
                return DELETE_ERROR;
            }
            return DELETED;
        }else{
            return IN_USE;
        }
    }

    function deleteDepartmentAdmin() {
        $row = $this->getTable('departments');
        $db =  $this->getDBO();
        $c_id = JFactory::getApplication()->input->get('cid', array(0), '', 'array');
        foreach ($c_id as $id) {
            if(is_numeric($id)){
                if ($this->departmentCanDelete($id) == true) {
                    $query = "DELETE department
                         FROM `#__js_ticket_departments` AS department
                         WHERE department.id = " . $id;
                    $db->setQuery($query);
                    if (!$db->execute()) {
                        return DELETE_ERROR;
                    }
                }else{
                    return DELETE_ERROR;
                }
            }else{
                return false;
            }
        }
        return DELETED;
    }

    function departmentCanDelete($id){
        if(!is_numeric($id)) return false;
        $db = $this->getDBO();
        $query = "SELECT( (SELECT count(id) FROM `#__js_ticket_tickets` where departmentid=".$id." ) +
                          (SELECT count(id) FROM `#__js_ticket_acl_role_access_departments` where departmentid=".$id." ) +
                          (SELECT count(id) FROM `#__js_ticket_acl_user_access_departments` where departmentid=".$id." )
                        ) AS total";
        $db->setQuery($query);
        $total = $db->loadResult();
        if($total > 0) return false;
        else return true;
    }

     function getDepartmentById($id) {
        if (!is_numeric($id))
            return false;
        $db = $this->getDBO();
        $query = "SELECT departmentname FROM `#__js_ticket_departments` WHERE id = " . $id;
        $db->setQuery($query);
        $departmentname = $db->loadResult();
        return $departmentname;
    }

    function listDepartmentsByGroup($val) {
        if ($val)
            if (is_numeric($val) == false)
                return false;
        $db = $this->getDBO();
        $query = "SELECT groupacss.departmentid AS departmentid, dep.departmentname AS departmentname
            FROM `#__js_ticket_group_access_departments`AS groupacss
            JOIN `#__js_ticket_departments` AS dep ON groupacss.departmentid = dep.id
            WHERE groupacss.status = 1 AND groupacss.groupid = " . $val . " ORDER BY departmentname ASC";
        $db->setQuery($query);
        $result = $db->loadObjectList();
        $required = ' required';
        if (empty($result)) {
            $return_value = "<input class='inputbox" . $required . "' type='text' name='departmentid' size='40' maxlength='100'  />";
        } else {
            $return_value = "<select name='departmentid' class='inputbox" . $required . "' >\n";
            $return_value .= "<option value='0'>" . JText::_('Select Department') . "</option>\n";
            foreach ($result as $row) {
                $return_value .= "<option value=\"$row->departmentid\" >$row->departmentname</option> \n";
            }
            $return_value .= "</select>\n";
        }
        return $return_value;
    }

    function getDefaultDepartmentID(){
      $db = JFactory::getDbo();
      $query = "SELECT id FROM `#__js_ticket_departments` WHERE isdefault = 1";
      $db->setQuery($query);
      $defaultid = $db->loadResult();
      return $defaultid;
    }
}
?>
