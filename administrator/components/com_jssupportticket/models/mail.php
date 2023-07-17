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

class JSSupportticketModelMail extends JSSupportTicketModel {

    function __construct() {
        parent::__construct();
    }

    function storeMessage() {
        $data = JFactory::getApplication()->input->post->getArray();
        $row = $this->getTable('staffmail');
        $data['message'] = JFactory::getApplication()->input->get('message', '', 'raw');
        if (!$row->bind($data)) {
            $this->setError($row->getError());
            return SENT_ERROR;
        }
        if (!$row->check()) {
            $this->setError($row->getError());
            return MESSAGE_EMPTY;
        }
        if (!$row->store()) {
            $this->getJSModel('systemerrors')->updateSystemErrors($row->getError());
            $this->setError($row->getError());
            return SENT_ERROR;
        }
        return SENT;
    }

    function storemessagereply() {
        $data = JFactory::getApplication()->input->post->getArray();
        $data['isread'] = 2;

        $row = $this->getTable('staffmail');
        if (!$row->bind($data)) {
            $this->setError($row->getError());
            return SENT_ERROR;
        }
        if (!$row->check()) {
            $this->setError($row->getError());
            return MESSAGE_EMPTY;
        }
        if (!$row->store()) {
            $this->getJSModel('systemerrors')->updateSystemErrors($row->getError());
            $this->setError($row->getError());
            return SENT_ERROR;
        }
        return SENT;
    }

    function getFormData($uid) {
        if (isset($uid))
            if (!is_numeric($uid))
                return false;
        $lists['staff'] = JHtml::_('select.genericList', $this->getJSModel('staff')->getStaff(JText::_('Select Staff')), 'to', 'class="inputbox required inputbox js-ticket-form-field-select"' . '', 'value', 'text', '');
        $unreadmessages = $this->getUnreadMessages($uid);
        $outboxmessages = $this->getOutboxMessages($uid);
        $total = $this->getTotalInboxMessages($uid);
        $result[0] = $lists;
        $result[1] = $unreadmessages;
        $result[2] = $total;
        $result[3] = $outboxmessages;
        return $result;
    }

