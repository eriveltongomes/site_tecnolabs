<?php

/**
 * @Copyright Copyright (C) 2015 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Company:     Buruj Solutions
 + Contact:     www.burujsolutions.com , info@burujsolutions.com
 * Created on:  May 22, 2015
  ^
  + Project:    JS Tickets
  ^
 */
defined('_JEXEC') or die('Not Allowed');

jimport('joomla.application.component.model');
jimport('joomla.html.html');

class JSSupportticketModelEmail extends JSSupportTicketModel {

    function __construct(){
        parent::__construct();
    }

    function getFormData($id) {

        $db = $this->getDbo();
        $email;
        if (isset($id)) {
            if (!is_numeric($id))
                return False;
            $query = "SELECT * FROM `#__js_ticket_email` WHERE id =" . $id;
            $db->setQuery($query);
            $email = $db->loadObject();
        }
        $priority = $this->getJSModel('priority')->getPriority(JText::_('Select Priority'));
        $config = $this->getJSModel('config')->getConfigByFor('default');

        if (isset($email)) {
            $priorityid = $email->priorityid;
            $lists['priority'] = JHTML::_('select.genericList', $priority, 'priorityid', 'class="inputbox" ' . '', 'value', 'text', $priorityid);
        } else {
            $priorityid = '';
            $lists['priority'] = JHTML::_('select.genericList', $priority, 'priorityid', 'class="inputbox" ' . '', 'value', 'text', $config['priority']);
        }

        $result[0] = $email;
        $result[1] = '';
        $result[2] = $lists;

        return $result;
    }

    function storeEmail($data) {
        if(!$data['id'])
        if($this->checkAlreadyExist($data['email'])){
            return ALREADY_EXIST;
        }
        if($data['password'] != ""){
            $data['password'] = base64_encode($data['password']);
        }
        $row = $this->getTable('emails');
        if (!$row->bind($data)) {
            $this->setError($row->getError());
            return SAVE_ERROR;
        }
        try
        {
            $row->store();
        }
        catch (RuntimeException $e)
        {
            $this->getJSModel('systemerrors')->updateSystemErrors($e);
            $this->setError($e);
            return SAVE_ERROR;
        }
        JSSupportticketMessage::$recordid = $row->id;
        return SAVED;
    }

    function checkAlreadyExist($email){
        $db = JFactory::getDbo();
        $query = "SELECT COUNT(id) FROM `#__js_ticket_email` WHERE email = '".$email."'";
        $db->setQuery($query);
        $result = $db->loadResult();
        if($result > 0)
            return true;
        else
            return false;
    }

    function getAllEmails($searchemail, $searchtype, $limitstart, $limit) {
        $type[] = array('value' => null, 'text' => JText::_('Select Email'));
        $type[] = array('value' => 1, 'text' => JText::_('Yes'));
        $type[] = array('value' => 0, 'text' => JText::_('No'));
        $lists['autoresponcetype'] = JHTML::_('select.genericList', $type, 'filter_autoresponcetype', 'class="inputbox" ' . '', 'value', 'text', $searchtype);
        $db = $this->getDbo();
        //For Total Record
        $query = "SELECT COUNT(id) From `#__js_ticket_email`";
        $db->setQuery($query);
        $total = $db->loadResult();

        $query = "SELECT email.id, email.email, email.autoresponce, email.created, email.update,priority.priority
                    FROM `#__js_ticket_email` AS email
                    LEFT JOIN `#__js_ticket_priorities`AS priority ON priority.id=email.priorityid
                    WHERE email.status <> -1 ";
        if (isset($searchemail) && $searchemail <> ''){
            $searchemail = trim($searchemail);
            $query .= " AND email.email LIKE " . $db->quote('%' . $searchemail . '%');
        }
        if (isset($searchtype) && $searchtype <> '') {
            if (!is_numeric($searchtype))
                return False;
            $query .= " AND email.autoresponce =" . $searchtype;
        }
        $db->setQuery($query, $limitstart, $limit);
        $emails = $db->loadObjectList();
        if ($searchemail)
            $lists['searchemail'] = $searchemail;
        $result[0] = $emails;
        $result[1] = $total;
        $result[2] = $lists;
        return $result;
    }

    function deleteEmail() {
        $row = $this->getTable('emails');
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
        if (!is_numeric($id))
            return FALSE;
        $db = $this->getDBO();
        $query = "SELECT COUNT(id) FROM `#__js_ticket_email` WHERE id=" . $id;
        $db->setQuery($query);
        $total = $db->loadResult();
        if ($total > 0)
            return true;
        else
            return false;
    }

    function getEmailList($title = ''){
        $db= $this->getDbo();
        $query="SELECT id, email FROM `#__js_ticket_email` WHERE status = 1 ORDER BY email ASC";
        try{
            $db->setQuery($query);
            $rows=$db->loadObjectList();
            $emaillist = array();
            if($title)
                $emaillist[]=array('value'=>'','text'=>$title);
            foreach ($rows as $row) {
                $emaillist[]=array('value'=>$row->id,'text'=>$row->email);
            }
            return $emaillist;
        }
        catch (RuntimeException $e){
            $this->getJSModel('systemerrors')->updateSystemErrors($db->getErrorMsg());
            return false;
        }
    }

