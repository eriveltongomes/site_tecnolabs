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

class JSSupportticketModelStaff extends JSSupportTicketModel {

    function __construct() {
        parent::__construct();
    }

    function getAllStaffMembers($username, $roleid, $statusid, $limitstart, $limit) {
        $db = $this->getDBO();
        $result = array();
        $status [] = array('value' => null, 'text' => JText::_('Select Status'));
        $status [] = array('value' => 1, 'text' => JText::_('Active'));
        $status [] = array('value' => -1, 'text' => JText::_('Disabled'));

        $lists['roles'] = JHTML::_('select.genericList', $this->getJSModel('roles')->getRoles(JText::_('Select Role')), 'filter_sm_roleid', 'class="inputbox js-ticket-select-field" ' . '', 'value', 'text', $roleid);
        $lists['status'] = JHTML::_('select.genericList', $status, 'filter_sm_statusid', 'class="inputbox js-ticket-select-field" ' . '', 'value', 'text', $statusid);
        $query = "SELECT COUNT(id) FROM #__js_ticket_staff AS staff WHERE staff.status <> 0";

        if ($username){
            $username = trim($username);
            $query .= " AND staff.username LIKE " . $db->quote('%'.$username.'%');
        }
        if ($roleid) {
            if (!is_numeric($roleid))
                return false;
            $query .= " AND staff.roleid = " . $roleid;
        }
        if ($statusid) {
            if (!is_numeric($statusid))
                return false;
            $query .=" AND staff.status = " . $statusid;
        }
        $db->setQuery($query);
        $total = $db->loadResult();
        if ($total <= $limitstart)
            $limitstart = 0;
        $query = "SELECT staff.*,role.name AS groupname,user.lastvisitDate AS lastvisit
					FROM `#__js_ticket_staff` AS staff
					LEFT JOIN `#__js_ticket_acl_roles` AS role ON role.id = staff.roleid
					LEFT JOIN `#__users` AS user ON user.id = staff.uid
					WHERE staff.status <> 0";
        if ($username)
            $query .= " AND staff.username LIKE " . $db->quote('%'.$username.'%');
        if ($roleid) {
            if (!is_numeric($roleid))
                return false;
            $query .= " AND staff.roleid= " . $roleid;
        }
        if ($statusid) {
            if (!is_numeric($statusid))
                return false;
            $query .=" AND staff.status = " . $statusid;
        }
        $db->setQuery($query, $limitstart, $limit);
        if ($username)
            $lists['username'] = $username;
        $result[0] = $db->loadObjectList();
        $result[1] = $total;
        $result[2] = $lists;
        return $result;
    }

    function getAllUsers($searchname, $searchusername, $searchrole , $limitstart, $limit) {
        $db = JFactory::getDBO();
        $result = array();
        $version = new JVersion;
        $joomla = $version->getShortVersion();
        $jversion = substr($joomla, 0, 3);

        if ($jversion == '1.5') {
            $query = 'SELECT COUNT(a.id)'
                    . ' FROM #__user AS a';
        } else {
            $query = 'SELECT COUNT(a.id)'
                    . ' FROM #__users AS a';
        }
        $clause = ' WHERE ';
        if ($searchname) {
            $searchname = trim($searchname);
            $query .= $clause . ' LOWER(a.name) LIKE ' . $db->Quote('%' . $db->getEscaped($searchname, true) . '%', false);
            $clause = 'AND';
        }
        if ($searchusername) {
            $searchusername = trim($searchusername);
            $query .= $clause . ' LOWER(a.username) LIKE ' . $db->Quote('%' . $db->getEscaped($searchusername, true) . '%', false);
        }
        $db->setQuery($query);
        $total = $db->loadResult();
        if ($total <= $limitstart)
            $limitstart = 0;

        if ($jversion == '1.5') {
            $query = 'SELECT a.*'
                    . ' FROM #__users AS a';
        } else {
            $query = 'SELECT a.*'
                    . ' FROM #__users AS a';
        }
        $clause = ' WHERE ';
        if ($searchname) {
            $searchname = trim($searchname);
            $query .= $clause . ' LOWER(a.name) LIKE ' . $db->Quote('%' . $db->getEscaped($searchname, true) . '%', false);
            $clause = 'AND';
        }
        if ($searchusername) {
            $searchusername = trim($searchusername);
            $query .= $clause . ' LOWER(a.username) LIKE ' . $db->Quote('%' . $db->getEscaped($searchusername, true) . '%', false);
            $clause = 'AND';
        }
        if ($searchrole){
            $searchrole = trim($searchrole);
            $query .= $clause . ' LOWER( role.title) LIKE ' . $db->Quote('%' . $db->getEscaped($searchrole, true) . '%', false);
        }

        $query .= ' GROUP BY a.id';
        $db->setQuery($query, $limitstart, $limit);
        $result[0] = $db->loadObjectList();

        $lists = array();
        if ($searchname)
            $lists['searchname'] = $searchname;
        if ($searchusername)
            $lists['searchusername'] = $searchusername;
        if ($searchrole)
            $lists['searchrole'] = $searchrole;
        $result[1] = $total;
        $result[2] = $lists;
        return $result;
    }