    function getInboxMessages($subject, $startdate, $enddate, $read, $uid, $limitstart, $limit) {
        if (!is_numeric($uid))
            return false;
        $db = $this->getDbo();
        $wherequery = '';
        $query = "SELECT message.*, concat(staff.firstname,' ',staff.lastname) AS staffname,
					(SELECT COUNT(id) FROM `#__js_ticket_staff_mail` WHERE replytoid = message.id AND isread = 2) AS count
					FROM `#__js_ticket_staff_mail` AS message
					JOIN `#__js_ticket_staff` AS staff ON staff.uid = message.from
					WHERE message.to = " . $uid;
        if ($subject) {
            $subject = trim($subject);
            $wherequery .= " AND message.subject LIKE " . $db->Quote("%" . $subject . "%");
        }
        if ($read) {
            if (!is_numeric($read))
                return false;
            $wherequery .= " AND message.isread = " . $read;
        }
        $config = $this->getJSModel('config')->getConfigs();
        $dateformat = $config['date_format'];
        if(isset($startdate) && !empty($startdate)){
            $dateformat = $config['date_format'];
            if ($dateformat == 'm-d-Y') {
              $arr = explode('-', $startdate);
              $startdate = $arr[2] . '-' . $arr[0] . '-' . $arr[1];
            } elseif ($dateformat == 'd-m-Y' OR $dateformat == 'Y-m-d') {
              $arr = explode('-', $startdate);
              $startdate = $arr[2] . '-' . $arr[1] . '-' . $arr[0];
            }
            $startdate = JHTML::_('date',strtotime($startdate),"Y-m-d H:i:s" );
        }

        if(isset($enddate) && !empty($enddate) ){
            $dateformat = $config['date_format'];
            if ($dateformat == 'm-d-Y') {
              $arr = explode('-', $enddate);
              $enddate = $arr[2] . '-' . $arr[0] . '-' . $arr[1];
            } elseif ($dateformat == 'd-m-Y' OR $dateformat == 'Y-m-d') {
              $arr = explode('-', $enddate);
              $enddate = $arr[2] . '-' . $arr[1] . '-' . $arr[0];
            }
            $enddate = JHTML::_('date',strtotime($enddate),"Y-m-d H:i:s" );
        }        

        if ($startdate && $enddate) {
            $query .= " AND message.created BETWEEN " . $db->Quote($startdate) . " AND " . $db->Quote($enddate);
        } elseif ($startdate) {
            $wherequery .= " AND message.created >= " . $db->Quote($startdate);
        }
        $query .= $wherequery;
        $query .= " ORDER BY message.created DESC, count DESC ";
        $fromquery = "SELECT DISTINCT message.*,concat(staff.firstname,' ',staff.lastname) AS staffname,
						(SELECT COUNT(id) FROM `#__js_ticket_staff_mail` WHERE replytoid = message.id AND isread = 2) AS count
						FROM `#__js_ticket_staff_mail` AS message 
						JOIN `#__js_ticket_staff_mail` AS frommessage ON frommessage.replytoid = message.id
						JOIN `#__js_ticket_staff` AS staff ON staff.uid = message.from
						WHERE message.from = " . $uid . " AND frommessage.replytoid IS NOT NULL AND frommessage.replytoid != '' AND frommessage.from != " . $uid;
        $fromquery .= $wherequery;
        $query = "(" . $query . ") UNION (" . $fromquery . ") ORDER BY count DESC, created DESC";
        $db->setQuery($query, $limitstart, $limit);
        $messages = $db->loadObjectList();
        /* 		SELECT DISTINCT message.*,concat(staff.firstname,' ',staff.lastname) AS staffname
          FROM `g6ln7_js_ticket_staff_mail` AS message
          JOIN g6ln7_js_ticket_staff_mail AS frommessage ON frommessage.replytoid = message.id
          JOIN `g6ln7_js_ticket_staff` AS staff ON staff.uid = message.from
          WHERE message.from = 44 AND frommessage.isread = 2 AND frommessage.replytoid IS NOT NULL AND frommessage.replytoid != '' AND frommessage.from != 44
         */
        $unreadmessages = $this->getUnreadMessages($uid);
        $outboxmessages = $this->getOutboxMessages($uid);
        $total = $this->getTotalInboxMessages($uid);
        $return[0] = $messages;
        $return[1] = $unreadmessages;
        $return[2] = $total; //For total inbox messages
        $return[3] = $outboxmessages; //For outbox messages

        if ($subject)
            $lists['subject'] = $subject;
        if ($startdate)
            $lists['start_date'] = $startdate;
        if ($enddate)
            $lists['end_date'] = $enddate;
        if ($read || $read == 0)
            $lists['read'] = $read;
        if (isset($lists))
            $return[4] = $lists;
        return $return;
    }

    function getOutboxMessages($uid) {
        if (!is_numeric($uid))
            return false;
        $db = $this->getDbo();
        $query = "SELECT Count(message.id)
					FROM `#__js_ticket_staff_mail` AS message
                    JOIN `#__js_ticket_staff` AS staff ON staff.uid = message.to
					WHERE message.from = " . $uid . " AND message.to IS NOT NULL AND message.to != ''";
        $db->setQuery($query);
        $total = $db->loadResult();
        return $total;
    }

