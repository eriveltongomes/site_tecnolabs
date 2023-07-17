<?php
/**
 * @Copyright Copyright (C) 2012 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Company:     Buruj Solutions
  + Contact:        www.burujsolutions.com , info@burujsolutions.com
 * Created on:  May 03, 2012
  ^
  + Project:    JS Tickets
  ^
 */
defined('_JEXEC') or die('Not Allowed');

jimport('joomla.application.component.model');
jimport('joomla.html.html');

class JSSupportticketModelFeedback extends JSSupportTicketModel {

    function __construct() {
        parent::__construct();
    }    

    function getFeedBackForFrom(){
        $fields = $this->getJSModel('userfields')->getFieldsOrderingforForm(2);
        return $fields;   
    }

    function storeFeedback($data) {
        $db = $this->getDBO();
        //custom field code start
        //custom field code start
        $customflagforadd = false;
        $customflagfordelete = false;
        $custom_field_namesforadd = array();
        $custom_field_namesfordelete = array();
        $userfield = $this->getJSModel('userfields')->getUserfieldsfor(2);
        $params = '';
        foreach ($userfield AS $ufobj) {
            $vardata = '';
            if($ufobj->userfieldtype == 'file'){
                if(isset($data[$ufobj->field.'_1']) && $data[$ufobj->field.'_1']== 0){
                    $vardata = $data[$ufobj->field.'_2'];
                }else{
                    $vardata = $_FILES[$ufobj->field]['name'];
                }
                $config = $this->getJSModel('config')->getConfigByFor('default');
                $model_attachment = $this->getJSModel('attachments');
                $file_size = $config['filesize'];
                if($_FILES[$ufobj->field]['size'] > ($file_size * 1024)){
                    $vardata = '';
                }else{
                    if ($_FILES[$ufobj->field]['name'] != "") {
                        $is_allow = $model_attachment->checkExtension($_FILES[$ufobj->field]['name']);
                        if($is_allow == 'N'){
                            $vardata = '';
                        }else{
                            $vardata = $_FILES[$ufobj->field]['name'];
                            $customflagforadd=true;
                            $custom_field_namesforadd[]=$ufobj->field;
                        }
                    } 
                }
            }else{
                $vardata = isset($data[$ufobj->field]) ? $data[$ufobj->field] : '';
            }
            if(isset($data[$ufobj->field.'_1']) && $data[$ufobj->field.'_1'] == 1){
                $customflagfordelete = true;
                $custom_field_namesfordelete[]= $data[$ufobj->field.'_2'];
            }
            if($vardata != ''){
                //had to comment this so that multpli field should work properly
                // if($ufobj->userfieldtype == 'multiple'){
                //     $vardata = explode(',', $vardata[0]); // fixed index
                // }
                if(is_array($vardata)){
                    $vardata = implode(', ', $vardata);
                }
                $params[$ufobj->field] = htmlspecialchars($vardata);
            }
        }
        if($data['id'] != ''){
            if(is_numeric($data['id'])){
                $db = $this->getDbo();
                $query = "SELECT params FROM `#__js_ticket_tickets` WHERE id = " . $data['id'];
                $db->setQuery($query);
                $oParams = $db->loadResult();

                if(!empty($oParams)){
                    $oParams = json_decode($oParams,true);
                    $unpublihsedFields = $this->getJSModel('userfields')->getUserUnpublishFieldsfor(2);
                    foreach($unpublihsedFields AS $field){
                        if(isset($oParams[$field->field])){
                            $params[$field->field] = $oParams[$field->field];
                        }
                    }
                }
            }
        }
        if (!empty($params)) {
            $params = json_encode($params);
        }
        //$remarks = JFactory::getApplication()->input->get( 'remarks', '', 'post','string', JREQUEST_ALLOWHTML ); // use jsticket_message to avoid conflict
        $remarks = JFactory::getApplication()->input->get('remarks', '', 'raw');
        $data2['params'] = $params;
        $data2['id'] = $data['id'];
        $data2['ticketid'] = $data['ticketid'];
        $data2['rating'] = $data['rating'];
        $data2['remarks'] = $remarks;
        $data2['status'] = 1;
        $data2['created'] = $data['created'];
        $row = $this->getTable('feedback');
        if (!$row->bind($data2)) {
            $this->setError($row->getError());
            echo $row->getError();
            $return_value = false;
        }
        if (!$row->store()) {
            $this->updateSystemErrors($row->getError());
            $this->setError($row->getError());
            echo $row->getError();
            $return_value = false;
        }
        if (isset($return_value) && $return_value == false) {
            $message = $row->getError();
            $this->activity_log->storeActivityLog($row->id, 1, $eventtype, $message, 'Error');
            return SAVE_ERROR;
        }

        $ticketid = $data['ticketid'];
        if($customflagfordelete == true){
            foreach ($custom_field_namesfordelete as $key) {
                $res = $this->getJSModel('ticket')->removeFileCustom($ticketid,$key);
            }
        }
        //storing custom field attachments
        if($customflagforadd == true){
            foreach ($custom_field_namesforadd as $key) {
                if ($_FILES[$key]['size'] > 0) { // logo
                    $res = $this->getJSModel('ticket')->uploadFileCustom($ticketid,$key);
                }
            }
        }
        return;
    }