    function getStaffMemberSignature($uid) {
        if (!is_numeric($uid))
            return false;
        $db = JFactory::getDBO();
        $query = "SELECT user.signature
                    FROM `#__js_ticket_staff` AS user
                    WHERE user.uid = " . $uid;
        $db->setQuery($query);
        $usersignature = $db->loadResult();
        $usersignature = str_replace(Chr(13), '<br>', $usersignature);
        return $usersignature;
    }

    function getStaffforForm($id ) {
        $roles= $this->getJSModel('roles');
                $permission_by_task=array();

        $db = $this->getDBO();
        if($id){
            if (is_numeric($id) == false) return false;
            $query = "SELECT staff.*
            FROM `#__js_ticket_staff` AS staff
            WHERE staff.id = ".$id;
            $db->setQuery($query);
            $staff = $db->loadObject();
            if($staff->uid != '' && $staff->uid != 0){
                            $version = new JVersion;
                            $joomla = $version->getShortVersion();
                            $jversion = substr($joomla,0,3);
                            if($jversion == '1.5'){
                                    $query = 'SELECT a.*'
                                                    . ' FROM #__users AS a WHERE a.id='.$staff->uid;
                            }else{
                                    $query = 'SELECT a.*'
                                                    . ' FROM #__users AS a WHERE a.id='.$staff->uid;
                            }
                            $db->setQuery($query);
                            $user = $db->loadObject();
            }
            $query = "SELECT u_per.permissionid AS userpermissionid,per.id,per.permission,per.permissiongroup AS pgroup
                        FROM `#__js_ticket_acl_permissions` AS per
                        LEFT JOIN `#__js_ticket_acl_user_permissions` AS u_per ON (u_per.staffid=".$staff->id." AND u_per.permissionid=per.id )
                        ORDER BY per.permissiongroup,per.id";
            $db->setQuery($query);
            $permission_user = $db->loadObjectList();

            $query = "SELECT u_da.departmentid AS userdepartmentid,dep.id,dep.departmentname AS name
                        FROM `#__js_ticket_departments` AS dep
                        LEFT JOIN `#__js_ticket_acl_user_access_departments` AS u_da ON (u_da.staffid=".$staff->id." AND u_da.departmentid=dep.id )
                        ORDER BY dep.id";
            $db->setQuery($query);
            $department_user = $db->loadObjectList();
            foreach($permission_user AS $userper){
                $userpermissionid="";if(isset($userper->userpermissionid)){$userpermissionid=$userper->userpermissionid; }
                switch($userper->pgroup){
                    case 1:
                        $permission_by_task['ticket_section'][]=(object) array('id'=>$userper->id,'permission'=>$userper->permission,'pgroup'=>$userper->pgroup,'userpermissionid'=>$userpermissionid);
                    break;
                    case 2:
                        $permission_by_task['staff_section'][]=(object) array('id'=>$userper->id,'permission'=>$userper->permission,'pgroup'=>$userper->pgroup,'userpermissionid'=>$userpermissionid);
                    break;
                    case 3:
                        $permission_by_task['kb_section'][]=(object) array('id'=>$userper->id,'permission'=>$userper->permission,'pgroup'=>$userper->pgroup,'userpermissionid'=>$userpermissionid);
                    break;
                    case 4:
                        $permission_by_task['faq_section'][]=(object) array('id'=>$userper->id,'permission'=>$userper->permission,'pgroup'=>$userper->pgroup,'userpermissionid'=>$userpermissionid);
                    break;
                    case 5:
                        $permission_by_task['download_section'][]=(object) array('id'=>$userper->id,'permission'=>$userper->permission,'pgroup'=>$userper->pgroup,'userpermissionid'=>$userpermissionid);
                    break;
                    case 6:
                        $permission_by_task['announcement_section'][]=(object) array('id'=>$userper->id,'permission'=>$userper->permission,'pgroup'=>$userper->pgroup,'userpermissionid'=>$userpermissionid);
                    break;
                    case 7:
                        $permission_by_task['mail_section'][]=(object) array('id'=>$userper->id,'permission'=>$userper->permission,'pgroup'=>$userper->pgroup,'userpermissionid'=>$userpermissionid);
                    break;
                }
            }
        }

        $title = "";
        if(isset($staff) ){
            $lists['roles'] = JHTML::_('select.genericList', $roles->getRoles(JText::_('Select Role')), 'roleid', 'class="inputbox  js-ticket-select-field required" '. 'onChange="getrolepermission(this.value)"', 'value', 'text', $staff->roleid);
        }else{
            $lists['roles'] = JHTML::_('select.genericList', $roles->getRoles(JText::_('Select Role')), 'roleid', 'class="inputbox js-ticket-select-field required" '. 'onChange="getrolepermission(this.value)"', 'value', 'text', '');
        }
        if(isset($staff)){
            $result[0] = $staff;
            $result[1] = $user;
            $result[3] = $permission_user;
            $result[4] = $department_user;
                        $result[5] = $permission_by_task;
        }
        $result[2] = $lists;
        return $result;
    }
    function storeStaffMember($data){
        $user = JSSupportticketCurrentUser::getInstance();
        if(!$user->getIsAdmin()){
            $permission = ($data['id'] == '') ? 'Add User' : 'Edit User';
            $per = $user->checkUserPermission($permission);
            if ($per == false)
                return PERMISSION_ERROR;
        }
        if(!isset($data['appendsignature'])) $data['appendsignature'] = 0;
        if(!isset($data['photo'])) $data['photo'] = '';
        $row = $this->getTable('staff');
        if($data['id'] == ''){
            $isexist = $this->checkUserExist($data['uid']);
            if($isexist <> 0){
                return ALREADY_EXIST;
            }
        }
        if (!$row->bind($data)) {
            $this->setError($row->getError());
            return SAVE_ERROR;
        }
        if (!$row->check()) {
            $this->setError($row->getError());
            return SAVE_ERROR;
        }
	try{
            $row->store();
        }
        catch (RuntimeException $e){
            $this->getJSModel('systemerrors')->updateSystemErrors($e);
            $this->setError($e);
            return SAVE_ERROR;
        }

        $roledepdataArray = array();
        if(!empty($data['roledepdata'])){
            $roledepdataArray = $data['roledepdata'];
        }
        $store_user_departments=$this->getJSModel('useraccessdepartments')->storeUserAccessDepartments($roledepdataArray,$row->uid,$row->roleid,$row->id);
        if($store_user_departments==false){
            $row->delete($row->id);
            return SAVE_ERROR;
        }
        $roleperdataArray = array();
        if(!empty($data['roleperdata'])){
            $roleperdataArray = $data['roleperdata'];
        }
        $store_user_permissons = $this->getJSModel('userpermissions')->storeUserPermissions($roleperdataArray,$row->uid,$row->roleid,$row->id);
        if($store_user_permissons==false){
            $row->delete($row->id);
            return SAVE_ERROR;
        }

        JSSupportticketMessage::$recordid = $row->id;
        return SAVED;
    }