    function getUnreadMessages($uid) {
        if (!is_numeric($uid))
            return false;
        $db = $this->getDbo();
        $query = "SELECT (SELECT Count(message.id)
					FROM `#__js_ticket_staff_mail` AS message
					WHERE message.to = " . $uid . " AND message.isread = 2)+
				(SELECT COUNT(DISTINCT message.id)
					FROM `#__js_ticket_staff_mail` AS message 
					JOIN `#__js_ticket_staff_mail` AS frommessage ON frommessage.replytoid = message.id
					JOIN `#__js_ticket_staff` AS staff ON staff.uid = message.from
					WHERE message.from = " . $uid . " AND frommessage.isread = 2 AND frommessage.replytoid IS NOT NULL AND frommessage.replytoid != '' AND frommessage.from != " . $uid . ") AS total";
        $db->setQuery($query);
        $total = $db->loadResult();
        return $total;
    }

    function getMessage($id, $uid) {
        if (!is_numeric($uid))
            return false;
        if (!is_numeric($id))
            return false;
        $db = $this->getDbo();
        $query = "SELECT message.*,concat(staff.firstname,' ',staff.lastname) AS staffname,staff.photo AS staffphoto,staff.id AS staffid
					FROM `#__js_ticket_staff_mail` AS message
                    JOIN `#__js_ticket_staff` AS staff ON staff.uid = message.from
					WHERE message.id = " . $id." and (message.from =".$uid." or message.to=".$uid.")";
        $db->setQuery($query);
        $message = $db->loadObject();
        $this->messageSetAsRead($id, $uid);
        $query = "SELECT message.*,concat(staff.firstname,' ',staff.lastname) AS staffname,staff.photo AS staffphoto,staff.id AS staffid
					FROM `#__js_ticket_staff_mail` AS message
					JOIN `#__js_ticket_staff` AS staff ON staff.uid = message.from
					WHERE message.replytoid = " . $id;
        $db->setQuery($query);
        $replies = $db->loadObjectList();
        $unreadmessages = $this->getUnreadMessages($uid);
        $outboxmessages = $this->getOutboxMessages($uid);
        $total = $this->getTotalInboxMessages($uid);
        $result[0] = $message;
        $result[1] = $unreadmessages;
        $result[2] = $total;
        $result[3] = $outboxmessages;
        $result[4] = $message!=null ? $message->id : 0;
        $result[5] = $replies;
        return $result;
    }

    function getTotalInboxMessages($uid) {
        if (!is_numeric($uid))
            return false;
        $db = $this->getDbo();
        $query = "SELECT (SELECT Count(message.id)
					FROM `#__js_ticket_staff_mail` AS message
					WHERE message.to = " . $uid . ")+
				(SELECT COUNT(DISTINCT message.id)
					FROM `#__js_ticket_staff_mail` AS message 
					JOIN `#__js_ticket_staff_mail` AS frommessage ON frommessage.replytoid = message.id
					JOIN `#__js_ticket_staff` AS staff ON staff.uid = message.from
					WHERE message.from = " . $uid . " AND frommessage.replytoid IS NOT NULL AND frommessage.replytoid != '' AND frommessage.from != " . $uid . ") AS total";
        $db->setQuery($query);
        $total = $db->loadResult();
        return $total;
    }

    function messageSetAsRead($id, $uid) {
        if (!is_numeric($id))
            return false;
        $row = $this->getTable('staffmail');
        $data['id'] = $id;
        $data['isread'] = 1;
        $this->setRepliesAsRead($id, $uid);
        if (!$row->bind($data)) {
            $this->setError($row->getError());
            return false;
        }
        // if (!$row->check()) {
        //     $this->setError($row->getError());
        //     return false;
        // }
        if (!$row->store()) {
           $this->getJSModel('systemerrors')->updateSystemErrors($row->getError());
            $this->setError($row->getError());
            echo $row->getError();
            return false;
        }
        return true;
    }

    function setRepliesAsRead($id, $uid) {
        if (!is_numeric($id))
            return false;
        if(!is_numeric($uid)) return false;
        $db = $this->getDbo();
        $query = "UPDATE `#__js_ticket_staff_mail` AS message SET message.isread = 1 WHERE message.replytoid =" . $id . " AND message.from != " . $uid;
        $db->setQuery($query);
        if (!$db->execute($query))
            return false;
        else
            return true;
    }

    function setAsRead() {
        $cids = JFactory::getApplication()->input->get('cid', array(0), 'post', 'array');
        $row = $this->getTable('staffmail');
        $readall = 1;
        foreach ($cids as $cid) {
            if (!is_numeric($cid))
                return false;
            $data['id'] = $cid;
            $data['isread'] = 1;
            if (!$row->bind($data)) {
                $this->setError($row->getError());
                $readall++;
            }
            if (!$row->check()) {
                $this->setError($row->getError());
                $readall++;
            }
            if (!$row->store()) {
                $this->getJSModel('systemerrors')->updateSystemErrors($row->getError());
                $this->setError($row->getError());
                $readall++;
            }
        }
        if ($readall == 1) 
            return MAIL_MARKED;
        else{
            $readall = $readall-1;
            JSSupportticketMessage::$recordid = $readall;
            return MAIL_MARKED_ERROR;
        }
    }

    function setAsUnread() {
        $cids = JFactory::getApplication()->input->get('cid', array(0), 'post', 'array');
        $row = $this->getTable('staffmail');
        $readall = 1;
        foreach ($cids as $cid) {
            if (!is_numeric($cid))
                return false;
            $data['id'] = $cid;
            $data['isread'] = 2;
            if (!$row->bind($data)) {
                $this->setError($row->getError());
                $readall++;
            }
            if (!$row->check()) {
                $this->setError($row->getError());
                $readall++;
            }
            if (!$row->store()) {
                $this->getJSModel('systemerrors')->updateSystemErrors($row->getError());
                $this->setError($row->getError());
                $readall++;
            }
        }
        if ($readall == 1) 
            return MAIL_MARKED;
        else{
            $readall = $readall-1;
            JSSupportticketMessage::$recordid = $readall;
            return MAIL_MARKED_ERROR;
        }
    }

    function deleteMessages() {
        $cids = JFactory::getApplication()->input->get('cid', array(0), 'post', 'array');
        $row = $this->getTable('staffmail');
        $deleteall = 1;
        foreach ($cids as $cid) {
            if ($this->messageCanDelete($cid) == true) {
                if (!$row->delete($cid)) {
                    $this->setError($row->getErrorMsg());
                    return DELETE_ERROR;
                }
            }else
                $deleteall++;
        }
        if ($deleteall == 1) 
            return DELETED;
        else{
            $deleteall = $deleteall-1;
            JSSupportticketMessage::$recordid = $deleteall;
            return DELETE_ERROR;
        }
    }

    function deleteMessage($id) {
        if($id){
            $row = $this->getTable('staffmail');
            if ($this->messageCanDelete($id) == true) {
                if (!$row->delete($id)) {
                    $this->setError($row->getErrorMsg());
                    return DELETE_ERROR;
                }else
                    return DELETED;
            }else{ return IN_USE;}
        }
    }

    function messageCanDelete($id) { 
        if (is_numeric($id) == false) return false;
        return true;
    }

    function getOutboxMessagesForOutbox($subject, $startdate, $enddate, $uid, $limitstart, $limit) {
        if (!is_numeric($uid))
            return false;
        $db = $this->getDbo();
        $query = "SELECT message.*, concat(staff.firstname,' ',staff.lastname) AS staffname
					FROM `#__js_ticket_staff_mail` AS message
					JOIN `#__js_ticket_staff` AS staff ON staff.uid = message.to
					WHERE message.from = " . $uid;
        if ($subject) {
            $subject = trim($subject);
            $query .= " AND message.subject LIKE '%" .addslashes($subject)."%'";
        }
        $config = $this->getJSModel('config')->getConfigs();
        $dateformat = $config['date_format'];
        if(isset($startdate) && !empty($startdate)){
            $dateformat = $config['date_format'];
            if ($dateformat == 'm-d-Y') {
              $arr = explode('-', $startdate);
              $startdate = $arr[2] . '-' . $arr[0] . '-' . $arr[1];
            } elseif ($dateformat == 'd-m-Y' OR $dateformat == 'Y-m-d') {
              $arr = explode('-', $startdate);
              $startdate = $arr[2] . '-' . $arr[1] . '-' . $arr[0];
            }
            $startdate = JHTML::_('date',strtotime($startdate),"Y-m-d H:i:s" );
        }

        if(isset($enddate) && !empty($enddate) ){
            $dateformat = $config['date_format'];
            if ($dateformat == 'm-d-Y') {
              $arr = explode('-', $enddate);
              $enddate = $arr[2] . '-' . $arr[0] . '-' . $arr[1];
            } elseif ($dateformat == 'd-m-Y' OR $dateformat == 'Y-m-d') {
              $arr = explode('-', $enddate);
              $enddate = $arr[2] . '-' . $arr[1] . '-' . $arr[0];
            }
            $enddate = JHTML::_('date',strtotime($enddate),"Y-m-d H:i:s" );
        }
        if ($startdate && $enddate) {
            $query .= " AND message.created BETWEEN " . $db->Quote($startdate) . " AND " . $db->Quote($enddate);
        } elseif ($startdate) {
            $query .= " AND message.created >= " . $db->Quote($startdate);
        }
        $query .= " ORDER BY message.created DESC ";
        $db->setQuery($query, $limitstart, $limit);
        $messages = $db->loadObjectList();
        $unreadmessages = $this->getUnreadMessages($uid);
        $outboxmessages = $this->getOutboxMessages($uid);
        $total = $this->getTotalInboxMessages($uid);
        $return[0] = $messages;
        $return[1] = $unreadmessages;
        $return[2] = $total; //For total inbox messages
        $return[3] = $outboxmessages; //For outbox messages

        if ($subject)
            $lists['subject'] = $subject;
        if ($startdate)
            $lists['start_date'] = $startdate;
        if ($enddate)
            $lists['end_date'] = $enddate;
        if (isset($lists))
            $return[4] = $lists;

        return $return;
    }
}

?>
