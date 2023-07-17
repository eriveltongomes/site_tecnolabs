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

class JSSupportticketModelEmailbanlist extends JSSupportTicketModel {

    function __construct() {
        parent::__construct();
    }

    function getFormData($id) {
        $db = $this->getDbo();
        $email;
        if (isset($id)) {
            if (!is_numeric($id))
                return False;
            $query = "SELECT * From `#__js_ticket_email_banlist` WHERE id =" . $id;
            $db->setQuery($query);
            $email = $db->loadObject();
        }
        $result[0] = $email;
        return $result;
    }

    function banEmail($data) {
        $isbaned = $this->checkIsBaned($data['email']);
        if ($isbaned) {
            return ALREADY_EXIST;
        }
        $row = $this->getTable('emailbanlist');
        $user = JSSupportticketCurrentUser::getInstance();
        if($user->getIsAdmin())
            $data['submitter'] = $user->getId();
        else
            $data['submitter'] = $user->getStaffId();
        if (!$row->bind($data)) {
            $this->setError($row->getError());
            return SAVE_ERROR;
        }
        if (!$row->store()) {
            $this->setError($row->getError());
            $this->getJSModel('systemerrors')->updateSystemErrors($row->getError());
            return SAVE_ERROR;
        }
        $this->getJSModel('email')->sendMail(2,1,$row->id); // Mailfor,banemail and closeticket ,Ticketid
        JSSupportticketMessage::$recordid = $row->id;
        return SAVED;
    }

    function checkIsBaned($email) {
        if(empty($email)) return false;
        $db = $this->getDbo();
        $query = "SELECT COUNT(id) FROM `#__js_ticket_email_banlist` WHERE email = " . $db->quote($email);
        $db->setQuery($query);
        $total = $db->loadResult();
        if($total > 0)
            return true;
        else 
            return false;
    }

    function getAllEmails($searchemail, $limitstart, $limit) {
        $db = $this->getDbo();
        $query = "SELECT COUNT(id) FROM `#__js_ticket_email_banlist`";
        $db->setQuery($query);
        $total = $db->loadResult();
        $query = "SELECT email.*,user.name AS username,staff.firstname,staff.lastname
            FROM`#__js_ticket_email_banlist` AS email
            LEFT JOIN `#__users` AS user ON user.id = email.submitter 
            LEFT JOIN `#__js_ticket_staff` AS staff ON staff.id = email.submitter ";
        if (isset($searchemail) && $searchemail <> ''){
            $searchemail = trim($searchemail);
            $query .= " WHERE email.email LIKE " . $db->quote('%' . $searchemail . '%');
        }
        $db->setQuery($query, $limitstart, $limit);
        $emails = $db->loadObjectList();
        $lists['searchemail'] = $searchemail;
        $result[0] = $emails;
        $result[1] = $total;
        $result[2] = $lists;
        return $result;
    }

    function deleteEmail() {
        $row = $this->getTable('emailbanlist');
        $c_id = JFactory::getApplication()->input->get('cid', array(0), '', 'array');
        foreach ($c_id as $id) {
            if ($this->emailCanDelete($id) == true) {
                if (!$row->delete($id)) {
                    $this->setError($row->getErrorMsg());
                    return DELETE_ERROR;
                }
            }
        }
        return DELETED;
    }

    function emailCanDelete($id) {
        if (!is_numeric($id)) return false;
        return true;
    }
    
    function banEmailTicket($email,$created,$ticketid,$callfrom){
        if(empty($email))
            return false;
        $activity_log = $this->getJSModel('activitylog');
        $user = JSSupportticketCurrentUser::getInstance();
        $eventtype = JText::_('Ban email');
        $staffid = 0;
        if($user->getIsAdmin()){
            $msg1 = JText::_('Admin');
            $staffid = $user->getId();
        }else{
            $per = $user->checkUserPermission('Ban Email And Close Ticket');
            if ($per == false)
                return PERMISSION_ERROR;
            $msg1 = JText::_('Staff');
            $staffid = $user->getStaffId();
        }

        $row = $this->getTable('emailbanlist');
        $data['email'] = $email;
        $data['submitter'] = $staffid;
        $data['created'] = $created;

        if (!$row->bind($data)){
            $this->setError($row->getError());
            $return_value=false;
        }
        if (!$row->check()){
            $this->setError($row->getError());
            $return_value=false;
        }
        if (!$row->store()){
            $this->updateSystemErrors($row->getError());
            $this->setError($row->getError());
            $return_value=false;
        }
        if(isset($return_value) && $return_value==false){
            $message = $row->getError();
            $activity_log->storeActivityLog($ticketid,1,$eventtype,$message,'Error');
            return TICKET_EMAIL_BAN_ERROR;
        }
        $msg = JText::_('Email is banned by');
        $message = $msg ." ".$user->getName(). ". ( ".$msg1." ) " ;
        $activity_log->storeActivityLog($ticketid,1,$eventtype,$message,'Sucessfull');
        
        if($callfrom==1){
            $this->getJSModel('email')->sendMail(2,1,$ticketid,'js_ticket_tickets'); // Mailfor,Ban Email,Ticketid
        }else{
            $this->getJSModel('email')->sendMail(2,1,$ticketid); // Mailfor,Ban Email,Ticketid
        }


        return TICKET_EMAIL_BAN;
    }

   
    function unbanEmailTicket($email, $id) {
        if(!is_numeric($id))
            return false;
        if(empty($email))
            return false;
        $user = JSSupportticketCurrentUser::getInstance();
        $activity_log = $this->getJSModel('activitylog');
        $eventtype = 'Unban Email';
        if($user->getIsAdmin()){
            $msg1 = JText::_('Admin');
        }else{
            $per = $user->checkUserPermission('Unban Email');
            if ($per == false)
                return PERMISSION_ERROR;
            $msg1 = JText::_('Staff');
        }

        $db = $this->getDbo();
        $query = "SELECT COUNT(id) FROM `#__js_ticket_email_banlist` WHERE email = " . $db->quote($email);
        $db->setQuery($query);
        $result = $db->loadResult();
        if ($result == 0) {
            return EMAIL_NOT_EXIST;
        }

        $query = "DELETE FROM `#__js_ticket_email_banlist` WHERE email = " . $db->quote($email);
        $db->setQuery($query);
        if (!$db->execute()) {
            $message = $row->getError();
            $activity_log->storeActivityLog($id,1,$eventtype,$message,'Error');
            return TICKET_EMAIL_UNBAN_ERROR;
        }
        $msg = JText::_('Email is unbanned by');
        $message = $msg . " " . $user->getName() . ". ( " . $msg1 . " ) ";
        $activity_log->storeActivityLog($id,1,$eventtype,$message,'Sucessfull');
        $this->getJSModel('email')->sendMail(2,2,$id,'js_ticket_tickets'); // Mailfor,unBan Email,Ticketid
        return TICKET_EMAIL_UNBAN;
    }
}

?>