     function staffMemberCanDelete($staffid) {
        if (!is_numeric($staffid)) return false;
        $db = $this->getDBO();
        $query = "SELECT (SELECT COUNT(id) AS total FROM `#__js_ticket_tickets` WHERE staffid = ".$staffid.") +
                    (SELECT COUNT(id) AS total FROM `#__js_ticket_staff_mail` AS mail WHERE mail.from = ".$staffid.") +
                    (SELECT COUNT(id) AS total FROM `#__js_ticket_staff_mail` AS mail WHERE mail.to = ".$staffid.") +
                    (SELECT COUNT(id) AS total FROM `#__js_ticket_replies` WHERE staffid = ".$staffid.")
                     AS total ";
        $db->setQuery($query);
        $total = $db->loadResult();
        if ($total > 0)
            return false;
        else
            return true;
    }

    function deleteStaffMember($id){
        if(!is_numeric($id)) return false;
        $user = JSSupportticketCurrentUser::getInstance();
        if(!$user->getIsAdmin()){
            $per = $user->checkUserPermission('Delete User');
            if ($per == false)
                return PERMISSION_ERROR;
        }
        $row = $this->getTable('staff');
        if($this->staffMemberCanDelete($id) == true){
            if (!$row->delete($id)){
                $this->getJSModel('systemerrors')->updateSystemErrors($row->getErrorMsg());
                $this->setError($row->getErrorMsg());
                return DELETE_ERROR;
            }
            $d_u_p = $this->getJSModel('userpermissions')->deleteUserPermissions($id);
            if($d_u_p==false)
                return DELETE_ERROR;
            return DELETED;
        }else return IN_USE;
    }

    function deleteStaffMemberAdmin() {
        $cids = JFactory::getApplication()->input->get('cid', array(0), '', 'array');
        $row = $this->getTable('staff');
        $deleteall = 1;
        foreach ($cids as $cid) {
            if(is_numeric($cid)){
                if ($this->staffMemberCanDelete($cid) == true) {
                    if (!$row->delete($cid)) {
                        $this->setError($row->getErrorMsg());
                        return DELETE_ERROR;
                    }
                    $d_u_p = $this->getJSModel('userpermissions')->deleteUserPermissions($cid);
                    if ($d_u_p == false)
                        return DELETE_ERROR;
                }else
                    $deleteall++;
            }else{
                return false;
            }
        }
        if($deleteall == 1){
            return DELETED;
        }else{
            $deleteall = $deleteall-1;
            JSSupportticketMessage::$recordid = $deleteall;
            return DELETE_ERROR;
        }
    }

    function isStaffMember($uid){
        if(!is_numeric($uid)) return false;
        $db = $this->getDBO();
        $query = "SELECT id FROM `#__js_ticket_staff` WHERE uid =".$uid;
        $db->setQuery($query);
        $id = $db->loadResult();
        if($id) return $id;
        else return false;
    }

    function isCurrentUserStaff(){
    	$db = $this->getDBO();
 		$uid = JFactory::getUser()->id;
        $query = "SELECT id FROM `#__js_ticket_staff` WHERE uid =".$uid;
        $db->setQuery($query);
        $result = $db->loadResult();
        if($result) return $result;
        else return false;
    }