    function getIdFromFeedbackId($ticketid) {
        if (!is_numeric($ticketid))
            return false;
        $db = $this->getDBO();
        $query = "SELECT id FROM `#__js_ticket_feedbacks` 
                WHERE ticketid = " . $ticketid;
        $db->setQuery($query);
        $result = $db->loadResult();
        if($result > 0){
            return false;
        }else{
            return true;
        }
    }

    function getAllFeedbacks($subject,$ticketid,$staffid,$from,$departmentid,$limitstart,$limit){

        $db = $this->getDBO();
        $inquery = '';
        if ($ticketid != null){
            $ticketid = trim($ticketid);
            $inquery .= " AND ticket.ticketid LIKE '%$ticketid%'";
        }
        
        if ($from != null){
            $from = trim($from);
            $inquery .= " AND ticket.name LIKE '%$from%'";
        }
        
        if ($subject != null) {
            $subject = trim($subject);
            $inquery .= " AND ticket.subject LIKE '%$subject%'";
        }
        if ($staffid) {
            if (is_numeric($staffid)) {
                $inquery .= " AND ticket.staffid = " . $staffid;
            }
        }
        if ($departmentid) {
            if (is_numeric($departmentid)) {
                $inquery .= " AND ticket.departmentid = " . $departmentid;
            }
        }
        //filter combo boxes
        $departments = $this->getJSModel('department')->getDepartments();
        $lists['subject'] = $subject;
        $lists['from'] = $from;
        $lists['ticketid'] = $ticketid;
        $lists['staffmembers'] = JHTML::_('select.genericList', $this->getJSModel('staff')->getStaffMembers(), 'staffid', 'class="inputbox js-ticket-select-field " ' . '', 'value', 'text', $staffid);
        $lists['departments'] = JHTML::_('select.genericList', $departments, 'departmentid', 'class="inputbox js-ticket-select-field " ' . '', 'value', 'text',$departmentid);
        
        // Pagination
        $query = "SELECT COUNT(feedback.id) 
                    FROM `#__js_ticket_feedbacks` AS feedback
                    JOIN `#__js_ticket_tickets` AS ticket ON ticket.id = feedback.ticketid
                    LEFT JOIN `#__js_ticket_departments` AS department ON ticket.departmentid = department.id
                    LEFT JOIN `#__js_ticket_staff` AS staff ON ticket.staffid = staff.id
                    WHERE 1 = 1 ";
        $query .= $inquery;
        $db->setQuery($query);
        $total = $db->loadResult();
        if ($total <= $limitstart)
            $limitstart = 0;

        // Data
        $query = "SELECT feedback.*,ticket.name, ticket.subject, ticket.id as ticketid, ticket.ticketid AS trackingid,ticket.name,department.departmentname ,staff.firstname,staff.lastname
                    FROM `#__js_ticket_feedbacks` AS feedback 
                    JOIN `#__js_ticket_tickets` AS ticket ON ticket.id = feedback.ticketid
                    LEFT JOIN `#__js_ticket_departments` AS department ON ticket.departmentid = department.id
                    LEFT JOIN `#__js_ticket_staff` AS staff ON ticket.staffid = staff.id
                    WHERE 1 = 1 ";
        $query .= $inquery;
        $db->setQuery($query, $limitstart, $limit);
        $result[0] = $db->loadObjectList();
        $result[1] = $total;
        $result[2] = $lists;
        return $result;
    }

    // feed back email by cron job

     function sendFeedbackMail() {
        $db = $this->getDBO();
        $config = $this->getJSModel('config')->getConfigs();
        if($config['feedback_email_delay_type'] == 1){
            $intrval_string = " date(DATE_ADD(closed,INTERVAL " . (int)$config['feedback_email_delay']." DAY)) < '".date("Y-m-d")."'";
        }else{
            $intrval_string = " DATE_ADD(closed,INTERVAL " .(int) $config['feedback_email_delay'] . " HOUR) < '".date("Y-m-d H:i:s")."'";
        }
        // select closed ticket 
        $query = "SELECT id FROM `#__js_ticket_tickets` WHERE ".$intrval_string." AND status = 4 AND (feedbackemail != 1  OR feedbackemail IS NULL) AND closed IS NOT NULL";
        $db->setQuery($query);
        $ticketids = $db->loadObjectList();
        if(!empty($ticketids)){
            foreach ($ticketids as $key) {
                if(is_numeric($key->id)){
                    $this->sendFeedbackMailByTicketid($key->id);
                }
            }
        }
        return;
    }

    function sendFeedbackMailByTicketid($ticketid) {
        $db = $this->getDBO();
        if (!is_numeric($ticketid))
            return false;
        $query = "UPDATE `#__js_ticket_tickets` SET feedbackemail = 1  WHERE id = " . $ticketid;
        $db->setQuery($query);
        $db->execute();
        $this->getJSModel('email')->sendMail(1,15,$ticketid); // 15 is for feedback email
        return;
    }
}
?>