    function sendMail($mailfor, $action, $id = null, $tablename = null) {
        if (!is_numeric($mailfor)) return false;
        if (!is_numeric($action)) return false;
        if ($id != null) if (!is_numeric($id)) return false;
        $config = $this->getJSModel('config')->getConfigs();
        switch ($mailfor) {
            case 1: // Mail For Tickets
                switch ($action) {
                    case 1: // New Ticket Created
                        $ticket = $this->getRecordByTablenameAndId('js_ticket_tickets', $id);
                        $username = $ticket->name;
                        $subject = $ticket->subject;
                        $trackingid = $ticket->ticketid;
                        $HelptopicName = $ticket->topic;
                        $email = $ticket->email;
                        $message = $ticket->message;
                        $matcharray = array(
                            '{USERNAME}' => $username,
                            '{SUBJECT}' => $subject,
                            '{TRACKINGID}' => $trackingid,
                            '{HELP_TOPIC}' => $HelptopicName,
                            '{EMAIL}' => $email,
                            '{MESSAGE}' => $message,
                            '{DEPARTMENT}' => $ticket->departmentname,
                            '{PRIORITY}' => $ticket->priority
                        );
                        // code for handling custom fields start
                        if(!empty($ticket->params)){
                            $data = json_decode($ticket->params,true);
                        }
                        $fields = $this->getJSModel('userfields')->getUserfieldsfor(1);
                        if( isset($data) && is_array($data) ){
                            foreach ($fields as $field) {
                                if($field->userfieldtype != 'file'){
                                    $fvalue = '';
                                    if(array_key_exists($field->field, $data)){
                                        $fvalue = $data[$field->field];
                                    }
                                    $matcharray['{'.$field->field.'}'] = $fvalue;// match array new index for custom field
                                }
                            }
                        }
                        // code for handling custom fields end
                        $object = $this->getSenderEmailAndName($id);
                        $senderEmail = $object->email;
                        $senderName = $object->name;

                        // New ticket mail to User
                        $template = $this->getTemplateForEmail('ticket-new');
                        //Parsing template
                        $msgSubject = $template->subject;
                        $msgBody = $template->body;
                        $link = $this->setGuestUrl($trackingid,$email);
                        //echo $link;exit;
                        $matcharray['{TICKETURL}'] = $link;
                        $this->replaceMatches($msgSubject, $matcharray);
                        $this->replaceMatches($msgBody, $matcharray);
                        $msgBody .= '<input type="hidden" name="ticketid:' . $trackingid . '###user####" />';
                        $msgBody .= '<span style="display:none;" ticketid:' . $trackingid . '###user#### ></span>';
                        $this->sendEmail($email, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        // New ticket mail to admin
                        if ($config['new_ticket_admin'] == 1) {
                            $adminEmailid = $config['admin_email'];
                            $adminEmail = $this->getEmailById($adminEmailid);
                            $adminName = $this->getJoomlaNameByEmail($adminEmail);
                            $template = $this->getTemplateForEmail('ticket-new-admin');
                            $msgSubject = $template->subject;
                            $msgBody = $template->body;
                            $link = $this->setAdminUrl($id);
                            $matcharray['{TICKETURL}'] = $link;
                            //$matcharray['{USERNAME}'] = $adminName;
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);
                            $msgBody .= '<input type="hidden" name="ticketid:' . $trackingid . '###admin####" />';
                            $msgBody .= '<span style="display:none;" ticketid:' . $trackingid . '###admin#### ></span>';
                            $this->sendEmail($adminEmail, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        }
                        //Check to send email to department
                        $db = $this->getDbo();
                        $query = "SELECT dept.sendemail, email.email AS emailaddress
                                    FROM `#__js_ticket_tickets` AS ticket
                                    LEFT JOIN `#__js_ticket_departments` AS dept ON dept.id = ticket.departmentid
                                    LEFT JOIN `#__js_ticket_email` AS email ON email.id = dept.emailid
                                    WHERE ticket.id = ".$id;

                        $db->setQuery($query);
                        $dept_result = $db->loadObject();

                        if($dept_result){
                            if(isset($dept_result->sendemail) && $dept_result->sendemail == 1){
                                $deptemail = $dept_result->emailaddress;
                                $template = $this->getTemplateForEmail('ticket-new-admin');
                                $msgSubject = $template->subject;
                                $msgBody = $template->body;
                                $link = $this->setAdminUrl($id);
                                $matcharray['{TICKETURL}'] = $link;
                                $msgBody .= '<input type="hidden" name="ticketid:' . $trackingid . '###admin####" />';
                                $msgBody .= '<span style="display:none;" ticketid:' . $trackingid . '###admin#### ></span>';
                                $this->replaceMatches($msgSubject, $matcharray);
                                $this->replaceMatches($msgBody, $matcharray);
                                $this->sendEmail($deptemail, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                            }
                        }
                        //New ticket mail to staff member
                        if ($config['new_ticket_staff'] == 1) {
                            // new
                            if(isset($ticket->staffid) AND is_numeric($ticket->staffid) AND $ticket->staffid>0){
                                //only send to assigned staff member
                                $staffmembers = $this->getJSModel('staff')->getStaffInfoByStaffId($ticket->staffid);
                            }else{
                                // Get All Staff member of the department of Current Ticket
                                $staffmembers = $this->getJSModel('staff')->getAllStaffMemberByDepId($ticket->departmentid);
                            }
                            foreach ($staffmembers AS $staff) {
                                if($staff->canemail == 1){
                                    $template = $this->getTemplateForEmail('ticket-staff');
                                    //Parsing template
                                    $msgSubject = $template->subject;
                                    $msgBody = $template->body;
                                    $link = $this->setStaffUrl($id);
                                    $matcharray['{TICKETURL}'] = $link;
                                    $matcharray['{USERNAME}'] = $staff->firstname.' '.$staff->lastname;
                                    $this->replaceMatches($msgSubject, $matcharray);
                                    $this->replaceMatches($msgBody, $matcharray);
                                    $msgBody .= '<input type="hidden" name="ticketid:' . $trackingid . '###staff####" />';
                                    $msgBody .= '<span style="display:none;" ticketid:' . $trackingid . '###staff#### ></span>';
                                    $this->sendEmail($staff->email, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                                }
                            }
                        }
                        break;
                    case 2: // Close Ticket
                        $ticket = $this->getRecordByTablenameAndId('js_ticket_tickets', $id);
                        $username = $ticket->name;
                        $subject = $ticket->subject;
                        $trackingid = $ticket->ticketid;
                        $email = $ticket->email;
                        $message = $ticket->message;
                        $matcharray = array(
                            '{USERNAME}' => $username,
                            '{SUBJECT}' => $subject,
                            '{TRACKINGID}' => $trackingid,
                            '{EMAIL}' => $email,
                            '{MESSAGE}' => $message,
                            '{DEPARTMENT}' => $ticket->departmentname,
                            '{PRIORITY}' => $ticket->priority
                        );
                        // code for handling custom fields start
                        if(!empty($ticket->params)){
                            $data = json_decode($ticket->params,true);
                        }
                        $fields = $this->getJSModel('userfields')->getUserfieldsfor(1);
                        if( isset($data) && is_array($data) ){
                            foreach ($fields as $field) {
                                if($field->userfieldtype != 'file'){
                                    $fvalue = '';
                                    if(array_key_exists($field->field, $data)){
                                        $fvalue = $data[$field->field];
                                    }
                                    $matcharray['{'.$field->field.'}'] = $fvalue;// match array new index for custom field
                                }
                            }
                        }
                        // code for handling custom fields end
                        $object = $this->getSenderEmailAndName($id);
                        $senderEmail = $object->email;
                        $senderName = $object->name;
                        $template = $this->getTemplateForEmail('close-tk');
                        // Close ticket mail to admin
                        if ($config['ticket_close_admin'] == 1) {
                            $adminEmailid = $config['admin_email'];
                            $adminEmail = $this->getEmailById($adminEmailid);
                            $link = $this->setAdminUrl($id);
                            $matcharray['{TICKETURL}'] = $link;
                            $msgSubject = $template->subject;
                            $msgBody = $template->body;
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);

                            $this->sendEmail($adminEmail, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        }
                        // Close ticket mail to staff member
                        if ($config['ticket_close_staff'] == 1) {
                            $staffEmail = $this->getJSModel('staff')->getStaffEmailByStaffId($ticket->staffid);
                            $link = $this->setStaffUrl($id);
                            $matcharray['{TICKETURL}'] = $link;
                            $msgSubject = $template->subject;
                            $msgBody = $template->body;
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);

                            $this->sendEmail($staffEmail, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        }
                        // New ticket mail to User
                        if ($config['ticket_close_user'] == 1) {
                            $link = $this->setUserUrl($id);
                            $matcharray['{TICKETURL}'] = $link;

                            $url = $this->getURL();
                            $private = $trackingid.','.$email;
                            $data = base64_encode($private);
                            $url .= '&c=feedback&task=showfeedbackform&jsticket='.$data;
                            $feedback_url = '<a href="'.$url.'" target="_blank">'. JText::_('Click here to give us feedback') .'</a>';
                            $matcharray['{FEEDBACKURL}'] = $feedback_url;
                            $msgSubject = $template->subject;
                            $msgBody = $template->body;
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);

                            $this->sendEmail($email, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        }
                        break;
                    case 3: // Delete Ticket
                        $session = JFactory::getApplication()->getSession();
                        $trackingid = $session->get('ticketid');
                        $email = $session->get('ticketemail');
                        $subject = $session->get('ticketsubject');
                        $matcharray = array(
                            '{TRACKINGID}' => $trackingid,
                            '{SUBJECT}' => $subject
                        );
                        $object = $this->getSenderEmailAndName(null);
                        $senderEmail = $object->email;
                        $senderName = $object->name;
                        $template = $this->getTemplateForEmail('delete-tk');
                        // Delete ticket mail to admin
                        if ($config['ticket_delete_admin'] == 1) {
                            $adminEmailid = $config['admin_email'];
                            $adminEmail = $this->getEmailById($adminEmailid);
                            $msgSubject = $template->subject;
                            $msgBody = $template->body;
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);
                            $this->sendEmail($adminEmail, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        }
                        // Delete ticket mail to staff
                        if ($config['ticket_delete_staff'] == 1) {
                            $staffEmail = $this->getJSModel('staff')->getStaffEmailByStaffId($data['staffid']);
                            $msgSubject = $template->subject;
                            $msgBody = $template->body;
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);
                            $this->sendEmail($staffEmail, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        }
                        // New ticket mail to User
                        if ($config['ticket_delete_user'] == 1) {
                            $msgSubject = $template->subject;
                            $msgBody = $template->body;
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);
                            $this->sendEmail($email, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        }
                        break;
                    case 4: // Reply Ticket (Admin/Staff Member)
                        $ticket = $this->getRecordByTablenameAndId('js_ticket_tickets', $id);
                        $username = $ticket->name;
                        $subject = $ticket->subject;
                        $trackingid = $ticket->ticketid;
                        $email = $ticket->email;
                        $message = $this->getJSModel('ticket')->getLatestReplyByTicketId($id);
                        $matcharray = array(
                            '{USERNAME}' => $username,
                            '{SUBJECT}' => $subject,
                            '{TRACKINGID}' => $trackingid,
                            '{EMAIL}' => $email,
                            '{MESSAGE}' => $message,
                            '{DEPARTMENT}' => $ticket->departmentname,
                            '{PRIORITY}' => $ticket->priority
                        );
                        // code for handling custom fields start
                        if(!empty($ticket->params)){
                            $data = json_decode($ticket->params,true);
                        }
                        $fields = $this->getJSModel('userfields')->getUserfieldsfor(1);
                        if( isset($data) && is_array($data) ){
                            foreach ($fields as $field) {
                                if($field->userfieldtype != 'file'){
                                    $fvalue = '';
                                    if(array_key_exists($field->field, $data)){
                                        $fvalue = $data[$field->field];
                                    }
                                    $matcharray['{'.$field->field.'}'] = $fvalue;// match array new index for custom field
                                }
                            }
                        }
                        // code for handling custom fields end
                        $object = $this->getSenderEmailAndName($id);
                        $senderEmail = $object->email;
                        $senderName = $object->name;
                        $template = $this->getTemplateForEmail('responce-tk');
                        // Reply ticket mail to admin
                        if ($config['ticket_response_staff_admin'] == 1) {
                            $adminEmailid = $config['admin_email'];
                            $adminEmail = $this->getEmailById($adminEmailid);
                            $link = $this->setAdminUrl($id);
                            $matcharray['{TICKETURL}'] = $link;
                            $msgSubject = $template->subject;
                            $msgBody = $template->body;
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);

                            $msgBody .= '<input type="hidden" name="ticketid:' . $trackingid . '###admin####" />';
                            $this->sendEmail($adminEmail, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        }
                        // Reply ticket mail to staff
                        if ($config['ticket_response_staff_staff'] == 1) {
                            $staffEmail = $this->getJSModel('staff')->getStaffEmailByStaffId($ticket->staffid);
                            $link = $this->setStaffUrl($id);
                            $matcharray['{TICKETURL}'] = $link;
                            $msgSubject = $template->subject;
                            $msgBody = $template->body;
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);
                            $msgBody .= '<input type="hidden" name="ticketid:' . $trackingid . '###staff####" />';
                            $this->sendEmail($staffEmail, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        }
                        // New ticket mail to User
                        if ($config['ticket_response_staff_user'] == 1) {
                            $link = $this->setUserUrl($id);
                            $matcharray['{TICKETURL}'] = $link;
                            $msgSubject = $template->subject;
                            $msgBody = $template->body;
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);
                            $msgBody .= '<input type="hidden" name="ticketid:' . $trackingid . '###user####" />';

                            $this->sendEmail($email, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        }
                        break;
                    case 5: // Reply Ticket (Ticket Member)
                        $ticket = $this->getRecordByTablenameAndId('js_ticket_tickets', $id);
                        $username = $ticket->name;
                        $subject = $ticket->subject;
                        $trackingid = $ticket->ticketid;
                        $email = $ticket->email;
                        $message = $this->getJSModel('ticket')->getLatestReplyByTicketId($id);
                        $matcharray = array(
                            '{USERNAME}' => $username,
                            '{SUBJECT}' => $subject,
                            '{TRACKINGID}' => $trackingid,
                            '{EMAIL}' => $email,
                            '{MESSAGE}' => $message,
                            '{DEPARTMENT}' => $ticket->departmentname,
                            '{PRIORITY}' => $ticket->priority
                        );
                        // code for handling custom fields start
                        if(!empty($ticket->params)){
                            $data = json_decode($ticket->params,true);
                        }
                        $fields = $this->getJSModel('userfields')->getUserfieldsfor(1);
                        if( isset($data) && is_array($data) ){
                            foreach ($fields as $field) {
                                if($field->userfieldtype != 'file'){
                                    $fvalue = '';
                                    if(array_key_exists($field->field, $data)){
                                        $fvalue = $data[$field->field];
                                    }
                                    $matcharray['{'.$field->field.'}'] = $fvalue;// match array new index for custom field
                                }
                            }
                        }
                        // code for handling custom fields end
                        $object = $this->getSenderEmailAndName($id);
                        $senderEmail = $object->email;
                        $senderName = $object->name;
                        $template = $this->getTemplateForEmail('reply-tk');
                        // New ticket mail to admin
                        if ($config['ticket_reply_user_admin'] == 1) {
                            $adminEmailid = $config['admin_email'];
                            $adminEmail = $this->getEmailById($adminEmailid);
                            $link = $this->setAdminUrl($id);
                            $matcharray['{TICKETURL}'] = $link;
                            $msgSubject = $template->subject;
                            $msgBody = $template->body;
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);
                            $msgBody .= '<input type="hidden" name="ticketid:' . $trackingid . '###admin####" />';

                            $this->sendEmail($adminEmail, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        }
                        // New ticket mail to staff
                        if ($config['ticket_reply_user_staff'] == 1) {
                            $staffEmail = $this->getJSModel('staff')->getStaffEmailByStaffId($ticket->staffid);
                            $link = $this->setStaffUrl($id);
                            $matcharray['{TICKETURL}'] = $link;
                            $msgSubject = $template->subject;
                            $msgBody = $template->body;
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);
                            $msgBody .= '<input type="hidden" name="ticketid:' . $trackingid . '###staff####" />';

                            $this->sendEmail($staffEmail, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        }
                        // New ticket mail to User
                        if ($config['ticket_reply_user_user'] == 1) {
                            $link = $this->setUserUrl($id);
                            $matcharray['{TICKETURL}'] = $link;
                            $msgSubject = $template->subject;
                            $msgBody = $template->body;
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);
                            $msgBody .= '<input type="hidden" name="ticketid:' . $trackingid . '###user####" />';

                            $this->sendEmail($email, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        }
                        break;
                    case 6: // Lock Ticket
                        $ticket = $this->getRecordByTablenameAndId('js_ticket_tickets', $id);
                        $username = $ticket->name;
                        $subject = $ticket->subject;
                        $trackingid = $ticket->ticketid;
                        $email = $ticket->email;
                        $matcharray = array(
                            '{USERNAME}' => $username,
                            '{SUBJECT}' => $subject,
                            '{TRACKINGID}' => $trackingid,
                            '{EMAIL}' => $email,
                            '{DEPARTMENT}' => $ticket->departmentname,
                            '{PRIORITY}' => $ticket->priority
                        );
                        // code for handling custom fields start
                        if(!empty($ticket->params)){
                            $data = json_decode($ticket->params,true);
                        }
                        $fields = $this->getJSModel('userfields')->getUserfieldsfor(1);
                        if( isset($data) && is_array($data) ){
                            foreach ($fields as $field) {
                                if($field->userfieldtype != 'file'){
                                    $fvalue = '';
                                    if(array_key_exists($field->field, $data)){
                                        $fvalue = $data[$field->field];
                                    }
                                    $matcharray['{'.$field->field.'}'] = $fvalue;// match array new index for custom field
                                }
                            }
                        }
                        // code for handling custom fields end
                        $object = $this->getSenderEmailAndName($id);
                        $senderEmail = $object->email;
                        $senderName = $object->name;
                        $template = $this->getTemplateForEmail('lock-tk');
                        // New ticket mail to admin
                        if ($config['ticket_lock_admin'] == 1) {
                            $adminEmailid = $config['admin_email'];
                            $adminEmail = $this->getEmailById($adminEmailid);
                            $link = $this->setAdminUrl($id);
                            $msgSubject = $template->subject;
                            $msgBody = $template->body;
                            $matcharray['{TICKETURL}'] = $link;
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);

                            $this->sendEmail($adminEmail, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        }
                        // New ticket mail to staff
                        if ($config['ticket_lock_staff'] == 1) {
                            $staffEmail = $this->getJSModel('staff')->getStaffEmailByStaffId($ticket->staffid);
                            $link = $this->setStaffUrl($id);
                            $msgSubject = $template->subject;
                            $msgBody = $template->body;
                            $matcharray['{TICKETURL}'] = $link;
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);

                            $this->sendEmail($staffEmail, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        }
                        // New ticket mail to User
                        if ($config['ticket_lock_user'] == 1) {
                            $link = $this->setUserUrl($id);
                            $msgSubject = $template->subject;
                            $msgBody = $template->body;
                            $matcharray['{TICKETURL}'] = $link;
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);

                            $this->sendEmail($email, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        }
                        break;
                    case 7: // Unlock Ticket
                        $ticket = $this->getRecordByTablenameAndId('js_ticket_tickets', $id);
                        $username = $ticket->name;
                        $subject = $ticket->subject;
                        $trackingid = $ticket->ticketid;
                        $email = $ticket->email;
                        $matcharray = array(
                            '{USERNAME}' => $username,
                            '{SUBJECT}' => $subject,
                            '{TRACKINGID}' => $trackingid,
                            '{EMAIL}' => $email,
                            '{DEPARTMENT}' => $ticket->departmentname,
                            '{PRIORITY}' => $ticket->priority
                        );
                        // code for handling custom fields start
                        if(!empty($ticket->params)){
                            $data = json_decode($ticket->params,true);
                        }
                        $fields = $this->getJSModel('userfields')->getUserfieldsfor(1);
                        if( isset($data) && is_array($data) ){
                            foreach ($fields as $field) {
                                if($field->userfieldtype != 'file'){
                                    $fvalue = '';
                                    if(array_key_exists($field->field, $data)){
                                        $fvalue = $data[$field->field];
                                    }
                                    $matcharray['{'.$field->field.'}'] = $fvalue;// match array new index for custom field
                                }
                            }
                        }
                        // code for handling custom fields end
                        $object = $this->getSenderEmailAndName($id);
                        $senderEmail = $object->email;
                        $senderName = $object->name;
                        $template = $this->getTemplateForEmail('unlock-tk');
                        // New ticket mail to admin
                        if ($config['ticket_unlock_admin'] == 1) {
                            $adminEmailid = $config['admin_email'];
                            $adminEmail = $this->getEmailById($adminEmailid);
                            $link = $this->setAdminUrl($id);
                            $msgSubject = $template->subject;
                            $msgBody = $template->body;
                            $matcharray['{TICKETURL}'] = $link;
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);

                            $this->sendEmail($adminEmail, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        }
                        // New ticket mail to staff
                        if ($config['ticket_unlock_staff'] == 1) {
                            $staffEmail = $this->getJSModel('staff')->getStaffEmailByStaffId($ticket->staffid);
                            $link = $this->setStaffUrl($id);
                            $msgSubject = $template->subject;
                            $msgBody = $template->body;
                            $matcharray['{TICKETURL}'] = $link;
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);

                            $this->sendEmail($staffEmail, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        }
                        // New ticket mail to User
                        if ($config['ticket_unlock_user'] == 1) {
                            $link = $this->setUserUrl($id);
                            $msgSubject = $template->subject;
                            $msgBody = $template->body;
                            $matcharray['{TICKETURL}'] = $link;
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);

                            $this->sendEmail($email, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        }
                        break;
                    case 8: // Markoverdue Ticket
                        $ticket = $this->getRecordByTablenameAndId('js_ticket_tickets', $id);
                        $trackingid = $ticket->ticketid;
                        $email = $ticket->email;
                        $subject = $ticket->subject;
                        $matcharray = array(
                            '{TRACKINGID}' => $trackingid,
                            '{SUBJECT}' => $subject,
                            '{DEPARTMENT}' => $ticket->departmentname,
                            '{PRIORITY}' => $ticket->priority
                        );
                        // code for handling custom fields start
                        if(!empty($ticket->params)){
                            $data = json_decode($ticket->params,true);
                        }
                        $fields = $this->getJSModel('userfields')->getUserfieldsfor(1);
                        if( isset($data) && is_array($data) ){
                            foreach ($fields as $field) {
                                if($field->userfieldtype != 'file'){
                                    $fvalue = '';
                                    if(array_key_exists($field->field, $data)){
                                        $fvalue = $data[$field->field];
                                    }
                                    $matcharray['{'.$field->field.'}'] = $fvalue;// match array new index for custom field
                                }
                            }
                        }
                        // code for handling custom fields end
                        $object = $this->getSenderEmailAndName($id);
                        $senderEmail = $object->email;
                        $senderName = $object->name;
                        $template = $this->getTemplateForEmail('moverdue-tk');
                        // New ticket mail to admin
                        if ($config['ticket_overdue_admin'] == 1) {
                            $adminEmailid = $config['admin_email'];
                            $adminEmail = $this->getEmailById($adminEmailid);
                            $link = $this->setAdminUrl($id);
                            $msgSubject = $template->subject;
                            $msgBody = $template->body;
                            $matcharray['{TICKETURL}'] = $link;
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);

                            $this->sendEmail($adminEmail, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        }
                        // New ticket mail to staff
                        if ($config['ticket_overdue_staff'] == 1) {
                            $staffEmail = $this->getJSModel('staff')->getStaffEmailByStaffId($ticket->staffid);
                            $link = $this->setStaffUrl($id);
                            $msgSubject = $template->subject;
                            $msgBody = $template->body;
                            $matcharray['{TICKETURL}'] = $link;
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);

                            $this->sendEmail($staffEmail, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        }
                        // New ticket mail to User
                        if ($config['ticket_overdue_user'] == 1) {
                            $link = $this->setUserUrl($id);
                            $msgSubject = $template->subject;
                            $msgBody = $template->body;
                            $matcharray['{TICKETURL}'] = $link;
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);

                            $this->sendEmail($email, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        }
                        break;
                    case 9: // Mark in progress Ticket
                        $ticket = $this->getRecordByTablenameAndId('js_ticket_tickets', $id);
                        $trackingid = $ticket->ticketid;
                        $email = $ticket->email;
                        $subject = $ticket->subject;
                        $matcharray = array(
                            '{TRACKINGID}' => $trackingid,
                            '{SUBJECT}' => $subject,
                            '{DEPARTMENT}' => $ticket->departmentname,
                            '{PRIORITY}' => $ticket->priority
                        );
                        // code for handling custom fields start
                        if(!empty($ticket->params)){
                            $data = json_decode($ticket->params,true);
                        }
                        $fields = $this->getJSModel('userfields')->getUserfieldsfor(1);
                        if( isset($data) && is_array($data) ){
                            foreach ($fields as $field) {
                                if($field->userfieldtype != 'file'){
                                    $fvalue = '';
                                    if(array_key_exists($field->field, $data)){
                                        $fvalue = $data[$field->field];
                                    }
                                    $matcharray['{'.$field->field.'}'] = $fvalue;// match array new index for custom field
                                }
                            }
                        }
                        // code for handling custom fields end
                        $object = $this->getSenderEmailAndName($id);
                        $senderEmail = $object->email;
                        $senderName = $object->name;
                        $template = $this->getTemplateForEmail('markprgs-tk');
                        // New ticket mail to admin
                        if ($config['ticket_progress_admin'] == 1) {
                            $adminEmailid = $config['admin_email'];
                            $adminEmail = $this->getEmailById($adminEmailid);
                            $link = $this->setAdminUrl($id);
                            $msgSubject = $template->subject;
                            $msgBody = $template->body;
                            $matcharray['{TICKETURL}'] = $link;
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);

                            $this->sendEmail($adminEmail, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        }
                        // New ticket mail to staff
                        if ($config['ticket_progress_staff'] == 1) {
                            $staffEmail = $this->getJSModel('staff')->getStaffEmailByStaffId($ticket->staffid);
                            $link = $this->setStaffUrl($id);
                            $msgSubject = $template->subject;
                            $msgBody = $template->body;
                            $matcharray['{TICKETURL}'] = $link;
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);

                            $this->sendEmail($staffEmail, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        }
                        // New ticket mail to User
                        if ($config['ticket_progress_user'] == 1) {
                            $link = $this->setUserUrl($id);
                            $msgSubject = $template->subject;
                            $msgBody = $template->body;
                            $matcharray['{TICKETURL}'] = $link;
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);

                            $this->sendEmail($email, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        }

                        break;
                    case 10: // Ban email and close Ticket
                        $ticket = $this->getRecordByTablenameAndId('js_ticket_tickets', $id);
                        $trackingid = $ticket->ticketid;
                        $email = $ticket->email;
                        $subject = $ticket->subject;
                        $matcharray = array(
                            '{EMAIL_ADDRESS}' => $email,
                            '{SUBJECT}' => $subject,
                            '{TRACKINGID}' => $trackingid,
                            '{DEPARTMENT}' => $ticket->departmentname,
                            '{PRIORITY}' => $ticket->priority
                        );
                        // code for handling custom fields start
                        if(!empty($ticket->params)){
                            $data = json_decode($ticket->params,true);
                        }
                        $fields = $this->getJSModel('userfields')->getUserfieldsfor(1);
                        if( isset($data) && is_array($data) ){
                            foreach ($fields as $field) {
                                if($field->userfieldtype != 'file'){
                                    $fvalue = '';
                                    if(array_key_exists($field->field, $data)){
                                        $fvalue = $data[$field->field];
                                    }
                                    $matcharray['{'.$field->field.'}'] = $fvalue;// match array new index for custom field
                                }
                            }
                        }
                        // code for handling custom fields end
                        $object = $this->getSenderEmailAndName($id);
                        $senderEmail = $object->email;
                        $senderName = $object->name;
                        $template = $this->getTemplateForEmail('banemailcloseticket-tk');
                        $msgSubject = $template->subject;
                        $msgBody = $template->body;
                        // New ticket mail to admin
                        if ($config['ticker_ban_and_close_admin'] == 1) {
                            $adminEmailid = $config['admin_email'];
                            $adminEmail = $this->getEmailById($adminEmailid);
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);

                            $this->sendEmail($adminEmail, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        }
                        // New ticket mail to staff
                        if ($config['ticker_ban_and_close_staff'] == 1) {
                            $staffEmail = $this->getJSModel('staff')->getStaffEmailByStaffId($ticket->staffid);
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);

                            $this->sendEmail($staffEmail, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        }
                        // New ticket mail to User
                        if ($config['ticker_ban_and_close_user'] == 1) {
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);

                            $this->sendEmail($email, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        }
                        break;
                    case 11: // Priority change ticket
                        $ticket = $this->getRecordByTablenameAndId('js_ticket_tickets', $id);
                        $trackingid = $ticket->ticketid;
                        $subject = $ticket->subject;
                        $email = $ticket->email;
                        $Priority = $this->getJSModel('priority')->getPriorityById($ticket->priorityid);
                        $matcharray = array(
                            '{PRIORITY_TITLE}' => $Priority->priority,
                            '{SUBJECT}' => $subject,
                            '{TRACKINGID}' => $trackingid,
                            '{DEPARTMENT}' => $ticket->departmentname
                        );
                        // code for handling custom fields start
                        if(!empty($ticket->params)){
                            $data = json_decode($ticket->params,true);
                        }
                        $fields = $this->getJSModel('userfields')->getUserfieldsfor(1);
                        if( isset($data) && is_array($data) ){
                            foreach ($fields as $field) {
                                if($field->userfieldtype != 'file'){
                                    $fvalue = '';
                                    if(array_key_exists($field->field, $data)){
                                        $fvalue = $data[$field->field];
                                    }
                                    $matcharray['{'.$field->field.'}'] = $fvalue;// match array new index for custom field
                                }
                            }
                        }
                        // code for handling custom fields end
                        $object = $this->getSenderEmailAndName($id);
                        $senderEmail = $object->email;
                        $senderName = $object->name;
                        $template = $this->getTemplateForEmail('pchnge-tk');
                        $msgSubject = $template->subject;
                        $msgBody = $template->body;
                        // New ticket mail to admin
                        if ($config['ticket_priority_admin'] == 1) {
                            $link = $this->setAdminUrl($id);
                            $matcharray['{TICKETURL}'] = $link;
                            $adminEmailid = $config['admin_email'];
                            $adminEmail = $this->getEmailById($adminEmailid);
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);

                            $this->sendEmail($adminEmail, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        }
                        // New ticket mail to staff
                        if ($config['ticket_priority_staff'] == 1) {
                            $staffEmail = $this->getJSModel('staff')->getStaffEmailByStaffId($ticket->staffid);
                            $link = $this->setStaffUrl($id);
                            $matcharray['{TICKETURL}'] = $link;
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);

                            $this->sendEmail($staffEmail, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        }
                        // New ticket mail to User
                        if ($config['ticket_priority_user'] == 1) {
                            $link = $this->setUserUrl($id);
                            $matcharray['{TICKETURL}'] = $link;
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);

                            $this->sendEmail($email, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        }
                        break;
                    case 12: // DEPARTMENT TRANSFER
                        $ticket = $this->getRecordByTablenameAndId('js_ticket_tickets', $id);
                        $trackingid = $ticket->ticketid;
                        $subject = $ticket->subject;
                        $email = $ticket->email;
                        $Department = $this->getJSModel('department')->getDepartmentById($ticket->departmentid);
                        $matcharray = array(
                            '{DEPARTMENT_TITLE}' => $Department,
                            '{SUBJECT}' => $subject,
                            '{TRACKINGID}' => $trackingid,
                            '{PRIORITY}' => $ticket->priority
                        );
                        // code for handling custom fields start
                        if(!empty($ticket->params)){
                            $data = json_decode($ticket->params,true);
                        }
                        $fields = $this->getJSModel('userfields')->getUserfieldsfor(1);
                        if( isset($data) && is_array($data) ){
                            foreach ($fields as $field) {
                                if($field->userfieldtype != 'file'){
                                    $fvalue = '';
                                    if(array_key_exists($field->field, $data)){
                                        $fvalue = $data[$field->field];
                                    }
                                    $matcharray['{'.$field->field.'}'] = $fvalue;// match array new index for custom field
                                }
                            }
                        }
                        // code for handling custom fields end
                        $object = $this->getSenderEmailAndName($id);
                        $senderEmail = $object->email;
                        $senderName = $object->name;
                        $template = $this->getTemplateForEmail('deptrans-tk');
                        $msgSubject = $template->subject;
                        $msgBody = $template->body;
                        // New ticket mail to admin
                        if ($config['ticket_department_transfer_admin'] == 1) {
                            $adminEmailid = $config['admin_email'];
                            $adminEmail = $this->getEmailById($adminEmailid);
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);
                            $this->sendEmail($adminEmail, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        }
                        // New ticket mail to staff
                        if ($config['ticket_department_transfer_staff'] == 1) {
                          // Get All Staff member of the department of Current Ticket
                          $staffmembers = $this->getJSModel('staff')->getAllStaffMemberByDepId($ticket->departmentid);
                          foreach ($staffmembers AS $staff) {
                            if($staff->canemail == 1){
                              $this->replaceMatches($msgSubject, $matcharray);
                              $this->replaceMatches($msgBody, $matcharray);
                              $msgBody .= '<input type="hidden" name="ticketid:' . $trackingid . '###staff####" />';
                              echo '<br/>'.$msgBody;
                              $this->sendEmail($staff->email, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                            }
                          }
                        }
                        // New ticket mail to User
                        if ($config['ticket_department_transfer_user'] == 1) {
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);

                            $this->sendEmail($email, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        }
                        break;
                    case 13: // REASSIGN TICKET TO STAFF
                        $ticket = $this->getRecordByTablenameAndId('js_ticket_tickets', $id);
                        $trackingid = $ticket->ticketid;
                        $email = $ticket->email;
                        $subject = $ticket->subject;
                        $Staff = $this->getJSModel('staff')->getMyName($ticket->staffid);
                        $matcharray = array(
                            '{STAFF_MEMBER_NAME}' => $Staff,
                            '{SUBJECT}' => $subject,
                            '{TRACKINGID}' => $trackingid,
                            '{DEPARTMENT}' => $ticket->departmentname,
                            '{PRIORITY}' => $ticket->priority
                        );
                        // code for handling custom fields start
                        if(!empty($ticket->params)){
                            $data = json_decode($ticket->params,true);
                        }
                        $fields = $this->getJSModel('userfields')->getUserfieldsfor(1);
                        if( isset($data) && is_array($data) ){
                            foreach ($fields as $field) {
                                if($field->userfieldtype != 'file'){
                                    $fvalue = '';
                                    if(array_key_exists($field->field, $data)){
                                        $fvalue = $data[$field->field];
                                    }
                                    $matcharray['{'.$field->field.'}'] = $fvalue;// match array new index for custom field
                                }
                            }
                        }
                        // code for handling custom fields end
                        $object = $this->getSenderEmailAndName($id);
                        $senderEmail = $object->email;
                        $senderName = $object->name;
                        $template = $this->getTemplateForEmail('reassign-tk');
                        $msgSubject = $template->subject;
                        $msgBody = $template->body;
                        // New ticket mail to admin
                        $link = $this->setAdminUrl($id);
                        $matcharray['{TICKETURL}'] = $link;
                        if ($config['ticket_reassign_admin'] == 1) {
                            $adminEmailid = $config['admin_email'];
                            $adminEmail = $this->getEmailById($adminEmailid);
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);

                            $this->sendEmail($adminEmail, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        }
                        $msgSubject = $template->subject;
                        $msgBody = $template->body;
                        $matcharray = array(
                            '{STAFF_MEMBER_NAME}' => $Staff,
                            '{SUBJECT}' => $subject,
                            '{TRACKINGID}' => $trackingid
                        );
                        $link = $this->setStaffUrl($id);
                        $matcharray['{TICKETURL}'] = $link;
                        // New ticket mail to staff
                        if ($config['ticket_reassign_staff'] == 1) {
                            $staffEmail = $this->getJSModel('staff')->getStaffEmailByStaffId($ticket->staffid);
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);

                            $this->sendEmail($staffEmail, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        }
                        // New ticket mail to User
                        if ($config['ticket_reassign_user'] == 1) {
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);

                            $this->sendEmail($email, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        }
                    break;
                    case 14: // email to user for repling closed ticket
                        $ticketRecord = $this->getRecordByTablenameAndId('js_ticket_tickets', $id);
                        $Subject = $ticketRecord->subject;
                        $Email = $ticketRecord->email;
                        $matcharray = array(
                            '{SUBJECT}' => $Subject,
                            '{DEPARTMENT}' => $ticketRecord->departmentname,
                            '{PRIORITY}' => $ticketRecord->priority
                        );
                        // code for handling custom fields start
                        if(!empty($ticketRecord->params)){
                            $data = json_decode($ticketRecord->params,true);
                        }
                        $fields = $this->getJSModel('userfields')->getUserfieldsfor(1);
                        if( isset($data) && is_array($data) ){
                            foreach ($fields as $field) {
                                if($field->userfieldtype != 'file'){
                                    $fvalue = '';
                                    if(array_key_exists($field->field, $data)){
                                        $fvalue = $data[$field->field];
                                    }
                                    $matcharray['{'.$field->field.'}'] = $fvalue;// match array new index for custom field
                                }
                            }
                        }
                        // code for handling custom fields end
                        $object = $this->getSenderEmailAndName($id);
                        $senderEmail = $object->email;
                        $senderName = $object->name;
                        $template = $this->getTemplateForEmail('mail-rpy-closed');
                        // New ticket mail to User
                        if ($config['ticket_reply_closed_ticket_user'] == 1) {
                            $msgBody = $template->body;
                            $this->replaceMatches($msgBody, $matcharray);
                            $this->sendEmail($Email, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        }
                    break;
                    case 15: // Feedback email to user for closed ticket
                        $ticketRecord = $this->getRecordByTablenameAndId('js_ticket_tickets', $id);
                        $Subject = $ticketRecord->subject;
                        $Email = $ticketRecord->email;
                        $TrackingId = $ticketRecord->ticketid;
                        $close_date = date($config['date_format'], strtotime($ticketRecord->closed));
                        $username = $ticketRecord->name;
                        $url = $this->getURL();
                        $private = $TrackingId.','.$Email;
                        $data = base64_encode($private);
                        $url .= '&c=feedback&task=showfeedbackform&jsticket='.$data;
                        $link = "<a href=".$url.">";
                        $linkclosing = "</a>";
                        $url = $this->getURL();
                        $url .= '&c=ticket&layout=ticketdetail&jsticket='.$data;
                        $tracking_url = '<a href="'.$url.'" target="_blank">'. $TrackingId .'</a>';
                        $matcharray = array(
                            '{USER_NAME}' => $username,
                            '{TICKET_SUBJECT}' => $Subject,
                            '{TRACKING_ID}' => $tracking_url,
                            '{CLOSE_DATE}' => $close_date,
                            '{LINK}' => $link,
                            '{/LINK}' => $linkclosing,
                            '{DEPARTMENT}' => $ticketRecord->departmentname,
                            '{PRIORITY}' => $ticketRecord->priority
                        );
                        // code for handling custom fields start
                        if(!empty($ticketRecord->params)){
                            $data = json_decode($ticketRecord->params,true);
                        }
                        $fields = $this->getJSModel('userfields')->getUserfieldsfor(1);
                        if( isset($data) && is_array($data) ){
                            foreach ($fields as $field) {
                                if($field->userfieldtype != 'file'){
                                    $fvalue = '';
                                    if(array_key_exists($field->field, $data)){
                                        $fvalue = $data[$field->field];
                                    }
                                    $matcharray['{'.$field->field.'}'] = $fvalue;// match array new index for custom field
                                }
                            }
                        }
                        // code for handling custom fields end
                        $object = $this->getSenderEmailAndName($id);
                        $senderEmail = $object->email;
                        $senderName = $object->name;
                        $template = $this->getTemplateForEmail('mail-feedback');
                        $msgSubject = $template->subject;
                        // New ticket mail to User
                        if ($config['ticket_feedback_user'] == 1) {
                            $msgBody = $template->body;
                            $this->replaceMatches($msgBody, $matcharray);
                            $this->sendEmail($Email, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        }
                    break;
                }
                break;
            case 2: // Ban Email
                switch ($action) {
                    case 1: // Ban Email
                        if ($tablename != null)
                            $banemailRecord = $this->getRecordByTablenameAndId($tablename, $id);
                        else
                            $banemailRecord = $this->getRecordByTablenameAndId('js_ticket_email_banlist', $id);
                        $email = $banemailRecord->email;
                        $matcharray = array(
                            '{EMAIL_ADDRESS}' => $email
                        );
                        $object = $this->getDefaultSenderEmailAndName();
                        $senderEmail = $object->email;
                        $senderName = $object->name;
                        $template = $this->getTemplateForEmail('banemail-tk');
                        $msgSubject = $template->subject;
                        $msgBody = $template->body;
                        // New ticket mail to admin
                        if ($config['ticket_ban_email_admin'] == 1) {
                            $adminEmailid = $config['admin_email'];
                            $adminEmail = $this->getEmailById($adminEmailid);
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);

                            $this->sendEmail($adminEmail, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        }
                        // New ticket mail to staff
                        if ($config['ticket_ban_email_staff'] == 1) {
                            if ($tablename != null){
                                $staffEmail = $this->getJSModel('staff')->getStaffEmailByStaffId($banemailRecord->staffid);
                            }else{
                                $staffEmail = $this->getJSModel('staff')->getStaffEmailByStaffId($banemailRecord->submitter);
                            }
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);

                            $this->sendEmail($staffEmail, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        }
                        // New ticket mail to User
                        if ($config['ticket_ban_email_user'] == 1) {
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);

                            $this->sendEmail($email, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        }
                        break;
                    case 2: // Unban Email
                        if ($tablename != null)
                            $ticket = $this->getRecordByTablenameAndId($tablename, $id);
                        else
                            $ticket = $this->getRecordByTablenameAndId('js_ticket_tickets', $id);
                        $email = $ticket->email;
                        $matcharray = array(
                            '{EMAIL_ADDRESS}' => $email
                        );
                        $object = $this->getSenderEmailAndName($id);
                        $senderEmail = $object->email;
                        $senderName = $object->name;
                        $template = $this->getTemplateForEmail('unbanemail-tk');
                        $msgSubject = $template->subject;
                        $msgBody = $template->body;
                        // New ticket mail to admin
                        if ($config['unban_email_admin'] == 1) {
                            $adminEmailid = $config['admin_email'];
                            $adminEmail = $this->getEmailById($adminEmailid);
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);

                            $this->sendEmail($adminEmail, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        }
                        // New ticket mail to staff
                        if ($config['unban_email_staff'] == 1) {
                            if ($tablename != null)
                                $staffEmail = $this->getJSModel('staff')->getStaffEmailByStaffId($ticket->staffid);
                            else
                                $staffEmail = $this->getJSModel('staff')->getStaffEmailByStaffId($ticket->submitter);
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);

                            $this->sendEmail($staffEmail, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        }
                        // New ticket mail to User
                        if ($config['unban_email_user'] == 1) {
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);

                            $this->sendEmail($email, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        }
                        break;
                }
                break;
            case 3: // Sending email alerts on mail system
                switch ($action) {
                    case 1: // Store message
                        $mailRecord = $this->getMailRecordById($id);
                        $matcharray = array(
                            '{STAFF_MEMBER_NAME}' => $mailRecord->sendername,
                            '{SUBJECT}' => $mailRecord->subject,
                            '{MESSAGE}' => $mailRecord->message
                        );
                        $object = $this->getSenderEmailAndName(null);
                        $senderEmail = $object->email;
                        $senderName = $object->name;
                        $template = $this->getTemplateForEmail('mail-new');
                        $msgSubject = $template->subject;
                        $msgBody = $template->body;
                        $email = $mailRecord->receveremail;
                        $this->replaceMatches($msgSubject, $matcharray);
                        $this->replaceMatches($msgBody, $matcharray);

                        $this->sendEmail($email, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        break;
                    case 2: // Store reply
                        $mailRecord = $this->getMailRecordById($id, 1);
                        $matcharray = array(
                            '{STAFF_MEMBER_NAME}' => $mailRecord->sendername,
                            '{SUBJECT}' => $mailRecord->subject,
                            '{MESSAGE}' => $mailRecord->message
                        );
                        $object = $this->getSenderEmailAndName(null);
                        $senderEmail = $object->email;
                        $senderName = $object->name;
                        $template = $this->getTemplateForEmail('mail-rpy');
                        $msgSubject = $template->subject;
                        $msgBody = $template->body;
                        $email = $mailRecord->receveremail;
                        $this->replaceMatches($msgSubject, $matcharray);
                        $this->replaceMatches($msgBody, $matcharray);

                        $this->sendEmail($email, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        break;
                }
                break;
            case 4: // GDPR
                switch($action){
                    case 1: // erase data request email
                        $mailRecord = $this->getRecordByTablenameAndId($tablename, $id);
                        $matcharray = array(
                            '{SITETITLE}' => $config['title'],
                            '{USERNAME}' => $mailRecord->name,
                            '{CURRENT_YEAR}' => date('Y')
                        );
                        if($config['erase_data_request_user'] == 1){
                            $object = $this->getSenderEmailAndName(null);
                            $senderEmail = $object->email;
                            $senderName = $object->name;
                            $template = $this->getTemplateForEmail('delete-user-data');
                            $msgSubject = $template->subject;
                            $msgBody = $template->body;
                            $Email = $mailRecord->email;
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);
                            $this->sendEmail($Email, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        }
                        // Erase data request receive
                        if ($config['erase_data_request_admin'] == 1) {
                            $adminEmailid = $config['admin_email'];
                            $adminEmail = $this->getEmailById($adminEmailid);
                            $adminName = $this->getJoomlaNameByEmail($adminEmail);
                            $template = $this->getTemplateForEmail('delete-user-data-admin');
                            $msgSubject = $template->subject;
                            $msgBody = $template->body;
                            $link = $this->setAdminUrl($id);
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);
                            $this->sendEmail($adminEmail, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        }
                    break;
                    case 2: // user data delete
                        $mailRecord = $this->getRecordByTablenameAndId($tablename, $id);
                        $matcharray = array(
                            '{SITETITLE}' => $config['title'],
                            '{USERNAME}' => $mailRecord->name,
                            '{CURRENT_YEAR}' => date('Y')
                        );
                        if($config['delete_user_data'] == 1){
                            $object = $this->getSenderEmailAndName(null);
                            $senderEmail = $object->email;
                            $senderName = $object->name;
                            $template = $this->getTemplateForEmail('user-data-deleted');
                            $msgSubject = $template->subject;
                            $msgBody = $template->body;
                            $Email = $mailRecord->email;
                            $this->replaceMatches($msgSubject, $matcharray);
                            $this->replaceMatches($msgBody, $matcharray);
                            $this->sendEmail($Email, $msgSubject, $msgBody, $senderEmail, $senderName, '', $action);
                        }
                    break;
                }
            break;
        }
    }

    private function sendSMTPmail($recevierEmail, $subject, $body, $senderEmail, $senderName, $attachments, $action){
        if($recevierEmail == ''){
            return;
        }
        require_once JPATH_LIBRARIES . '/vendor/phpmailer/phpmailer/class.phpmailer.php';
        require_once JPATH_LIBRARIES . '/vendor/phpmailer/phpmailer/class.smtp.php';
        $mail = new PHPMailer(true);                              // Passing `true` enables exceptions
        try {

            $emailconfig = $this->getSMTPEmailConfig($senderEmail);
			$site_title = $this->getJSModel('config')->getConfigurationByName('title');
			$senderName = $site_title;
            //Server settings
            //$mail->SMTPDebug = 2;                                // Enable verbose debug output
            $mail->isSMTP();                                      // Set mailer to use SMTP

            $mail->Host = $emailconfig->smtphost;  // Specify main and backup SMTP servers
            //$mail->Host = 'smtp1.example.com;smtp2.example.com';  // Specify main and backup SMTP servers
            $mail->SMTPAuth = $emailconfig->smtpauthencation;                               // Enable SMTP authentication
            //$mail->Username = $senderEmail;                 // SMTP username
            $mail->Username = $emailconfig->name;                 // SMTP username
            $mail->Password = base64_decode($emailconfig->password);                           // SMTP password
            if($emailconfig->smtpsecure == 0){
                $mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
            }else{
                $mail->SMTPSecure = 'tls';
            }
            $mail->Port = $emailconfig->mailport;
                                      // TCP port to connect to
            //Recipients
            $mail->setFrom($senderEmail, $senderName);
            // foreach ($recevierEmail as $key => $value) {
            //     $mail->addAddress($value, 'Name Of User');     // Add a recipient
            // }

            $mail->addAddress($recevierEmail);     // Add a recipient

            //$mail->addAddress('ellen@example.com');               // Name is optional
            // $mail->addReplyTo('info@example.com', 'Information');
            // $mail->addCC('xaid@burujsolutions.com');


            //Attachments
            if(!empty($attachments) && $attachments != ''){
                $mail->addAttachment($attachments);         // Add attachments
            }

            //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

            //Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body    = $body;
            //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

            $mail->send();

            //echo 'Message has been sent';
            return 'Message has been sent';
        } catch (Exception $e) {
            $this->getJSModel('systemerrors')->updateSystemErrors('Message could not be sent. Mailer Error: '. $mail->ErrorInfo);
        }
        return;
    }

    private function getRecordByTablenameAndId($tablename, $id) {
        if (!is_numeric($id))
            return false;
        $db = JFactory::getDBO();


        switch($tablename){
            case 'js_ticket_tickets':
                $query = "SELECT ticket.*,department.departmentname,helptopic.topic,priority.priority "
                    . " FROM `#__" . $tablename . "` AS ticket "
                    . " LEFT JOIN `#__js_ticket_departments` AS department ON department.id = ticket.departmentid "
                    . " LEFT JOIN `#__js_ticket_help_topics` AS helptopic ON helptopic.id = ticket.helptopicid "
                    . " LEFT JOIN `#__js_ticket_priorities` AS priority ON priority.id = ticket.priorityid "
                    . " WHERE ticket.id = " . $id;
            break;
            default:
                $query = "SELECT * FROM `#__" . $tablename . "` WHERE id = " . $id;
            break;
        }

        $db->setQuery($query);
        if (!$db->execute()) {
            $this->getJSModel('systemerrors')->updateSystemErrors($db->getErrorMsg());
            $db->setError($db->getErrorMsg());
            return false;
        }
        $record = $db->loadObject();
        return $record;
    }

    private function replaceMatches(&$string, $matcharray) {
        foreach ($matcharray AS $find => $replace) {
            $string = str_replace($find, $replace, $string);
        }
    }

    private function getSenderEmailAndName($id) {
        if ($id) {
            if (!is_numeric($id))
                return false;
            $db = JFactory::getDBO();
            $query = "SELECT email.email,email.name
                        FROM `#__js_ticket_tickets` AS ticket
                        JOIN `#__js_ticket_departments` AS department ON department.id = ticket.departmentid
                        JOIN `#__js_ticket_email` AS email ON email.id = department.emailid
                        WHERE ticket.id = " . $id;
            $db->setQuery($query);
            if (!$db->execute()) {
                $this->getJSModel('systemerrors')->updateSystemErrors($db->getErrorMsg());
                $db->setError($db->getErrorMsg());
                return false;
            }
            $email = $db->loadObject();
        } else {
            $email = '';
        }
        if (empty($email)) {
            $email = $this->getDefaultSenderEmailAndName();
        }
        return $email;
    }

    private function getDefaultSenderEmailAndName() {
        $config = $this->getJSModel('config')->getConfigByFor('email');
        $emailid = $config['alert_email'];
        $db = JFactory::getDbo();
        $query = "SELECT email,name FROM `#__js_ticket_email` WHERE id = " . $emailid;
        $db->setQuery($query);
        $email = $db->loadObject();
        return $email;
    }

    function getEmailById($id) {
        if (!is_numeric($id))
            return false;
        $db = $this->getDBO();
        $query = "SELECT email  FROM `#__js_ticket_email` WHERE id = " . $id;
        $db->setQuery($query);
        $email = $db->loadResult();
        return $email;
    }

    private function getTemplateForEmail($templatefor) {
        $db = $this->getDBO();
        $query = "SELECT * FROM `#__js_ticket_emailtemplates` WHERE templatefor = " . $db->quote($templatefor);
        $db->setQuery($query);
        $template = $db->loadObject();
        return $template;
    }

    function sendEmail($recevierEmail, $subject, $body, $senderEmail, $senderName, $attachments, $action) {
        $enablesmtp = $this->checkSMTPEnableOrDisable($senderEmail);
        if ($enablesmtp) {
            $this->sendSMTPmail($recevierEmail, $subject, $body, $senderEmail, $senderName, $attachments, $action);
        }else{
            $this->sendEmailDefault($recevierEmail, $subject, $body, $senderEmail, $senderName, $attachments, $action);
        }

    }

    private function sendEmailDefault($recevierEmail, $subject, $body, $senderEmail, $senderName, $attachments, $action) {

        /*
          $attachments = array( WP_CONTENT_DIR . '/uploads/file_to_attach.zip' );
          $headers = 'From: My Name <myname@example.com>' . "\r\n";
          wp_mail('test@example.org', 'subject', 'message', $headers, $attachments );

          $action
          For which action of $mailfor you want to send the mail
          1 => New Ticket Create
          2 => Close Ticket
          3 => Delete Ticket
          4 => Reply Ticket (Admin/Staff Member)
          5 => Reply Ticket (Ticket member)

            switch ($action) {
                case 1:
                    do_action('jsst-beforeemailticketcreate', $recevierEmail, $subject, $body, $senderEmail);
                    break;
                case 2:
                    do_action('jsst-beforeemailticketreply', $recevierEmail, $subject, $body, $senderEmail);
                    break;
                case 3:
                    do_action('jsst-beforeemailticketclose', $recevierEmail, $subject, $body, $senderEmail);
                    break;
                case 4:
                    do_action('jsst-beforeemailticketdelete', $recevierEmail, $subject, $body, $senderEmail);
                    break;
            }
        */
        if(empty($senderName)){
            $senderName = $this->getJSModel('config')->getConfigurationByName('title');
        }
        $headers = 'From: ' . $senderName . ' <' . $senderEmail . '>' . "\r\n";

        if(!empty($recevierEmail) && !empty($senderEmail)){
            $message = JFactory::getMailer();
            $message->addRecipient($recevierEmail);
            $message->setSubject($subject);
            $siteAddress = JURI::base();
            //echo 'admin'.$adminEmail.$body;
            $message->setBody($body);
            $sender = array( $senderEmail, $senderName );
            $message->setSender($sender);
            $message->IsHTML(true);
            $sent = $message->send();
            return $sent;
        }else return true;
    }

    function getMailRecordById($id, $replyto = null) {
        if (!is_numeric($id))
            return false;
        $db = JFactory::getDBO();
        if ($replyto == null) {
            $query = "SELECT mail.subject,mail.message,CONCAT(staff.name,' ',staff.lastname) AS sendername
                        FROM `#__js_ticket_mail` AS mail
                        JOIN `#__js_ticket_staff` AS staff ON staff.id = mail.from
                        WHERE mail.id = " . $id;
        } else {
            $query = "SELECT mail.subject,reply.message,CONCAT(staff.name,' ',staff.lastname) AS sendername
                        FROM `#__js_ticket_mail` AS reply
                        JOIN `#__js_ticket_mail` AS mail ON mail.id = reply.replytoid
                        JOIN `#__js_ticket_staff` AS staff ON staff.id = reply.from
                        WHERE reply.id = " . $id;
        }
        $db->setQuery($query);
        if (!$db->execute()) {
            $this->getJSModel('systemerrors')->updateSystemErrors($db->getErrorMsg());
            $db->setError($db->getErrorMsg());
            return false;
        }
        $result = $db->loadObject();
        return $result;
    }

    function getJoomlaNameByEmail($emailaddress){
        $db = JFactory::getDbo();
        $query = "SELECT name FROM `#__users` WHERE email = ".$db->quote($emailaddress);
        $db->setQuery($query);
        $name = $db->loadResult();
        return $name;
    }

    function getURL(){
        $url = JURI::root();
        $url .= 'index.php?option=com_jssupportticket';
        return $url;
    }
    function getAdminURL(){
        $url = JURI::root();
        $url .= 'administrator/index.php?option=com_jssupportticket';
        return $url;
    }
    function setGuestUrl($trackingid,$email){
        $url = $this->getURL();
        $private = $trackingid.','.$email;
        $data = base64_encode($private);
        $url .= '&c=ticket&layout=ticketdetail&jsticket='.$data;
        $link = '<a href="'.$url.'" target="_blank">'.JText::_('Ticket Detail').'</a>';
        return $link;
    }
    function setUserUrl($id){
        $url = $this->getURL();
        $url .= '&c=ticket&layout=ticketdetail&id='.$id;
        $link = '<a href="'.$url.'" target="_blank">'.JText::_('Ticket Detail').'</a>';
        return $link;
    }
    function setStaffUrl($id){
        $url = $this->getURL();
        $url .= '&c=ticket&layout=ticketdetail&id='.$id;
        $link = '<a href="'.$url.'" target="_blank">'.JText::_('Ticket Detail').'</a>';
        return $link;
    }

    function setAdminUrl($id){
        $url = $this->getAdminURL();
        $url .= '&c=ticket&layout=ticketdetails&cid='.$id;
        $link = '<a href="'.$url.'" target="_blank">'.JText::_('Ticket Detail').'</a>';
        return $link;
    }

    function checkSMTPEnableOrDisable($senderemail){
        if(!is_string($senderemail))
            return false;
        $db = JFactory::getDbo();
        $query = "SELECT COUNT(id) FROM `#__js_ticket_email` WHERE email = ".$db->quote($senderemail). " AND smtpemailauth = 1"; // 1 For smtp 0 for default
        $db->setQuery($query);
        $total = $db->loadResult();
        if($total > 0){
            return true;
        }else{
            return false;
        }
    }

    function getSMTPEmailConfig($senderemail){
        $db = JFactory::getDbo();
        $query = "SELECT * FROM `#__js_ticket_email` WHERE email = ".$db->quote($senderemail);
        $db->setQuery($query);
        $emailconfig = $db->loadObject();
        return $emailconfig;
    }

    function sendTestEmail(){
        $hosttype = JFactory::getApplication()->input->get('hosttype');
        $hostname = JFactory::getApplication()->input->getString('hostname');
        $ssl = JFactory::getApplication()->input->get('ssl');
        $hostportnumber = JFactory::getApplication()->input->get('hostportnumber');
        $emailaddress = JFactory::getApplication()->input->getString('emailaddress');
        $username = JFactory::getApplication()->input->getString('username');
        $password = JFactory::getApplication()->input->getString('password');
        $smtpauthencation = JFactory::getApplication()->input->get('smtpauthencation');

        require_once JPATH_LIBRARIES . '/vendor/phpmailer/phpmailer/class.phpmailer.php';
        require_once JPATH_LIBRARIES . '/vendor/phpmailer/phpmailer/class.smtp.php';

        $mail = new PHPMailer(true);
        try {

            $mail->isSMTP();
            $mail->Host = $hostname;
            //$mail->Host = 'smtp1.example.com;
            $mail->SMTPAuth = $smtpauthencation;
            $mail->Username = $username;
            $mail->Password = $password;
            if($ssl == 0){
                $mail->SMTPSecure = 'ssl';
            }else{
                $mail->SMTPSecure = 'tls';
            }
            $mail->Port = $hostportnumber;
            //Recipients
            $senderName = $this->getJSModel('config')->getConfigurationByName('title');
            $mail->setFrom($emailaddress, $senderName);


            $config = $this->getJSModel('config')->getConfigs();
            $adminEmailid = $config['admin_email'];
            $adminEmail = $this->getEmailById($adminEmailid);
            $adminName = $this->getJoomlaNameByEmail($adminEmail);
            $mail->addAddress($adminEmail,$adminName);


            $mail->isHTML(true);
            $mail->Subject = 'SMPT Test email';
            $mail->Body    = 'This is body test for SMTP check email';
            $responce = $mail->send();

            $error['msg'] = 'Test email has been sent on : '. $adminEmail;
            $error['type'] = 0;
        } catch (Exception $e) {
            $error['msg'] = 'Message could not be sent. Mailer Error: '. $mail->ErrorInfo;
            $error['type'] = 1;
        }
        return json_encode($error);;

    }
}
?>