        function getStaff($title){
        $db= $this->getDbo();
        $query="SELECT uid,firstname,lastname FROM `#__js_ticket_staff`";
        try{
            $db->setQuery($query);
            $rows=$db->loadObjectList();
            $staff=array();
            if($title)
                $staff[]=array('value'=>'','text'=>$title);
            foreach ($rows as $row) {
                $staff[]=array('value'=>$row->uid,'text'=>$row->firstname.' '.$row->lastname);
            }
            return $staff;
        }
        catch (RuntimeException $e){
            echo $db->stderr();
            return false;
        }
        
    }



    function getStaffid($uid){
        if(!is_numeric($uid)) return false;
        $db = $this->getDbo();

        $query = "SELECT id FROM `#__js_ticket_staff` WHERE uid = ".$uid;
        $db->setQuery($query);
        $staffid=$db->loadResult();

        return $staffid;
    }

    function getStaffMembers(){
        $db = $this->getDBO();
        $query = "SELECT * FROM `#__js_ticket_staff` WHERE status = 1";
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        $staffmember = array();
        $staffmember[] =  array('value' => null,  'text' => JText::_('Select Staff'));
        foreach($rows as $row){
            $staffmember[] =  array('value' => $row->id,'text' => $row->firstname . ' ' .$row->lastname);
        }
        return $staffmember;
    }

    function isCurrentStaffDisabled(){
        $db = $this->getDBO();
        $uid = JFactory::getUser()->id;
        $query = "SELECT status FROM `#__js_ticket_staff` WHERE uid =".$uid;
        $db->setQuery($query);
        $status = $db->loadResult();
        if($status == 1) return false;
        else return true;
    }

    function storeStaffSetting(){
        $row = $this->getTable('staff');
        $data = JFactory::getApplication()->input->post->getArray();
        if (!$row->bind($data)){
            $this->setError($row->getError());
            return false;
        }
        if (!$row->check()){
            $this->setError($row->getError());
            return 2;
        }
        if (!$row->store()){
            $this->setError($row->getError());
            $this->updateSystemErrors($row->getError());
            echo $row->getError();
            return false;
        }
        $return = true;
        return $return;
    }

    function getStaffSettings($uid){
        if($uid){
            if(!is_numeric($uid)) return false;
            $db = $this->getDbo();
            $query = "SELECT staff.*,role.name AS rolename ,user.username
                    FROM `#__js_ticket_staff` AS staff
                    JOIN `#__js_ticket_acl_roles` AS role ON role.id = staff.roleid
                    JOIN `#__users` AS user ON user.id = staff.uid
                    WHERE uid = $uid";
            $db->setQuery($query);
            $result = $db->loadObject();
            return $result;
        }
    }

    function getStaffChangeProfile($name,$emailaddress) {
        $db = $this->getDBO();
        $query = "SELECT DISTINCT user.ID AS userid, user.username, user.email AS useremail, user.name AS userdisplayname
                    FROM `#__users` AS user
                    WHERE NOT EXISTS( SELECT staff.id FROM `#__js_ticket_staff` AS staff WHERE user.ID = staff.uid)";
        if (strlen($name) > 1) {
            $name = trim($name);
            $query .= " AND user.username LIKE ".$db->quote('%'.$name.'%');
        }
        if (strlen($emailaddress) > 1) {
            $emailaddress = trim($emailaddress);
            $query .= " AND user.email LIKE ".$db->quote('%'.$emailaddress.'%');
        }
        $db->setQuery($query);
        $users = $db->loadObjectList();
        $result = '';
        foreach ($users AS $user) {
            $result .= '<div class="js-col-md-1">' . $user->userid . '</div>
                        <div class="js-col-md-3"><a href="#" class="js-userpopup-link" data-id="' . $user->userid . '">' . $user->username . '</a></div>
                        <div class="js-col-md-4">' . $user->useremail . '</div>
                        <div class="js-col-md-4">' . $user->userdisplayname . '</div>';
        }
        return $result;
    }

    function saveStaffProfileAjax($value,$datafor) {
        $user = JSSupportticketCurrentUser::getInstance();
        $uid = $user->getId();
        $db = $this->getDBO();
        $query = "UPDATE `#__js_ticket_staff` SET $datafor = " . $db->quote($value) . " WHERE uid = $uid";
        $db->setQuery($query);
        if(!$db->execute()){
            return false;
        }else{
            return true;
        }
    }

    function uploadStaffImage($id) {
        $id = JFactory::getApplication()->input->get('id');
        if(!is_numeric($id)) return false;
        if(!isset($_FILES['filename'])) return false;
        //image upload
        $_FILES['filename']['name']     = $_FILES['filename']['name'][0];
        $_FILES['filename']['type']     = $_FILES['filename']['type'][0];
        $_FILES['filename']['tmp_name'] = $_FILES['filename']['tmp_name'][0];
        $_FILES['filename']['error']    = $_FILES['filename']['error'][0];
        $_FILES['filename']['size']     = $_FILES['filename']['size'][0];
        
        if ($_FILES['filename']['size'] > 0) {
            $datadirectory = $this->getJSModel('config')->getConfigs();
            $datadirectory = $datadirectory['data_directory'];
            $base = JPATH_BASE;
            if(JFactory::getApplication()->isClient('administrator')){
                $base = substr($base, 0, strlen($base) - 14); //remove administrator
            }
            $path = $base . '/' . $datadirectory;
            $imagepath = JURI::root() . '/' . $datadirectory;
            if (!file_exists($path)) { // create user directory
                $this->getJSModel('attachments')->makeDir($path);
            }
            $path = $path . '/staffdata';
            $imagepath = $imagepath . '/staffdata';
            if (!file_exists($path)) { // create user directory
                $this->getJSModel('attachments')->makeDir($path);
            }
            $path = $path . '/staff_' . $id;
            $imagepath = $imagepath . '/staff_' . $id;
            if (!file_exists($path)) { // create user directory
                $this->getJSModel('attachments')->makeDir($path);
            }

            require_once JPATH_COMPONENT_ADMINISTRATOR . '/include/lib/class.upload.php';
            $handle = new upload($_FILES['filename']);
            if ($handle->uploaded) {
                $handle->file_new_name_body = 'staff_' . $id;
                $handle->image_resize = true;
                $handle->image_x = 200;
                $handle->image_y = 200;
                $handle->image_ratio_fill = true;
                $handle->process($path);
                if ($handle->processed) {
                    $handle->clean();
                    $result = $handle->file_dst_name;
                } else {
                    $result = false;
                }
            }
            if ($result != false) {
                $array['errorcode'] = true;
                $db = JFactory::getDbo();
                $db->setQuery("UPDATE `#__js_ticket_staff` SET photo = '" . $result . "' WHERE id = $id");
                $db->execute();
            } else {
                $array['errorcode'] = false;
            }
        }
        $imagepath .= '/' . $result;
        $array['imagepath'] = $imagepath;
        return $array;
    }



    function getStaffAccessDepartments($uid){
        if(!is_numeric($uid)) return false;
        $db = $this->getDBO();
        $query = "SELECT dep.id ,dep.departmentname FROM `#__js_ticket_departments` AS  dep";
        $checkisstaffmember=$this->isStaffMember($uid);
        $clause = ' WHERE ';
        if($checkisstaffmember){
            $clause = " AND ";
            $query.=" JOIN `#__js_ticket_acl_user_access_departments` AS uad ON uad.departmentid=dep.id WHERE uad.uid=".$uid;
        }
        $query .= $clause .' dep.status = 1  ';//AND dep.ispublic = 1
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        $departments = array();
        $departments[] =  array('value' => null,  'text' => JText::_('Select Department'));
        foreach($rows as $row){
                $departments[] =  array('value' => $row->id,'text' => $row->departmentname);
        }
        return $departments;

    }
    function getStaffAccessDepartmentPremade($uid,$id){
        if(!is_numeric($uid)) return false;
        if(!is_numeric($id)) return false;
        $db = $this->getDBO();
        $query = "SELECT d_m_p.id,d_m_p.title FROM `#__js_ticket_department_message_premade` AS d_m_p ";
        $checkisstaffmember=$this->isStaffMember($uid);

        $wherequery=" WHERE d_m_p.isenabled = 1";
        if($checkisstaffmember){
            $query.=" JOIN `#__js_ticket_acl_user_access_departments` AS uad ON d_m_p.departmentid=uad.departmentid";
            $wherequery.=" AND uad.uid=".$uid;
        }

        if($id!=0){
            if(is_numeric($id)==false) return false;
            $wherequery.=" AND d_m_p.departmentid=".$id;
        }
        $query.=$wherequery;
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        $premade = array();
        if(!empty($rows)){
            $premade[] =  array('value' => null,  'text' => JText::_('Select Premade'));
            foreach($rows as $row){
                $premade[] =  array('value' => $row->id,'text' => JText::_($row->title));
            }
        }else $premade[] =  array('value' => null,    'text' => JText::_('Select Premade'));
        return $premade;

    }

    function checkUserExist($val) {
        if (is_numeric($val) == false) return false;
        $db = $this->getDBO();
        $query  = "SELECT COUNT(id) FROM `#__js_ticket_staff` WHERE uid = ".$val;
        $db->setQuery($query);
        $result = $db->loadResult();
        return $result;
    }

    function getMyName($id) {
        if (!is_numeric($id))
            return false;
        $db = $this->getDBO();
        $query = "SELECT CONCAT(firstname,' ',lastname) AS name FROM `#__js_ticket_staff` WHERE id = $id";
        $db->setQuery($query);
        $result = $db->loadResult();
        return $result;
    }

    function getAllStaffMemberByDepId($id) {
        if (!is_numeric($id))
            return false;
        $db = $this->getDBO();
        $query = "SELECT staff.email ,staff.firstname,staff.lastname,
                    (SELECT usr_perm.grant
                        FROM `#__js_ticket_acl_permissions` AS p
                        JOIN `#__js_ticket_acl_user_permissions` AS usr_perm ON usr_perm.permissionid = p.id
                        WHERE p.permission = 'New Ticket Notification' AND usr_perm.staffid = staff.id ) AS canemail
                    FROM `#__js_ticket_acl_user_access_departments` AS dep
                    JOIN `#__js_ticket_staff` AS staff ON staff.id = dep.staffid
                    WHERE dep.departmentid = $id";
        $db->setQuery($query);
        $result = $db->loadObjectList();
        return $result;
    }

    function getStaffEmailByStaffId($id){
        if (!is_numeric($id))
            return false;
        $db = $this->getDBO();
        $query = "SELECT staff.email FROM `#__js_ticket_staff` AS staff WHERE staff.id =".$id;
        $db->setQuery($query);
        $email = $db->loadResult();
        return $email;
    }

    function getStaffInfoByStaffId($id){
        if (!is_numeric($id))
            return false;
        $db = $this->getDBO();
        $query = "SELECT staff.email,                    (SELECT usr_perm.grant
                        FROM `#__js_ticket_acl_permissions` AS p
                        JOIN `#__js_ticket_acl_user_permissions` AS usr_perm ON usr_perm.permissionid = p.id
                        WHERE p.permission = 'New Ticket Notification' AND usr_perm.staffid = staff.id ) AS canemail,staff.firstname,staff.lastname FROM `#__js_ticket_staff` AS staff WHERE staff.id =".$id;
        $db->setQuery($query);
        $data = $db->loadObjectList();
        return $data;
    }

    function getStaffListForReports() {
        $db = JFactory::getDbo();
        $query = "SELECT DISTINCT EXISTS( SELECT staff.id FROM `#__js_ticket_staff` AS staff WHERE user.ID = staff.uid) AS alreadyuser,user.ID AS userid, user.name AS username, user.email AS useremail, user.name AS userdisplayname
                    FROM `#__users` AS user ORDER BY alreadyuser";
        $db->setQuery($query);
        $users = $db->loadObjectList();
        return $users;
    }
    function getUserNameById($id){
        if (!is_numeric($id))
            return false;
        $db = JFactory::getDbo();
        //$query = "SELECT user_nicename AS name FROM `#__users` WHERE id = $id";
        $query = "SELECT name FROM `#__users` WHERE id = $id";
        $db->setQuery($query);
        $username = $db->loadResult();
        return $username;
    }


    function getusersearchstaffreportajax() {
        $userlimit = JFactory::getApplication()->input->get('userlimit',0);
        $maxrecorded = 4;
        $username = JFactory::getApplication()->input->getString('username');
        $name = JFactory::getApplication()->input->getString('name');
        $emailaddress = JFactory::getApplication()->input->getString('emailaddress');
        $db = JFactory::getDbo();
        $wherequery = '';
        if (strlen($name) > 1) {
            $name = trim($name);
            $wherequery .= " AND user.name LIKE ".$db->quote('%'.$name.'%');
        }
        if (strlen($username) > 1) {
            $username = trim($username);
            $wherequery .= " AND user.username LIKE ".$db->quote('%'.$username.'%');
        }
        if (strlen($emailaddress) > 1) {
            $emailaddress = trim($emailaddress);
            $wherequery .= " AND user.email LIKE ".$db->quote('%'.$emailaddress.'%');
        }
        $query = "SELECT DISTINCT COUNT(user.id)
                    FROM `#__users` AS user
                    WHERE EXISTS( SELECT staff.id FROM `#__js_ticket_staff` AS staff WHERE user.id = staff.uid)";
        $query .= $wherequery;
        $db->setQuery($query);
        $total = $db->loadResult();
        $limit = $userlimit * $maxrecorded;
        if($limit >= $total){
          $limit = 0;
        }
        $query = "SELECT DISTINCT user.id AS userid, user.username AS username, user.email AS useremail, user.name AS displayname
                    FROM `#__users` AS user
                    WHERE EXISTS( SELECT staff.id FROM `#__js_ticket_staff` AS staff WHERE user.id = staff.uid)";
        $query .= $wherequery;
        $query .= " LIMIT $limit, $maxrecorded";
        $db->setQuery($query);
        $users = $db->loadObjectList();
        $html = $this->makeUserList($users,$total,$maxrecorded,$userlimit);
        return $html;
    }

    function getUserListForRegistration() {
        $db = JFactory::getDbo();
        $query = "SELECT DISTINCT EXISTS( SELECT staff.id FROM `#__js_ticket_staff` AS staff WHERE user.ID = staff.uid) AS alreadyuser,user.ID AS userid, user.name AS username, user.email AS useremail, user.name AS userdisplayname
                    FROM `#__users` AS user ORDER BY alreadyuser";
        $db->setQuery($query);
        $users = $db->loadObjectList();
        return $users;
    }

    function getusersearchajax() {
        $userlimit = JFactory::getApplication()->input->get('userlimit',0);
        $maxrecorded = 4;
        $db = JFactory::getDbo();
        $name = JFactory::getApplication()->input->getString('name','');
        $username = JFactory::getApplication()->input->getString('username','');
        $emailaddress = JFactory::getApplication()->input->getString('emailaddress','');
        $wherequery = '';
        if($name!=''){
            $name = trim($name);
            $wherequery .= " AND user.name LIKE ".$db->quote('%'.$name.'%');
        }
        if($username!=''){
            $username = trim($username);
            $wherequery .= " AND user.username LIKE ".$db->quote('%'.$username.'%');
        }
        if($emailaddress!=''){
            $emailaddress = trim($emailaddress);
            $wherequery .= " AND user.email LIKE ".$db->quote('%'.$emailaddress.'%');
        }

        $query = "SELECT DISTINCT COUNT(user.id) FROM `#__users` AS user WHERE NOT EXISTS(SELECT id FROM `#__js_ticket_staff` WHERE uid = user.id) ";
        $query .= $wherequery;
        $db->setQuery($query);
        $total = $db->loadResult();
        $limit = $userlimit * $maxrecorded;
        if($limit >= $total){
            $limit = 0;
        }
        $query = "SELECT DISTINCT user.id AS userid, user.username AS username, user.email AS useremail,user.name AS displayname
                FROM `#__users` AS user WHERE NOT EXISTS(SELECT id FROM `#__js_ticket_staff` WHERE uid = user.id) ";
        $query .= $wherequery;
        $query .= " LIMIT $limit, $maxrecorded";
        $db->setQuery($query);
        $users = $db->loadObjectList();
        $html = $this->makeUserList($users,$total,$maxrecorded,$userlimit);
        return $html;
    }
    function getstaffusersearchajax() {
      $userlimit = JFactory::getApplication()->input->get('userlimit',0);
      $maxrecorded = 4;
      $db = JFactory::getDbo();
      $name = JFactory::getApplication()->input->getString('name','');
      $username = JFactory::getApplication()->input->getString('username','');
      $emailaddress = JFactory::getApplication()->input->getString('emailaddress','');
      $wherequery = '';
      if($name!=''){
        $name = trim($name);
        $wherequery = " AND user.name LIKE ".$db->quote('%'.$name.'%');
      }
      if($username!=''){
        $username = trim($username);
        $wherequery = " AND user.username LIKE ".$db->quote('%'.$username.'%');
      }
      if($emailaddress!=''){
        $emailaddress = trim($emailaddress);
        $wherequery = " AND user.email LIKE ".$db->quote('%'.$emailaddress.'%');
      }
      $query = "SELECT DISTINCT COUNT(user.id)
              FROM `#__users` AS user
              JOIN `#__js_ticket_staff` AS staff ON staff.uid = user.id
              WHERE 1 = 1 ";
      $query .= $wherequery;
      $db->setQuery($query);
      $total = $db->loadResult();
      $limit = $userlimit * $maxrecorded;
      if($limit >= $total){
          $limit = 0;
      }
      $query = "SELECT DISTINCT user.id AS userid, user.name AS displayname, user.email AS useremail, user.username AS username
              FROM `#__users` AS user
              JOIN `#__js_ticket_staff` AS staff ON staff.uid = user.id
              WHERE 1 = 1 ";
      $query .= $wherequery;
      $query .= " LIMIT $limit, $maxrecorded ";
      $db->setQuery($query);
      $users = $db->loadObjectList();
      $html = $this->makeUserList($users,$total,$maxrecorded,$userlimit);
      return $html;
    }

    function makeUserList($users,$total,$maxrecorded,$userlimit){
        $html = '';
        if(!empty($users)){
            if(is_array($users)){
                $html ='
                <div class="js-ticket-table-wrp js-col-md-12">
                    <div class="js-ticket-table-header">
                        <div class="js-ticket-table-header-col js-col-md-2 js-col-xs-2">'.JText::_('User ID').'</div>
                        <div class="js-ticket-table-header-col js-col-md-3 js-col-xs-3">'.JText::_('Username').'</div>
                        <div class="js-ticket-table-header-col js-col-md-4 js-col-xs-4">'.JText::_('Email Address').'</div>
                        <div class="js-ticket-table-header-col js-col-md-3 js-col-xs-3">'.JText::_('Name').'</div>
                    </div>
                    <div class="js-ticket-table-body">';
                foreach($users AS $user){
                    $html .='
                        <div class="js-ticket-data-row">
                                <div class="js-ticket-table-body-col js-col-md-2 js-col-xs-2">
                                    <span class="js-ticket-display-block">'.JText::_('User ID').'</span>'.$user->userid.'
                                </div>
                                <div class="js-ticket-table-body-col js-col-md-3 js-col-xs-3">
                                    <span class="js-ticket-display-block">'.JText::_('Username:').'</span>
                                    <span class="js-ticket-title"><a href="#" class="js-userpopup-link" data-id="'.$user->userid.'" data-email="'.$user->useremail.'" data-name="'.$user->username.'">'.$user->username.'</a></span>
                                </div>
                                <div class="js-ticket-table-body-col js-col-md-4 js-col-xs-4">
                                    <span class="js-ticket-display-block">'.JText::_('Email:').'</span>
                                    '.$user->useremail.'
                                </div>
                                <div class="js-ticket-table-body-col js-col-md-3 js-col-xs-3">
                                    <span class="js-ticket-display-block">'.JText::_('Name:').'</span>
                                    '.$user->displayname.'
                                </div>
                            </div>';
                }
                $html .='</div>';
            }
                $num_of_pages = ceil($total / $maxrecorded);
                $num_of_pages = ($num_of_pages > 0) ? ceil($num_of_pages) : floor($num_of_pages);
                if($num_of_pages > 0){
                    $page_html = '';
                    $prev = $userlimit;
                    if($prev > 0){
                        $page_html .= '<a class="jsst_userlink" href="#" onclick="updateuserlist('.($prev - 1).');">'.JText::_('Previous').'</a>';
                    }
                    for($i = 0; $i < $num_of_pages; $i++){
                        if($i == $userlimit)
                            $page_html .= '<span class="jsst_userlink selected" >'.($i + 1).'</span>';
                        else
                            $page_html .= '<a class="jsst_userlink" href="#" onclick="updateuserlist('.$i.');">'.($i + 1).'</a>';

                    }
                    $next = $userlimit + 1;
                    if($next < $num_of_pages){
                        $page_html .= '<a class="jsst_userlink" href="#" onclick="updateuserlist('.$next.');">'.JText::_('Next').'</a>';
                    }
                    if($page_html != ''){
                        $html .= '<div class="jsst_userpages">'.$page_html.'</div>';
                    }
                }
            
        }else{
            $html = messagesLayout::getRecordNotFound();
        }
        return $html;
    }

    function isUserStaff($uid = null) {
        $db = JFactory::getDbo();
        $user = JFactory::getUser();
        if ($uid == null)
            $uid = $user->id;
        if ($uid == 0) {
            return false;
        } else {
            $query = "SELECT id FROM `#__js_ticket_staff` WHERE uid = " . $uid;
            $db->setQuery($query);
            $staffid = $db->loadResult();
            if ($staffid) {
                $query = "SELECT id FROM `#__js_ticket_staff` WHERE uid = " . $uid . " AND status = 1";
                $db->setQuery($query);
                $staffenabled = $db->loadResult();
                if ($staffenabled) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
    }


    function storeTimeTaken($data,$ref_no,$ref_for){
        $user = JSSupportticketCurrentUser::getInstance();
        if($user->getIsAdmin()){
            return false;
        }
        $created = date('Y-m-d H:i:s');
        $conflict = 0;
        if(!isset($_SESSION['ticket_time_start'][$data['ticketid']])){
            return;
        }
        if(!isset($data['timer_time_in_seconds']) || $data['timer_time_in_seconds'] == ''){
            return;
        }
        $session_time_start = $_SESSION['ticket_time_start'][$data['ticketid']];
        
        $time1 = new DateTime($session_time_start);
        $time2 = new DateTime($created);
        $interval = $time1->diff($time2);
        $systemtime = $interval->format('%s');
        if($data['timer_time_in_seconds'] > $systemtime){
            $conflict = 1;
        }
        $row = $this->getTable('stafftime');
        $data2['ticketid'] =  $data['ticketid'];
        $data2['staffid'] =  $data['staffid'];
        $data2['referencefor'] =  $ref_for;
        $data2['referenceid'] =  $ref_no;
        $data2['usertime'] =  $data['timer_time_in_seconds'];
        $data2['systemtime'] =  $systemtime;
        $data2['conflict'] =  $conflict;
        $data2['description'] =  $data['timer_edit_desc'];
        $data2['status'] =  1;
        $data2['created'] =  $created;
        if (!$row->bind($data2)) {
            $this->setError($row->getError());
            $return_value = false;
        }
        if (!$row->check()) {
            $this->setError($row->getError());
            $return_value = false;
        }
        if (!$row->store()) {
            $this->updateSystemErrors($row->getError());
            $this->setError($row->getError());
            $return_value = false;
        }
    return;
    }
    
    function getTimeTakenByTicketId($id){
        $db = JFactory::getDbo();
        if(!is_numeric($id)) return false;
        $query = "SELECT SUM(usertime) 
                    FROM `#__js_ticket_staff_time`
                    WHERE ticketid = ".$id;
        $db->setQuery($query);
        $total = $db->loadResult();
        return $total;
    }
    function getTimeTakenByTicketIdAndStaffId($id,$staffid){
        $db = JFactory::getDbo();
        if(!is_numeric($id)) return false;
        $query = "SELECT SUM(usertime) 
                    FROM `#__js_ticket_staff_time`
                    WHERE ticketid = ".$id ." AND staffid = ".$staffid;
        $db->setQuery($query);
        $total = $db->loadResult();
        return $total;
    }

    function getAverageTimeByStaffId($id){
        $db = JFactory::getDbo();
        if(!is_numeric($id)) return false;
        $query = "SELECT COUNT(DISTINCT(ticketid)) AS tickets , SUM(usertime) AS usertime , SUM(systemtime) AS systemtime,SUM(conflict) as conflict
                    FROM `#__js_ticket_staff_time`
                    WHERE staffid = ".$id;
        $db->setQuery($query);
        $total = $db->loadObject();
        $result[0] = 0;
        $result[1] = 0;
        if(!empty($total) && $total->tickets > 0){
            $result[0] = $total->usertime / $total->tickets;
            if($total->conflict > 0){
                $result[1] = 1;
            }
        }
        return $result;
    }

    function getTimeTakenByReferenceId($id,$referencefor){
        if(!is_numeric($id)) return false;
        $query = "SELECT usertime 
                    FROM `#__js_ticket_staff_time`
                    WHERE referencefor = ".$referencefor." AND  referenceid = ".$id;
        $db->setQuery($query);
        $time = $db->loadResult();
        return $time;
    }

}
?>
