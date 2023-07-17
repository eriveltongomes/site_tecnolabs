<?php

/**
 * @Copyright Copyright (C) 2015 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Company:     Buruj Solutions
  + Contact:    www.burujsolutions.com , info@burujsolutions.com
 * Created on:	May 22, 2015
  ^
  + Project:    JS Tickets
  ^
 */
defined('_JEXEC') or die('Not Allowed');

jimport('joomla.application.component.model');
jimport('joomla.html.html');

class JSSupportticketModelTicket extends JSSupportTicketModel {

    var $activity_log;
    var $_jinput = null;
    function __construct() {
        parent::__construct();

        $this->activity_log = $this->getJSModel('activitylog');
        $this->_jinput = JFactory::getApplication()->input;
    }

    function getTicketNameById($id){
        if(!is_numeric($id))
            return $id;
        $db = $this->getDbo();
        $query = "SELECT subject FROM `#__js_ticket_tickets` WHERE id='".$id."'";
        $db->setQuery($query);
        $subject = $db->loadResult();
        return $subject;
    }

    function getRandomFolderName() {
        $foldername = "";
        $length = 7;
        $possible = "qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM";
        // we refer to the length of $possible a few times, so let's grab it now
        $maxlength = strlen($possible);
        if ($length > $maxlength) { // check for length overflow and truncate if necessary
            $length = $maxlength;
        }
        // set up a counter for how many characters are in the ticketid so far
        $i = 0;
        // add random characters to $password until $length is reached
        while ($i < $length) {
            // pick a random character from the possible ones
            $char = substr($possible, mt_rand(0, $maxlength - 1), 1);
            if ($i == 0) {
                if (ctype_alpha($char)) {
                    $foldername .= $char;
                    $i++;
                }
            } else {
                $foldername .= $char;
                $i++;
            }
        }
        return $foldername;
    }

    function storeTicket($data){

        // JPluginHelper::importPlugin('jssupportticket');
        // $dispatcher = JDispatcher::getInstance();
        // $dispatcher->trigger( 'onSaveForm', array(&$data));

        //to check hash
        if($data['id'] != ''){
            $db = $this->getDbo();
            $query = "SELECT hash,uid FROM `#__js_ticket_tickets` WHERE ticketid='".$data['ticketid']."'";
            $db->setQuery($query);
            $row = $db->loadObject();
            $data['uid'] = $row->uid;
            if( $row->hash != $this->generateHash($data['id']) ){
                return false;
            }//end
        }

        $config = $this->getJSModel('config')->getConfigByFor('default');
        $user = JSSupportticketCurrentUser::getInstance();
        if(isset($data['ticketviaemail']) && $data['ticketviaemail'] == 1){
            $eventtype = JText::_('New Ticket via email');
        }else{
            $eventtype = JText::_('New Ticket');
        }


        if($user->getIsAdmin()){
            $msg1 = JText::_('Admin');
        }else{
            if($data['id'] == ''){
                $canadd = $this->checkCanAddTicket($data['email']);
                if(!$canadd){
                    return LIMIT_EXCEED;
                }
                $canadd = $this->checkMaxOpenTickets($data['email']);
                if(!$canadd){
                    return LIMIT_EXCEED_OPEN;
                }

                $checkduplicatetk = $this->checkIsTicketDuplicate($data['subject'],$data['email']);
                if(!$checkduplicatetk){
                    return TICKET_DUPLICATE;
                }
            }

            $isBaned = $this->getJSModel('emailbanlist')->checkIsBaned($data['email']);
            if ($isBaned) {
                $this->getJSModel('emailbanlistlog')->updateBanlistLog($data['name'], $data['email']);
                return BANNED_EMAIL;
            }

            if($user->getIsStaff()){
                $per = $user->checkUserPermission('Add Ticket');
                if ($per == false)
                    return PERMISSION_ERROR;
                $msg1 = JText::_('Staff');
            }else{
				if(!isset($data['ticketviaemail'])){
					$data = JFactory::getApplication()->input->post->getArray();
					$msg1 = JText::_('User');
					if ($user->getIsGuest()) {
						if($config['visitor_can_create_ticket'] == 1){
							if ($config['show_captcha_visitor_form_ticket'] == 1) {
								if($config['captcha_selection'] == 1){
									JPluginHelper::importPlugin('captcha');
									if (JVERSION < 3)
										$dispatcher = JDispatcher::getInstance();
									else{
                                        $dispatcher = JEventDispatcher::getInstance();
									}
									$joomla_captcha = JFactory::getConfig()->get('captcha');
									if ( $joomla_captcha == 'recaptcha') { // 2.0
                                        $res = $dispatcher->trigger('onCheckAnswer', $data['recaptcha_response_field']);
										$isValidated = $res[0];
									}elseif ( $joomla_captcha == 'recaptcha_invisible') {
										$captcha = JCaptcha::getInstance(JFactory::getConfig()->get('captcha'));
										$isValidated = $captcha->checkAnswer('recaptchainvb');
										
									}
	
									if (!$isValidated == 1) {
										return INVALID_CAPTCHA;
									}
								}else{
									if (!$this->performChecks()) {
										return INVALID_CAPTCHA;
									}
								}
							}
							$msg1 = JText::_('Guest');
						}else{
							return PERMISSION_ERROR;
						}
					}
				}
            }
        }

        if(isset($data['duedate']) && !empty($data['duedate'])){
            $dateformat = $config['date_format'];
            if ($dateformat == 'm-d-Y') {
              $arr = explode('-', $data['duedate']);
              $data['duedate'] = $arr[2] . '-' . $arr[0] . '-' . $arr[1];
            } elseif ($dateformat == 'd-m-Y' OR $dateformat == 'Y-m-d') {
              $arr = explode('-', $data['duedate']);
              $data['duedate'] = $arr[2] . '-' . $arr[1] . '-' . $arr[0];
            }
            $data['duedate'] = JHTML::_('date',strtotime($data['duedate']),"Y-m-d H:i:s" );
        }

        //for new ticket case
        if (($data['id']) == ''){
            $data['ticketid'] = $this->getTicketId();
            $data['attachmentdir'] = $this->getRandomFolderName();
            $data['created'] = date('Y-m-d H:i:s');
        }
        if ($data['id'] <> '' AND $data['isoverdue'] == 1) {// for edit case to change the overdue if criteria is passed
            $curdate = date('Y-m-d H:i:s');
            if (date('Y-m-d',strtotime($data['duedate'])) > date('Y-m-d',strtotime($curdate))){
                $data['isoverdue'] = 0;
            }else{
                $db = JFactory::getDbo();
                $query = "SELECT ticket.duedate FROM `#__js_ticket_tickets` AS ticket WHERE ticket.id = ".$data['id'];
                $db->setQuery($query);
                $duedate = $db->loadResult();
                if(date('Y-m-d',strtotime($data['duedate'])) != date('Y-m-d',strtotime($duedate))){
                    return DUE_DATE_ERROR; //Due Date must be greater then current date
                }
            }
        }

        //custom field code start
        $customflagforadd = false;
        $customflagfordelete = false;
        $custom_field_namesforadd = array();
        $custom_field_namesfordelete = array();
        $userfield = $this->getJSModel('userfields')->getUserfieldsfor(1);
        $params = array();
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
            }elseif($ufobj->userfieldtype == 'date'){
                if(isset($data[$ufobj->field]) && !empty($data[$ufobj->field])){
                    $tempdate = $data[$ufobj->field];
                    $dateformat = JSSupportTicketModel::getJSModel('config')->getConfigurationByName("date_format");
                    if ($dateformat == 'm-d-Y') {
                      $arr = explode('-', $tempdate);
                      $tempdate = $arr[2] . '-' . $arr[0] . '-' . $arr[1];
                    } elseif ($dateformat == 'd-m-Y' OR $dateformat == 'Y-m-d') {
                      $arr = explode('-', $tempdate);
                      $tempdate = $arr[2] . '-' . $arr[1] . '-' . $arr[0];
                    }
                    $vardata = JHTML::_('date',strtotime($tempdate),"Y-m-d" );
                }else{
                    $vardata = '';
                }
            }
            else{
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
                    $unpublihsedFields = $this->getJSModel('userfields')->getUserUnpublishFieldsfor(1);
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
        $data['params'] = $params;

        $row = $this->getTable('tickets');
        if(!isset($data['ticketviaemail'])){
			$data['message'] = JFactory::getApplication()->input->get('message', '', 'raw');
            //$data['message'] = $this->getJSModel('jssupportticket')->getHtmlInput('message');
			if(!$user->getIsStaff() && !$user->getIsAdmin())
				$data['uid'] = $user->getId();
		}
        if (!$row->bind($data)) {
            $this->setError($row->getError());
            echo $row->getError();
            $return_value = false;
        }
        if(!$data['id'])
        if (!$row->check()) {
            $this->setError($row->getError());
            return MESSAGE_EMPTY;
        }
        if (trim($row->staffid) == "") { $row->staffid = NULL; }
        if (trim($row->helptopicid) == "") { $row->helptopicid = NULL; }
        if (trim($row->isoverdue) == "") { $row->isoverdue = NULL; }
        if (trim($row->duedate) == "") { $row->duedate = NULL; }
        if (!$row->store()) {
            $this->getJSModel('systemerrors')->updateSystemErrors($row->getError());
            $this->setError($row->getError());
            $return_value = false;
        }
        if (isset($return_value) && $return_value == false) {
            $message = $row->getError();
            $this->activity_log->storeActivityLog($row->id, 1, $eventtype, $message, 'Error');
            return SAVE_ERROR;
        }
        $ticketid = $row->id;
        if($data['id'] == ''){
            $db = JFactory::getDbo();
            $hash = $this->generateHash($ticketid);
            $query = "UPDATE `#__js_ticket_tickets` SET attachmentdir = CONCAT(attachmentdir,id),hash='$hash' WHERE id = ".$ticketid;
            $db->setQuery($query);
            $db->execute();
        }
        $filesize = $config['filesize'];
        $total = isset($_FILES['filename']['name']) ? count($_FILES['filename']['name']) : 0;
        $model_attachment = $this->getJSModel('attachments');
        for ($i = 0; $i < $total; $i++) {
            if ($_FILES['filename']['name'][$i] != '') {
                if ($_FILES['filename']['size'][$i] > 0) {
                    $uploadfilesize = $_FILES['filename']['size'][$i];
                    $uploadfilesize = $uploadfilesize / 1024; //kb
                    if ($uploadfilesize > $filesize) {
                        return FILE_SIZE_ERROR;
                    }
                    $file_name = str_replace(' ', '_', $_FILES['filename']['name'][$i]);
                    $result = $model_attachment->checkExtension($file_name);
                    if ($result == 'N') {
                        return FILE_EXTENTION_ERROR;
                    }
                    $res = $model_attachment->uploadAttchments($i, $ticketid, 1, 0, 'ticket');
                    if ($res) {
                        $result = $this->storeTicketAttachment($ticketid, $uploadfilesize, $file_name);
                    }else{
                        return FILE_RW_ERROR;
                    }
                }
            }
        }

        // new
        //removing custom field attachments

        if($customflagfordelete == true){
            foreach ($custom_field_namesfordelete as $key) {
                $res = $this->removeFileCustom($ticketid,$key);
            }
        }
        //storing custom field attachments
        if($customflagforadd == true){
            foreach ($custom_field_namesforadd as $key) {
                if ($_FILES[$key]['size'] > 0) { // logo
                    $res = $this->uploadFileCustom($ticketid,$key);
                }
            }
        }

        /*
        if (isset($data['issuesummary']) AND ! empty($data['issuesummary'])) {
            $result = $this->storeTicketIssueSummary($ticketid, $user->getId(), $user->getName(), $data['issuesummary'], $data['created']);
        }
        */
        if (isset($data['id']) && $data['id'] <> '') {//for edit case
            if (isset($data['internalnote']) AND ! empty($data['internalnote'])){
                $staffid=$user->getStaffid();
                if($user->getIsAdmin()){
                    $staffid=0;
                }
                $result = $this->storeTicketInternalNote($ticketid, $staffid, $data['internalnote'], $data['created'], $data['internalnotetitle']);
            }
        }elseif (isset($data['internalnote']) AND ! empty($data['internalnote'])) {
            $result = $this->storeTicketInternalNote($ticketid, $user->getStaffid(), $data['internalnote'], $data['created'], $data['internalnotetitle']);
        }

        //$this->getJSModel('userfields')->storeUserFieldData($data, $ticketid);


        if ($data['id'] == "") {
            if(isset($data['ticketviaemail']) && $data['ticketviaemail'] == 1){
                $msg = JText::_('Ticket is created via email');
				$maildata = $this->getJSModel('ticketviaemail')->getTicketEmailById($data['ticketviaemail_id']);
				$msg1 = $maildata->emailaddress;
            }else{
                $msg = JText::_('Ticket is created by');
            }
        }else{
            if(isset($data['ticketviaemail']) && $data['ticketviaemail'] == 1){
                $msg = JText::_('Ticket is updated via email');
				$maildata = $this->getJSModel('ticketviaemail')->getTicketEmailById($data['ticketviaemail_id']);
				$msg1 = $maildata->emailaddress;
            }else{
                $msg = JText::_('Ticket is updated by');
            }
        }
        if(isset($data['ticketviaemail']) && $data['ticketviaemail'] == 1){
            $message = $msg . " (" . $msg1 . ") ";
        }else{
            $message = $msg . " " . $user->getName() ." (" . $msg1 . ") ";
        }
        $this->activity_log->storeActivityLog($row->id, 1, $eventtype, $message, 'Sucessfull');

        if ($data['id'] == '')  // only for new ticket
            $this->getJSModel('email')->sendMail(1,1,$row->id); // Mailfor,Create Ticket,Ticketid

        JSSupportticketMessage::$recordid = $ticketid;
        if(!isset($data['ticketviaemail']))
			return SAVED;
		else
			return $ticketid;
    }

    private function ticketMultiSearch($searchkeys){
        $db = JFactory::getDbo();
        $inquery = "";
        $flag = true;
        if(!empty($searchkeys))
            if(isset($searchkeys['filter_ticketsearchkeys']) && !empty($searchkeys['filter_ticketsearchkeys'])){
                $keys = $searchkeys['filter_ticketsearchkeys'];
                $db = JFactory::getDbo();
                $keys = trim($keys);
                if (strlen($keys) == 11 || is_numeric($keys))
                    $inquery = " AND ticket.ticketid = ".$db->quote($keys);
                else if (strpos($keys, '@') && strpos($keys, '.'))
                    $inquery = " AND ticket.email LIKE ".$db->quote('%'.$keys.'%');
                else
                    $inquery = " AND ticket.subject LIKE ".$db->quote('%'.$keys.'%');
                $result['searchkeys'] = $keys;
                $flag = false;
            }else{
                if(isset($searchkeys['filter_ticketid']) && !empty($searchkeys['filter_ticketid'])){
                    $searchkeys['filter_ticketid'] = trim($searchkeys['filter_ticketid']);
                    $inquery =" AND ticket.ticketid = ".$db->quote($searchkeys['filter_ticketid']);
                    $result['ticketid'] = $searchkeys['filter_ticketid'];
                }
                if(isset($searchkeys['filter_from']) && !empty($searchkeys['filter_from'])){
                    $searchkeys['filter_from'] = trim($searchkeys['filter_from']);
                    $inquery .=" AND ticket.name LIKE ".$db->quote('%'.$searchkeys['filter_from'].'%');
                    $result['from'] = $searchkeys['filter_from'];
                }
                if(isset($searchkeys['filter_email']) && !empty($searchkeys['filter_email'])){
                    $searchkeys['filter_email'] = trim($searchkeys['filter_email']);
                    $inquery .=" AND ticket.email LIKE ".$db->quote('%'.$searchkeys['filter_email'].'%');
                    $result['email'] = $searchkeys['filter_email'];
                }
                if(isset($searchkeys['filter_department']) && !empty($searchkeys['filter_department'])){
                    $inquery .=" AND ticket.departmentid =".$searchkeys['filter_department'];
                    $result['department'] = $searchkeys['filter_department'];
                }
                if(isset($searchkeys['filter_priority']) && !empty($searchkeys['filter_priority'])){
                    $inquery .=" AND ticket.priorityid = ".$searchkeys['filter_priority'];
                    $result['priority'] = $searchkeys['filter_priority'];
                }
                if(isset($searchkeys['filter_subject']) && !empty($searchkeys['filter_subject'])){
                    $searchkeys['filter_subject'] = trim($searchkeys['filter_subject']);
                    $inquery .=" AND ticket.subject LIKE ".$db->quote('%'.$searchkeys['filter_subject'].'%');
                    $result['subject'] = $searchkeys['filter_subject'];
                }
                $config = $this->getJSModel('config')->getConfigs();
                if(isset($searchkeys['filter_dateend']) && !empty($searchkeys['filter_dateend'])){
                    $dateformat = $config['date_format'];
                    if ($dateformat == 'm-d-Y') {
                      $arr = explode('-', $searchkeys['filter_dateend']);
                      $searchkeys['filter_dateend'] = $arr[2] . '-' . $arr[0] . '-' . $arr[1];
                    } elseif ($dateformat == 'd-m-Y' OR $dateformat == 'Y-m-d') {
                      $arr = explode('-', $searchkeys['filter_dateend']);
                      $searchkeys['filter_dateend'] = $arr[2] . '-' . $arr[1] . '-' . $arr[0];
                    }
                    $searchkeys['filter_dateend'] = JHTML::_('date',strtotime($searchkeys['filter_dateend']),"Y-m-d H:i:s" );
                }

                if(isset($searchkeys['filter_datestart']) && !empty($searchkeys['filter_datestart']) ){
                    $dateformat = $config['date_format'];
                    if ($dateformat == 'm-d-Y') {
                      $arr = explode('-', $searchkeys['filter_datestart']);
                      $searchkeys['filter_datestart'] = $arr[2] . '-' . $arr[0] . '-' . $arr[1];
                    } elseif ($dateformat == 'd-m-Y' OR $dateformat == 'Y-m-d') {
                      $arr = explode('-', $searchkeys['filter_datestart']);
                      $searchkeys['filter_datestart'] = $arr[2] . '-' . $arr[1] . '-' . $arr[0];
                    }
                    $searchkeys['filter_datestart'] = JHTML::_('date',strtotime($searchkeys['filter_datestart']),"Y-m-d H:i:s" );
                }


                if(isset($searchkeys['filter_datestart']) && !empty($searchkeys['filter_datestart'])){
                    $inquery .=" AND DATE(ticket.created) >= ".$db->quote($searchkeys['filter_datestart']);
                    $result['datestart'] = $searchkeys['filter_datestart'];
                }
                if(isset($searchkeys['filter_dateend']) && !empty($searchkeys['filter_dateend'])){
                    $inquery .=" AND DATE(ticket.created) <= ".$db->quote($searchkeys['filter_dateend']);
                    $result['dateend'] = $searchkeys['filter_dateend'];
                }
                if(isset($searchkeys['filter_staffmember']) && !empty($searchkeys['filter_staffmember']) && is_numeric($searchkeys['filter_staffmember'])){
                    $inquery .=" AND ticket.staffid = ".$searchkeys['filter_staffmember'];
                    $result['staffmember'] = $searchkeys['filter_staffmember'];
                }
                if(isset($searchkeys['filter_assignedtome']) && $searchkeys['filter_assignedtome']==1){
                    $user = JSSupportticketCurrentUser::getInstance();
                    $inquery .=" AND ticket.staffid = ".$user->getStaffId();
                    $result['assignedtome'] = $searchkeys['filter_assignedtome'];
                }
                if($inquery=="")
                    $result['iscombinesearch'] = false;
                else
                    $result['iscombinesearch'] = true;
            }

        //Custom field search
        //start

        $mainframe = JFactory::getApplication();
        $option = 'com_jssupportticket';
        $data = getCustomFieldClass()->userFieldsForSearch(1);
        $valarray = array();
        $jsresetbutton = JFactory::getApplication()->input->get('jsresetbutton', NULL , 'post');
        if (!empty($data)) {
            foreach ($data as $uf) {
                $valarray[$uf->field] = $mainframe->getUserStateFromRequest($option . $uf->field , $uf->field , '','string');
                if($jsresetbutton == 1){//to reset date fields
                    $mainframe->setUserState($option.$uf->field,null);
                    $valarray[$uf->field] = null;
                }
                if (isset($valarray[$uf->field]) && $valarray[$uf->field] != null) {
                    switch ($uf->userfieldtype) {
                        case 'text':
                        case 'file':
                        case 'email':
                            $check_string = json_encode($valarray[$uf->field]);
                            $check_string = trim($check_string,'"');
                            $check_string = str_replace('\\', '\\\\\\\\', $check_string);
                            $inquery .= ' AND ticket.params REGEXP \'"' . $uf->field . '":"[^"]*' . htmlspecialchars($check_string) . '.*"\' ';
                            break;
                        case 'combo':
                            $inquery .= ' AND ticket.params LIKE \'%"' . $uf->field . '":"' . htmlspecialchars($valarray[$uf->field]) . '"%\' ';
                            break;
                        case 'depandant_field':
                            $inquery .= ' AND ticket.params LIKE \'%"' . $uf->field . '":"' . htmlspecialchars($valarray[$uf->field]) . '"%\' ';
                            break;
                        case 'radio':
                            if(isset($jsresetbutton)){
                                $mainframe->setUserState($option.$uf->field,'');
                                $valarray[$uf->field] = '';
                            }else{
                                $inquery .= ' AND ticket.params LIKE \'%"' . $uf->field . '":"' . htmlspecialchars($valarray[$uf->field]) . '"%\' ';
                            }
                            break;
                        case 'checkbox':
                            if(isset($jsresetbutton)){
                                $mainframe->setUserState($option.$uf->field,array());
                                $valarray[$uf->field] = array();
                            }else{
                                $finalvalue = '';
                                if(isset($valarray)){
                                    foreach($valarray[$uf->field] AS $value){
                                        $finalvalue .= $value.'.*';
                                    }
                                }
                                $inquery .= ' AND ticket.params REGEXP \'"' . $uf->field . '":"[^"]*' . htmlspecialchars($finalvalue) . '.*"\' ';
                            }
                            break;
                        case 'date':
                            $tempdate = htmlspecialchars($valarray[$uf->field]);
                            $dateformat = JSSupportTicketModel::getJSModel('config')->getConfigurationByName("date_format");
                            if ($dateformat == 'm-d-Y') {
                              $arr = explode('-', $tempdate);
                              $tempdate = $arr[2] . '-' . $arr[0] . '-' . $arr[1];
                            } elseif ($dateformat == 'd-m-Y' OR $dateformat == 'Y-m-d') {
                              $arr = explode('-', $tempdate);
                              $tempdate = $arr[2] . '-' . $arr[1] . '-' . $arr[0];
                            }
                            $tempdate = JHTML::_('date',strtotime($tempdate),"Y-m-d" );
                            $valarray[$uf->field] = $tempdate;
                            $inquery .= ' AND ticket.params LIKE \'%"' . $uf->field . '":"' . htmlspecialchars($valarray[$uf->field]) . '"%\' ';
                            break;
                        case 'textarea':
                            $inquery .= ' AND ticket.params REGEXP \'"' . $uf->field . '":"[^"]*' . htmlspecialchars($valarray[$uf->field]) . '.*"\' ';
                            break;
                        case 'multiple':
                            $finalvalue = '';
                            foreach($valarray[$uf->field] AS $value){
                                if($value != null){
                                    $finalvalue .= $value.'.*';
                                }
                            }
                            if($finalvalue !=''){
                                $inquery .= ' AND ticket.params REGEXP \'"' . $uf->field . '":"[^"]*'.htmlspecialchars($finalvalue).'"\'';
                            }
                            break;
                    }
                    $result['params'] = $valarray;
                }
            }
        }
        if($flag){
            if($inquery=="")
                $result['iscombinesearch'] = false;
            else
                $result['iscombinesearch'] = true;
        }

        //end

        $result['inquery'] = $inquery;
        return $result;
    }

    function getStaffMyTickets($searchkeys, $listtype,$sortby, $limitstart, $limit){
        $user = JSSupportticketCurrentUser::getInstance();
        if($user->getIsGuest()) return false;
        $staffid = $user->getStaffid();

        $db = $this->getDBO();
        $clause = " AND ";
        $wherequery = "";

        switch ($listtype) {
            case 1:
                $wherequery.= $clause . " ticket.status != 4 AND ticket.status != 5 ";
                $clause = " AND ";
                break;
            case 2:
                $wherequery .= $clause . " ticket.isanswered = 1 AND ticket.status = 3 ";//ticket.status != 4
                $clause = " AND ";
                break;
            case 3:
                $wherequery .= $clause . " ticket.isoverdue = 1 AND ticket.status != 4 ";
                $clause = " AND ";
                break;
            case 4:
            // case 5: // For merge status
                $wherequery .= $clause . " (ticket.status = 4  OR ticket.status = 5)";
                $clause = " AND ";
                break;
            case 6:
                $wherequery .= " ";
                break;
        }

        $multisearchquery = $this->ticketMultiSearch($searchkeys);

        $all_ticket = $user->checkUserPermission('All Tickets');
        if($all_ticket){
            $allticket_query = ' 1 = 1 ';
        }else{
            $allticket_query = " (ticket.staffid = $staffid OR ticket.departmentid IN (SELECT dept.departmentid FROM `#__js_ticket_acl_user_access_departments` AS dept WHERE dept.staffid = $staffid)) ";
        }

        $query = "SELECT COUNT(ticket.id)
                    FROM `#__js_ticket_tickets` AS ticket
                    LEFT JOIN `#__js_ticket_departments` AS department ON ticket.departmentid = department.id
                    JOIN `#__js_ticket_priorities` AS priority ON ticket.priorityid = priority.id
                    WHERE  $allticket_query ";
        $query .= $wherequery;
        $query .= $multisearchquery['inquery'];
        $db->setQuery($query);
        $total = $db->loadResult(); //tickets Count

        if($total <= $limitstart)
            $limitstart = 0;

        $staffmembers = $this->getJSModel('staff')->getStaffMembers();
        $departments = $this->getJSModel('department')->getDepartments();
        $priorities = $this->getPriorities();
        $staffmemberid = isset($multisearchquery['staffmember']) ? $multisearchquery['staffmember'] : '';
        $departmentid = isset($multisearchquery['department']) ? $multisearchquery['department'] : '';
        $priorityid = isset($multisearchquery['priority']) ? $multisearchquery['priority'] : '';

        $lists['staffmembers'] = JHTML::_('select.genericList', $staffmembers, 'filter_staffmember','', 'value', 'text',$staffmemberid);
        $lists['departments'] = JHTML::_('select.genericList', $departments, 'filter_department', '', 'value', 'text',$departmentid);
        $lists['priorities'] = JHTML::_('select.genericList', $priorities, 'filter_priority', '', 'value', 'text',$priorityid);

        $query = "SELECT DISTINCT ticket.*,department.departmentname AS departmentname ,priority.priority AS priority,priority.prioritycolour AS prioritycolour,staff.photo AS staffphoto,staff.id AS staffid, concat(assignstaff.firstname,' ',assignstaff.lastname) AS staffname, (SELECT name FROM `#__js_ticket_replies` AS reply WHERE ticket.id = reply.ticketid and reply.status = 1 ORDER BY reply.created DESC LIMIT 1) AS lastreplyby
                    FROM `#__js_ticket_tickets` AS ticket
                    LEFT JOIN `#__js_ticket_departments` AS department ON ticket.departmentid = department.id
                    JOIN `#__js_ticket_priorities` AS priority ON ticket.priorityid = priority.id
                    LEFT JOIN `#__js_ticket_staff` AS staff ON staff.uid = ticket.uid
                    LEFT JOIN `#__js_ticket_staff` AS assignstaff ON assignstaff.id = ticket.staffid
                    WHERE $allticket_query ";
        $query .= $wherequery;
        $query .= $multisearchquery['inquery'];
        $query .= " ORDER BY ".$sortby;
        $db->setQuery($query, $limitstart, $limit);
        $tickets = $db->loadObjectList(); //tickets
        $ticketinfo = array();
        $config = $this->getJSModel('config')->getConfigs();
        if($config['show_count_tickets'] == 1){
            $query = "SELECT COUNT(ticket.id)
                        FROM `#__js_ticket_tickets` AS ticket
                        LEFT JOIN `#__js_ticket_departments` AS department ON ticket.departmentid = department.id
                        JOIN `#__js_ticket_priorities` AS priority ON ticket.priorityid = priority.id
                        WHERE $allticket_query AND ticket.status != 4 AND ticket.status != 5";
            $query .= $multisearchquery['inquery'];
            $db->setQuery($query);
            $ticketinfo['open'] = $db->loadResult(); //Open Tickets

            $query = "SELECT COUNT(ticket.id)
                        FROM `#__js_ticket_tickets` AS ticket
                        LEFT JOIN `#__js_ticket_departments` AS department ON ticket.departmentid = department.id
                        JOIN `#__js_ticket_priorities` AS priority ON ticket.priorityid = priority.id
                        WHERE $allticket_query AND (ticket.status = 4 OR ticket.status = 5)";
            $query .= $multisearchquery['inquery'];
            $db->setQuery($query);
            $ticketinfo['close'] = $db->loadResult(); //Closed Tickets

            $query = "SELECT COUNT(ticket.id)
                        FROM `#__js_ticket_tickets` AS ticket
                        LEFT JOIN `#__js_ticket_departments` AS department ON ticket.departmentid = department.id
                        JOIN `#__js_ticket_priorities` AS priority ON ticket.priorityid = priority.id
                        WHERE $allticket_query AND ticket.status = 3";
            $query .= $multisearchquery['inquery'];
            $db->setQuery($query);
            $ticketinfo['isanswered'] = $db->loadResult(); //Is Answered Tickets

            $query = "SELECT COUNT(ticket.id)
                        FROM `#__js_ticket_tickets` AS ticket
                        LEFT JOIN `#__js_ticket_departments` AS department ON ticket.departmentid = department.id
                        JOIN `#__js_ticket_priorities` AS priority ON ticket.priorityid = priority.id
                        WHERE $allticket_query AND ticket.isoverdue = 1 AND (ticket.status != 4 OR ticket.status != 5)";
            $query .= $multisearchquery['inquery'];
            $db->setQuery($query);
            $ticketinfo['isoverdue'] = $db->loadResult(); //OverDue Tickets

            $query = "SELECT COUNT(ticket.id)
                        FROM `#__js_ticket_tickets` AS ticket
                        LEFT JOIN `#__js_ticket_departments` AS department ON ticket.departmentid = department.id
                        JOIN `#__js_ticket_priorities` AS priority ON ticket.priorityid = priority.id
                        WHERE $allticket_query ";
            $query .= $multisearchquery['inquery'];
            $db->setQuery($query);
            $ticketinfo['mytickets'] = $db->loadResult(); //My Tickets
        }

        $multisearchquery['inquery'] = ""; //empty the key

        $result[0] = $tickets;
        $result[1] = $total;
        $result[2] = $lists;
        $result[3] = $ticketinfo;
        $result[4] = $multisearchquery;
        return $result;
    }

    function getAdminMyTickets($searchdepartmentid, $searchpriorityid, $searchstaffmember, $searchsubject, $searchfrom, $searchfromemail, $searchticketid, $listtype, $sortby,$datestart, $dateend, $limitstart, $limit) {

        $db = $this->getDBO();
        // Ticket Default Status
        // 0 -> New Ticket
        // 1 -> Waiting admin/staff reply
        // 2 -> in progress
        // 3 -> waiting for customer reply
        // 4 -> close ticket

        // $listtype == 1  - open
        // $listtype == 2  - answerd
        // $listtype == 3  - overdue
        // $listtype == 4  - close
        // $listtype == 5  - my tickets
		$totalquery = "SELECT COUNT(ticket.id) FROM `#__js_ticket_tickets` AS ticket WHERE 1 = 1 ";
        $query = "SELECT ticket.*, dep.departmentname AS departmentname, dep.id AS departmentid, priority.priority AS priority, priority.prioritycolour AS prioritycolour,staff.photo AS staffphoto,staff.id AS staffid, staff.firstname as stafffirstname, staff.lastname as stafflastname
                  FROM `#__js_ticket_tickets` AS ticket
                  LEFT JOIN `#__js_ticket_departments` AS dep ON ticket.departmentid = dep.id
                  LEFT JOIN `#__js_ticket_priorities` AS priority ON ticket.priorityid = priority.id
                  LEFT JOIN `#__js_ticket_staff` AS staff ON staff.id = ticket.staffid
                  WHERE 1=1 ";

        $userquery = '';
        $uid = trim(JFactory::getApplication()->input->get('uid'));
        if($uid != null && is_numeric($uid) && $uid > 0){
            $userquery = ' AND ticket.uid = '.$uid;
        }

            $data = getCustomFieldClass()->userFieldsForSearch(1);
            $valarray = array();
            if (!empty($data)) {
                foreach ($data as $uf) {
                    $valarray[$uf->field] = JFactory::getApplication()->input->get($uf->field,NULL, 'post');
                    $jsresetbutton = JFactory::getApplication()->input->get('jsresetbutton',0);
                    if($jsresetbutton == 1){
                        $valarray[$uf->field] = null;
                    }
                    if (isset($valarray[$uf->field]) && $valarray[$uf->field] != null) {
                        switch ($uf->userfieldtype) {
                            case 'text':
                            case 'file':
                            case 'email':
                                $query .= ' AND ticket.params REGEXP \'"' . $uf->field . '":"[^"]*' . htmlspecialchars($valarray[$uf->field]) . '.*"\' ';
                                $totalquery .= ' AND ticket.params REGEXP \'"' . $uf->field . '":"[^"]*' . htmlspecialchars($valarray[$uf->field]) . '.*"\' ';
                                break;
                            case 'combo':
                                $query .= ' AND ticket.params LIKE \'%"' . $uf->field . '":"' . htmlspecialchars($valarray[$uf->field]) . '"%\' ';
                                $totalquery .= ' AND ticket.params LIKE \'%"' . $uf->field . '":"' . htmlspecialchars($valarray[$uf->field]) . '"%\' ';
                                break;
                            case 'depandant_field':
                                $query .= ' AND ticket.params LIKE \'%"' . $uf->field . '":"' . htmlspecialchars($valarray[$uf->field]) . '"%\' ';
                                $totalquery .= ' AND ticket.params LIKE \'%"' . $uf->field . '":"' . htmlspecialchars($valarray[$uf->field]) . '"%\' ';
                                break;
                            case 'radio':
                                $query .= ' AND ticket.params LIKE \'%"' . $uf->field . '":"' . htmlspecialchars($valarray[$uf->field]) . '"%\' ';
                                $totalquery .= ' AND ticket.params LIKE \'%"' . $uf->field . '":"' . htmlspecialchars($valarray[$uf->field]) . '"%\' ';
                                break;
                            case 'checkbox':
                                $finalvalue = '';
                                foreach($valarray[$uf->field] AS $value){
                                    $finalvalue .= $value.'.*';
                                }
                                $query .= ' AND ticket.params REGEXP \'"' . $uf->field . '":"[^"]*' . htmlspecialchars($finalvalue) . '.*"\' ';
                                $totalquery .= ' AND ticket.params REGEXP \'"' . $uf->field . '":"[^"]*' . htmlspecialchars($finalvalue) . '.*"\' ';
                                break;
                            case 'date':
                                $tempdate = htmlspecialchars($valarray[$uf->field]);
                                $dateformat = JSSupportTicketModel::getJSModel('config')->getConfigurationByName("date_format");
                                if ($dateformat == 'm-d-Y') {
                                  $arr = explode('-', $tempdate);
                                  $tempdate = $arr[2] . '-' . $arr[0] . '-' . $arr[1];
                                } elseif ($dateformat == 'd-m-Y' OR $dateformat == 'Y-m-d') {
                                  $arr = explode('-', $tempdate);
                                  $tempdate = $arr[2] . '-' . $arr[1] . '-' . $arr[0];
                                }
                                $tempdate = JHTML::_('date',strtotime($tempdate),"Y-m-d" );
                                $valarray[$uf->field] = $tempdate;
                                $query .= ' AND ticket.params LIKE \'%"' . $uf->field . '":"' . htmlspecialchars($valarray[$uf->field]) . '"%\' ';
                                $totalquery .= ' AND ticket.params LIKE \'%"' . $uf->field . '":"' . htmlspecialchars($valarray[$uf->field]) . '"%\' ';
                                break;
                            case 'textarea':
                                $query .= ' AND ticket.params REGEXP \'"' . $uf->field . '":"[^"]*' . htmlspecialchars($valarray[$uf->field]) . '.*"\' ';
                                $totalquery .= ' AND ticket.params REGEXP \'"' . $uf->field . '":"[^"]*' . htmlspecialchars($valarray[$uf->field]) . '.*"\' ';
                                break;
                            case 'multiple':
                                $finalvalue = '';
                                foreach($valarray[$uf->field] AS $value){
                                    if($value != null){
                                        $finalvalue .= $value.'.*';
                                    }
                                }
                                if($finalvalue !=''){
                                    $query .= ' AND ticket.params REGEXP \'"' . $uf->field . '":"[^"]*'.htmlspecialchars($finalvalue).'"\'';
                                    $totalquery .= ' AND ticket.params REGEXP \'"' . $uf->field . '":"[^"]*'.htmlspecialchars($finalvalue).'"\'';
                                }
                                break;
                        }
                        $params_filter = $valarray;
                    }
                }
            }


        if ($searchsubject <> ''){
            $searchsubject = trim($searchsubject);
            $query .= " AND ticket.subject LIKE " . $db->quote('%' . $searchsubject . '%');
            $totalquery .= " AND ticket.subject LIKE " . $db->quote('%' . $searchsubject . '%');
        }
        if ($searchfrom <> ''){
            $searchfrom = trim($searchfrom);
            $query .= " AND ticket.name LIKE " . $db->quote('%' . $searchfrom . '%');
            $totalquery .= " AND ticket.name LIKE " . $db->quote('%' . $searchfrom . '%');
        }
        if ($searchfromemail <> ''){
            $searchfromemail = trim($searchfromemail);
            $query .= " AND ticket.email LIKE " . $db->quote('%' . $searchfromemail . '%');
            $totalquery .= " AND ticket.email LIKE " . $db->quote('%' . $searchfromemail . '%');
        }
        if ($searchticketid <> ''){
            $searchticketid = trim($searchticketid);
            $query .= " AND ticket.ticketid LIKE " . $db->quote('%' . $searchticketid . '%');
            $totalquery .= " AND ticket.ticketid LIKE " . $db->quote('%' . $searchticketid . '%');
        }
        if ($searchdepartmentid <> ''){
            if(!is_numeric($searchdepartmentid)) return false;
            $query .= " AND ticket.departmentid = " . $searchdepartmentid;
            $totalquery .= " AND ticket.departmentid = " . $searchdepartmentid;
        }
        if ($searchpriorityid <> ''){
            if(!is_numeric($searchpriorityid)) return false;
            $query .= " AND ticket.priorityid = " . $searchpriorityid;
            $totalquery .= " AND ticket.priorityid = " . $searchpriorityid;
        }
        if ($searchstaffmember <> ''){
            if(!is_numeric($searchstaffmember)) return false;
            $query .= " AND ticket.staffid = " . $searchstaffmember;
            $totalquery .= " AND ticket.staffid = " . $searchstaffmember;
		}
        $config = $this->getJSModel('config')->getConfigs();
        $dateformat = $config['date_format'];
        if(isset($dateend) && !empty($dateend)){
            $dateformat = $config['date_format'];
            if ($dateformat == 'm-d-Y') {
              $arr = explode('-', $dateend);
              $dateend = $arr[2] . '-' . $arr[0] . '-' . $arr[1];
            } elseif ($dateformat == 'd-m-Y' OR $dateformat == 'Y-m-d') {
              $arr = explode('-', $dateend);
              $dateend = $arr[2] . '-' . $arr[1] . '-' . $arr[0];
            }
            $dateend = JHTML::_('date',strtotime($dateend),"Y-m-d H:i:s" );
        }

        if(isset($datestart) && !empty($datestart) ){
            $dateformat = $config['date_format'];
            if ($dateformat == 'm-d-Y') {
              $arr = explode('-', $datestart);
              $datestart = $arr[2] . '-' . $arr[0] . '-' . $arr[1];
            } elseif ($dateformat == 'd-m-Y' OR $dateformat == 'Y-m-d') {
              $arr = explode('-', $datestart);
              $datestart = $arr[2] . '-' . $arr[1] . '-' . $arr[0];
            }
            $datestart = JHTML::_('date',strtotime($datestart),"Y-m-d H:i:s" );
        }


        if(isset($datestart) && !empty($datestart)){
            $query .=" AND DATE(ticket.created) >= ".$db->quote($datestart);
            $totalquery .=" AND DATE(ticket.created) >= ".$db->quote($datestart);
        }
        if(isset($dateend) && !empty($dateend)){
            $query .=" AND DATE(ticket.created) <= ".$db->quote($dateend);
            $totalquery .=" AND DATE(ticket.created) <= ".$db->quote($dateend);
        }
        switch ($listtype) {
            case 1:
                $query .= " AND ticket.status != 4 AND ticket.isanswered = 0 AND ticket.status != 5";
                $totalquery .= " AND ticket.status != 4 AND ticket.isanswered = 0 AND ticket.status != 5";
                break;
            case 2:
                /*$query .= " AND ticket.status != 4 AND ticket.isanswered = 1 ";
                $totalquery .= " AND ticket.status != 4 AND ticket.isanswered = 1 ";*/
                $query .= " AND ticket.status = 3 AND ticket.isanswered = 1 ";
                $totalquery .= " AND ticket.status = 3 AND ticket.isanswered = 1 ";
                break;
            case 3:
                $query .= " AND ticket.isoverdue = 1 AND (ticket.status != 4 OR ticket.status != 5) ";
                $totalquery .= " AND ticket.isoverdue = 1 AND (ticket.status != 4 OR ticket.status != 5) ";
                break;
            case 4:
                $query .= " AND (ticket.status = 4 OR ticket.status = 5)";
                $totalquery .= " AND (ticket.status = 4 OR ticket.status = 5)";
                break;
            case 5:
                $query .= " ";
                break;
        }

        $totalquery .= $userquery;

        $db = JFactory::getDbo();
        $db->setQuery($totalquery);
        $total = $db->loadResult();

        if ($total <= $limitstart)
            $limitstart = 0;
        $query .= $userquery;
        $query .=  ' ORDER BY '.$sortby;
        $db->setQuery($query, $limitstart, $limit);
        $tickets = $db->loadObjectList(); // Tickets
        $ticketinfo = array();
        $config = $this->getJSModel('config')->getConfigs();
        if($config['show_count_tickets'] == 1){
            $query = "SELECT COUNT(id) FROM `#__js_ticket_tickets` as ticket
                        WHERE ticket.status != 4 AND ticket.isanswered = 0 AND ticket.status != 5";
            $query .= $userquery;
            $db->setQuery($query);
            $ticketinfo['open'] = $db->loadResult(); // Open Tickets

            $query = "SELECT COUNT(id) FROM `#__js_ticket_tickets` AS ticket
                        WHERE (ticket.status = 4 OR ticket.status = 5)";
            $query .= $userquery;
            $db->setQuery($query);
            $ticketinfo['close'] = $db->loadResult(); // Closed Tickets

            //$query = "SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE status != 4 AND isanswered = 1";
            $query = "SELECT COUNT(id) FROM `#__js_ticket_tickets` AS ticket WHERE status = 3 AND isanswered = 1";
            $query .= $userquery;
            $db->setQuery($query);
            $ticketinfo['isanswered'] = $db->loadResult(); // IsAnswered Tickets

            $query = "SELECT COUNT(ticket.id) FROM `#__js_ticket_tickets` AS ticket WHERE ticket.isoverdue = 1 AND ticket.status != 4";
            $query .= $userquery;
            $db->setQuery($query);
            $ticketinfo['isoverdue'] = $db->loadResult(); //IsOverDue Tickets

            $inquery = "";
            $query = "SELECT COUNT(id) FROM `#__js_ticket_tickets` AS ticket WHERE 1=1 ";
            $query .= $inquery;
            $query .= $userquery;
            $db->setQuery($query);
            $ticketinfo['mytickets'] = $db->loadResult(); // My Tickets
        }

        $departments = $this->getJSModel('department')->getDepartments();
        $priorities = $this->getPriorities();

        $lists['params'] =  isset($params_filter) ? $params_filter : '';
        $lists['searchsubject'] = $searchsubject;
        $lists['searchfrom'] = $searchfrom;
        $lists['searchfromemail'] = $searchfromemail;
        $lists['datestart'] = $datestart;
        $lists['dateend'] = $dateend;
        $lists['searchticket'] = $searchticketid;
        $lists['staffmembers'] = JHTML::_('select.genericList', $this->getJSModel('staff')->getStaffMembers(), 'filter_staffmember', 'class="js-form-select-field "'  . '', 'value', 'text', $searchstaffmember);
        $lists['departments'] = JHTML::_('select.genericList', $departments, 'filter_department', '', 'value', 'text',$searchdepartmentid);
        $lists['priorities'] = JHTML::_('select.genericList', $priorities, 'filter_priority', '', 'value', 'text',$searchpriorityid);

        $result[0] = $tickets;
        $result[1] = $total;
        $result[2] = $lists;
        $result[3] = $ticketinfo;
        return $result;
    }

    function getUserMyTickets($email,$listtype,$searchkeys,$sortby,$limitstart,$limit) {
        if(!$email) return false;
        $db = $this->getDBO();
        $user = JSSupportticketCurrentUser::getInstance();

        $query = "SELECT COUNT(id) FROM `#__js_ticket_tickets` AS ticket WHERE ";
        if(!$user->getIsGuest()){
            $query .= ' (ticket.email = '.$db->quote($email).' OR ticket.uid = '.$user->getId().')';
        }else{
            $query .= "ticket.email = ".$db->quote($email);
        }

        switch ($listtype){
            case 1:
                $query .= " AND ticket.status != 4 AND ticket.status != 5";
            break;
            case 4:
                $query .= " AND (ticket.status = 4 OR ticket.status = 5)";
            break;
            case 2:
                $query .= " AND ticket.status != 4 AND ticket.status != 5 AND ticket.isanswered = 1";
            break;
            case 5:
                $query .= " ";
            break;
        }

        $multisearchquery = $this->ticketMultiSearch($searchkeys);

        $departments = $this->getJSModel('department')->getDepartments();
        $priorities = $this->getPriorities();
        $departmentid = isset($multisearchquery['department']) ? $multisearchquery['department'] : '';
        $priorityid = isset($multisearchquery['priority']) ? $multisearchquery['priority'] : '';

        $lists['departments'] = JHTML::_('select.genericList', $departments, 'filter_department', '', 'value', 'text',$departmentid);
        $lists['priorities'] = JHTML::_('select.genericList', $priorities, 'filter_priority', '', 'value', 'text',$priorityid);

        $query .= $multisearchquery['inquery'];
        $db->setQuery($query);
        $total = $db->loadResult(); //Total Tickets

        $query = "SELECT ticket.*,dep.departmentname AS departmentname, priority.priority AS priority, priority.prioritycolour AS prioritycolour,
                    concat(staff.firstname,' ',staff.lastname) AS staffname, staff.photo AS staffphoto,
                    (SELECT COUNT(attach.id) From `#__js_ticket_attachments` AS attach WHERE attach.ticketid = ticket.id) AS attachments, (SELECT reply.name FROM `#__js_ticket_replies` AS reply WHERE ticket.id = reply.ticketid ORDER BY created DESC LIMIT 1 ) lastreplyby
                        FROM `#__js_ticket_tickets` AS ticket
                        JOIN `#__js_ticket_priorities` AS priority ON ticket.priorityid = priority.id
                        LEFT JOIN `#__js_ticket_departments` AS dep ON ticket.departmentid = dep.id
                        LEFT JOIN `#__js_ticket_staff` AS staff ON ticket.staffid = staff.id
                        WHERE ";
        if(!$user->getIsGuest()){
            $query .= ' (ticket.email = '.$db->quote($email).' OR ticket.uid = '.$user->getId().')';
        }else{
            $query .= "ticket.email = ".$db->quote($email);
        }

        switch ($listtype){
            case 1:
                $query .= " AND ticket.status != 4 AND ticket.status != 5";
            break;
            case 4:
                $query .= " AND (ticket.status = 4 OR ticket.status = 5)";
            break;
            case 2:
                $query .= " AND ticket.status != 4 AND ticket.status != 5 AND ticket.isanswered = 1";
            break;
            case 5:
                $query .= " ";
            break;
        }

        $query .= $multisearchquery['inquery'];
        $query .= " ORDER BY ".$sortby;

        $db->setQuery($query,$limitstart,$limit);
        $result = $db->loadObjectList(); //Tickets
        $ticketinfo = array();
        $config = $this->getJSModel('config')->getConfigs();
        if($config['show_count_tickets'] == 1){
            $query = "SELECT COUNT(DISTINCT ticket.id)
                        FROM `#__js_ticket_tickets` AS ticket
                        WHERE ticket.status != 4 AND ticket.status != 5 AND ";
            if(!$user->getIsGuest()){
                $query .= ' (ticket.email = '.$db->quote($email).' OR ticket.uid = '.$user->getId().')';
            }else{
                $query .= "ticket.email = ".$db->quote($email);
            }
            $query .= $multisearchquery['inquery'];
            $db->setQuery($query);
            $ticketinfo['open'] = $db->loadResult(); // Open Tickets

            $query = "SELECT COUNT(DISTINCT ticket.id)
                        FROM `#__js_ticket_tickets` AS ticket
                        WHERE ticket.status != 4 AND ticket.isanswered = 1 AND ticket.status != 5 AND ";
            if(!$user->getIsGuest()){
                $query .= ' (ticket.email = '.$db->quote($email).' OR ticket.uid = '.$user->getId().')';
            }else{
                $query .= "ticket.email = ".$db->quote($email);
            }
            $query .= $multisearchquery['inquery'];
            $db->setQuery($query);
            $ticketinfo['answered'] = $db->loadResult(); // Answered Tickets

            $query = "SELECT COUNT(DISTINCT ticket.id)
                        FROM `#__js_ticket_tickets` AS ticket
                        WHERE (ticket.status = 4 OR ticket.status = 5) AND ";
            if(!$user->getIsGuest()){
                $query .= ' (ticket.email = '.$db->quote($email).' OR ticket.uid = '.$user->getId().')';
            }else{
                $query .= "ticket.email = ".$db->quote($email);
            }
            $query .= $multisearchquery['inquery'];
            $db->setQuery($query);
            $ticketinfo['close'] = $db->loadResult(); // Closed Tickets

            $query = "SELECT COUNT(DISTINCT ticket.id)
                        FROM `#__js_ticket_tickets` AS ticket
                        WHERE ";
            if(!$user->getIsGuest()){
                $query .= ' (ticket.email = '.$db->quote($email).' OR ticket.uid = '.$user->getId().')';
            }else{
                $query .= "ticket.email = ".$db->quote($email);
            }
            $query .= $multisearchquery['inquery'];
            $db->setQuery($query);
            $ticketinfo['allticket'] = $db->loadResult(); // Closed Tickets
        }

        $multisearchquery['inquery'] = ""; // empty the inquery

        if($total == '') $total = 0;

        $return[0] = $result;
        $return[1] = $total;
        $return[2] = $ticketinfo;
        $return[3] = $lists;
        $return[4] = $multisearchquery;
        return $return;
    }

    function getFormData($id,$data) {
        if($id) if (!is_numeric($id)) return false;
        $db = $this->getDBO();
        $model_staff = $this->getJSModel('staff');
        $user = JSSupportticketCurrentUser::getInstance();
        $departments = $this->getJSModel('department')->getDepartments();
        $defaultdepartmentid = $this->getJSModel('department')->getDefaultDepartmentID();
        $assignto = $model_staff->getStaffMembers();
        $priorities = $this->getPriorities();
        if ($id <> '') {
            $user = JSSupportticketCurrentUser::getInstance();
            if(JFactory::getApplication()->isClient('site')){
                if ($user->getIsStaff()) { //staff
                    $staff_allowed  = $this->validateTicketDetailForStaff($id);
                    if(!$staff_allowed){
                        return false;
                    }
                }
            }
            $query = "SELECT ticket.*,ticket.name AS ticketname, internalnote.note AS internalnote, user.name
                      FROM `#__js_ticket_tickets` AS ticket
                      LEFT JOIN `#__js_ticket_notes` AS internalnote ON internalnote.ticketid = ticket.id
                      LEFT JOIN `#__users` AS user ON user.id = ticket.uid
                      WHERE ticket.id = " . $db->quote($id);
            $db->setQuery($query);
            $editticket = $db->loadObject();
            //to store hash value of id against old tickets
            if(isset($editticket)){
                if( $editticket->hash == null ){
                    $hash = $this->generateHash($id);
                    $query = "UPDATE `#__js_ticket_tickets` SET `hash`='".$hash."' WHERE id=".$id;
                    $db->setQuery($query);
                    $db->execute();
                } //end
            }
        }
        if(isset($editticket))
            $premade = $model_staff->getStaffAccessDepartmentPremade($user->getId(), $editticket->departmentid);
        else
            $premade = $model_staff->getStaffAccessDepartmentPremade($user->getId(), 0);
        //get the required fields for combobox
        $userfieldmodel = $this->getJSModel('userfields');
        $reqdepartment = $userfieldmodel->isFieldRequiredByField('department') == 1 ? ' required' : '';
        $reqhelptopicid = $userfieldmodel->isFieldRequiredByField('helptopic') == 1 ? ' required' : '';
        $reqassignto = $userfieldmodel->isFieldRequiredByField('assignto') == 1 ? ' required' : '';

        if(isset($editticket)) {
            $departmentid = isset($data['departmentid']) ? $data['departmentid'] : $editticket->departmentid;
            $helptopicid = isset($data['helptopicid']) ? $data['helptopicid'] : $editticket->helptopicid;
            $priorityid = isset($data['priorityid']) ? $data['priorityid'] : $editticket->priorityid;
            $staffid = isset($data['staffid']) ? $data['staffid'] : $editticket->staffid;
            $lists['departments'] = JHTML::_('select.genericList', $departments, 'departmentid', 'class="js-form-select-field '.$reqdepartment.'" ' . 'onChange="gethelptopicandpremade(\'helptopic\',\'premades\', this.value)"', 'value', 'text', $departmentid);
            $lists['helptopic'] = JHTML::_('select.genericList', $this->getJSModel('helptopic')->getHelpTopicForCombo($editticket->departmentid, JText::_('Select Help Topic')), 'helptopicid', 'class="js-form-select-field' .$reqhelptopicid.'" ' . '', 'value', 'text', $helptopicid);
            $lists['priorities'] = JHTML::_('select.genericList', $priorities, 'priorityid', 'class="js-form-select-field required" ' . '', 'value', 'text', $priorityid);
            $lists['assignto'] = JHTML::_('select.genericList', $assignto, 'staffid', 'class="js-form-select-field '.$reqassignto.'" ' . '', 'value', 'text', $staffid);
        } else {
            $query = "SELECT id FROM `#__js_ticket_priorities` WHERE isdefault = 1";
            $db->setQuery($query);
            $priority = $db->loadObject();
            if(isset($data['departmentid'])){
                $departmentid = $data['departmentid'];
            }else if(JFactory::getApplication()->input->get('departmentid') > 0){
                $departmentid = JFactory::getApplication()->input->get('departmentid');
            }else{
                $departmentid = $defaultdepartmentid;
            }

            if(isset($data['helptopicid'])){
                $helptopicid = $data['helptopicid'];
            }else if(JFactory::getApplication()->input->get('helptopicid') > 0){
                $helptopicid = JFactory::getApplication()->input->get('helptopicid');
            }else{
                $helptopicid = 0;
            }
            $priorityid = isset($data['priorityid']) ? $data['priorityid'] : $priority->id;
            $staffid = isset($data['staffid']) ? $data['staffid'] : $user->getID();
            $helptopiccombovalue = ($departmentid == '') ? $defaultdepartmentid : $departmentid;
            $lists['departments'] = JHTML::_('select.genericList', $departments, 'departmentid', 'class="inputbox js-form-select-field '.$reqdepartment.'" ' . 'onChange="gethelptopicandpremade(\'helptopic\',\'premades\', this.value)"', 'value', 'text', $departmentid);
            $lists['helptopic'] = JHTML::_('select.genericList', $this->getJSModel('helptopic')->getHelpTopicForCombo($helptopiccombovalue, JText::_('Select Help Topic')), 'helptopicid', 'class="inputbox js-form-select-field '.$reqhelptopicid.'" ' . '', 'value', 'text', $helptopicid);
            $lists['priorities'] = JHTML::_('select.genericList', $priorities, 'priorityid', 'class="inputbox js-form-select-field required" ' . '', 'value', 'text', $priorityid);
            $lists['assignto'] = JHTML::_('select.genericList', $assignto, 'staffid', 'class="inputbox js-form-select-field '.$reqassignto.'" ' . '', 'value', 'text', $staffid);
        }

        $reqpremade = $userfieldmodel->isFieldRequiredByField('premade') == 1 ? ' required' : '';
        $lists['premade'] = JHTML::_('select.genericList', $premade, 'premadeid', 'class="inputbox js-ticket-premade-select '.$reqpremade.'" ' . 'onChange="getpremade(\'issue_summary\', this.value ,append.checked)"', 'value', 'text', '');

        $ufields = $this->getJSModel('userfields');
        if (isset($editticket))
            $result[0] = $editticket;
        $result[1] = '';
        $result[2] = $lists;
        //$result[3] = $ufields->getUserFieldsForForm(1, $id);
        $result[4] = $ufields->getFieldsOrderingforForm(1);
        $result[5] = $this->getJSModel('attachments')->getAttachmentForForm($id);

        return $result;
    }

    function getTicketDetail($id) {

        if (!is_numeric($id))
            return false;
        $config = $this->getJSModel('config')->getConfigByFor('default');
        $autoclose = $config['ticket_auto_close_indays'];

        if ($autoclose == "")
            $autoclose = 0;

        $db = $this->getDBO();
        $query = "SELECT ticket.* , tickpriority.priority AS priority,tickpriority.prioritycolour AS prioritycolour ,
                 dep.departmentname ,concat(staff.firstname,' ',staff.lastname) AS staffname,attachdata.id AS attachmentid,
                 (SELECT Count(id) FROM `#__js_ticket_replies` AS reply WHERE reply.ticketid = ticket.id) AS replies, attachdata.filename AS filename , attachdata.filesize AS filesize,
                 DATE_ADD(ticket.lastreply,INTERVAL $autoclose DAY) AS autoclosedate

                 FROM `#__js_ticket_tickets` AS ticket
                 JOIN `#__js_ticket_priorities` AS tickpriority ON ticket.priorityid =tickpriority.id
                 LEFT JOIN `#__js_ticket_departments` AS dep ON ticket.departmentid =dep.id
                 LEFT JOIN `#__js_ticket_attachments` AS attachdata ON ticket.id =attachdata.ticketid AND attachdata.replyattachmentid = 0
                 LEFT JOIN `#__js_ticket_staff` AS staff ON ticket.staffid = staff.id
                 WHERE ticket.id = " . $id;
        $db->setQuery($query);
        $ticket = $db->loadObjectList();

        $query = "SELECT replies.*,attachment.id AS attachmentid, attachment.filename AS filename, attachment.filesize AS filesize, staff.appendsignature AS appendsignature, staff.signature AS signature,
                 (SELECT COUNT(id) FROM `#__js_ticket_attachments` WHERE ticketid = replies.ticketid AND replyattachmentid = replies.id) AS count
                 FROM`#__js_ticket_replies` AS replies
                 LEFT JOIN `#__js_ticket_staff` AS staff ON replies.staffid = staff.id
                 LEFT JOIN `#__js_ticket_attachments` AS attachment ON replies.ticketid = attachment.ticketid AND replies.id = attachment.replyattachmentid
                 WHERE replies.ticketid = " . $id . " ORDER BY replies.created";
        $db->setQuery($query);
        $messages = $db->loadObjectList();
        $ufields = $this->getJSModel('userfields');

        $result['ticket'] = $ticket[0];
        $result['attachment'] = $ticket;
        $result['messages'] = $messages;

        $result[7] = $ufields->getUserFieldsForView(1, $id);
        //$result[8] = $ufields->getFieldsOrdering(1); // company fields
        //$tickethistory=$this->getTicketHistory($id);
        //if($tickethistory) $result[9] = $tickethistory;
        return $result;
    }

    function getTicketDetailById($id){
        if (!is_numeric($id))
            return false;
        $permission_granted = false;
        $time_taken = 0;
        $user = JSSupportticketCurrentUser::getInstance();
        if ($user->getIsStaff()) { //staff
            $time_taken = $this->getJSModel('staff')->getTimeTakenByTicketId($id);
            if($user->getIsAdmin()){
                $permission_granted = true;
            }else{
                // check whether all tickets permission is allowed to staff
                $allticket = $user->checkUserPermission('All Tickets');
                $permission_granted = true;
                if(!$allticket){
                    $permission_granted = $this->validateTicketDetailForStaff($id);
                }
                $_SESSION['ticket_time_start'][$id] = date('Y-m-d H:i:s');
            }
        } else { // user
            if($user->getIsAdmin()){
                $permission_granted = true;
                $time_taken = $this->getJSModel('staff')->getTimeTakenByTicketId($id);
            }
            elseif (!$user->getIsGuest()){
                $permission_granted = $this->validateTicketDetailForUser($id);
                if (!$permission_granted) { // to show message when ticket exsists but current user not allowed to view it.
                    $session = JFactory::getApplication()->getSession();
                    $ticketuserid = $session->get('ticketuserid',-1);
                    if($ticketuserid == 0){
                        return 4;// when ticket belongs to visitor but logged in member trying to view it
                    }else{
                        return 2;// when logged in tries to view ticket that does not belong to him
                    }
                }
            }else{
                $permission_granted = $this->validateTicketDetailForVisitor($id);
                if (!$permission_granted) { // to show message when ticket exsists but visitor can not view it.
                    return 3;
                }
            }
        }
        if (!$permission_granted) { // validation failed
            return false;
        }

        $db = $this->getDbo();

        $inquery="";
        if($user->getIsGuest()){
            //$inquery = " AND ticket.uid=0";
        }
        $query = "SELECT ticket.*,department.departmentname AS departmentname ,priority.priority AS priority,priority.prioritycolour AS prioritycolour,attach.id AS attachmentid,
            helptopic.topic AS helptopic ,staff.firstname AS firstname ,staff.lastname AS lastname,attach.filename,attach.filesize,staff.photo AS staffphoto,staff.id AS staffid,
            (SELECT COUNT(id) FROM `#__js_ticket_attachments` WHERE ticketid = ticket.id AND replyattachmentid = 0) AS count,ticket.priorityid
            FROM `#__js_ticket_tickets` AS ticket
            JOIN `#__js_ticket_priorities` AS priority ON ticket.priorityid = priority.id
            LEFT JOIN `#__js_ticket_departments` AS department ON ticket.departmentid = department.id
            LEFT JOIN `#__js_ticket_help_topics` AS helptopic ON ticket.helptopicid = helptopic.id
            LEFT JOIN `#__js_ticket_staff` AS staff ON ticket.staffid = staff.id
            LEFT JOIN `#__js_ticket_attachments` AS attach ON ticket.id = attach.ticketid AND attach.replyattachmentid = 0
            WHERE ticket.id=" . $id;

        $query .= $inquery;

        $db->setQuery($query);
        $ticketdetails = $db->loadObjectList();
        $ticketemail = array();
        if($ticketdetails[0]->ticketviaemail == 1){
            $ticketemail = $this->getJSModel('ticketviaemail')->getTicketEmailById($ticketdetails[0]->ticketviaemail_id);
        }

        $query = "SELECT replies.*,  staff.appendsignature AS appendsignature, staff.signature AS signature,
                CONCAT(staff.firstname,' ',staff.lastname) AS staffname,staff.photo AS staffphoto,staff.id AS staffid, time.usertime
                FROM`#__js_ticket_replies` AS replies
                LEFT JOIN `#__js_ticket_staff` AS staff ON replies.staffid = staff.id
                LEFT JOIN `#__js_ticket_staff_time` AS time ON time.referenceid = replies.id AND time.referencefor = 1
                WHERE replies.ticketid = " . $id . " ORDER BY replies.created ASC";

        $db->setQuery($query);
        $replies = $db->loadObjectList();
        $attachmentmodel = $this->getJSModel('attachments');
        foreach ($replies AS $reply) {
            $reply->attachments = $attachmentmodel->getAttachmentForReply($id, $reply->id);
        }

        if($user->getIsStaff() || $user->getIsAdmin()){
            $query = "SELECT note.*,ticket.staffid AS staffid,CONCAT(staff.firstname,' ',staff.lastname) AS staffname,staff.photo AS staffphoto,staff.id AS staffid,time.usertime
                FROM `#__js_ticket_notes` AS note
                LEFT JOIN `#__js_ticket_tickets` AS ticket ON note.ticketid = ticket.id
                LEFT JOIN `#__js_ticket_staff` AS staff ON note.staffid = staff.id
                LEFT JOIN `#__js_ticket_staff_time` AS time ON time.referenceid = note.id AND time.referencefor = 2
                WHERE note.ticketid=" . $id . " ORDER BY note.created DESC ";
            $db->setQuery($query);
            $notes = $db->loadObjectList();
            $model_staff = $this->getJSModel('staff');
            if ($user->getIsAdmin()){
                $departments = $this->getJSModel('department')->getDepartments();
                $premade = $this->getJSModel('premade')->getPremade($ticketdetails[0]->departmentid);
            }else{
                $departments = $model_staff->getStaffAccessDepartments($user->getId());
                $premade = $model_staff->getStaffAccessDepartmentPremade($user->getId(),0);
            }
            $assignto = $model_staff->getStaffMembers();

            $priorities = $this->getPriorities();
            $assign_staffid = '';
            $assign_departmentid = '';
            if(isset($ticketdetails[0])) $assign_staffid = $ticketdetails[0]->staffid;
            if(isset($ticketdetails[0])) $assign_departmentid = $ticketdetails[0]->departmentid;
            $lists['departments'] = JHTML::_('select.genericList', $departments, 'departmentid', 'class="inputbox js-ticket-premade-select" ' . '', 'value', 'text', $assign_departmentid);
            $lists['staff'] = JHTML::_('select.genericList', $assignto, 'assigntostaff', 'class="inputbox js-ticket-premade-select" ' . '', 'value', 'text', $assign_staffid);
            $lists['premade'] = JHTML::_('select.genericList', $premade, '', 'class="inputbox js-ticket-premade-select" ' . 'onChange="getpremade(\'responcemsg\', this.value ,append.checked)"', 'value', 'text', '');
            $lists['priorities'] = JHTML::_('select.genericList', $priorities, 'priorityid', 'class="inputbox"', 'value', 'text', $ticketdetails[0]->priorityid);

            if(isset($ticketdetails[0]->email)){
                $query = "SELECT COUNT(id) AS id FROM `#__js_ticket_email_banlist` WHERE email = " . $db->quote($ticketdetails[0]->email);
                $db->setQuery($query);
            }
            $isemailban = $db->loadResult();
            if ($isemailban > 0)
                $isemailban = 1;
            else
                $isemailban = 2;

            $config_ticket = $this->getJSModel('config')->getConfigByFor('ticket');

            $result[1] = $notes;
            $result[3] = $lists;
            $result[4] = $isemailban;
            $result[5] = $config_ticket;
        } //end is staff

        $ufields = $this->getJSModel('userfields');
        $tickethistory = $this->getTicketHistory($id);
        $result[0] = isset($ticketdetails[0]) ? $ticketdetails[0] : '';
        $result[2] = $replies;
        $result[6] = $ticketdetails;
        $result[10] = $time_taken;
        $result[11] = $ticketemail;
        //$result[7] = $ufields->getUserFieldsForView(1, $id);
        //$result[8] = $ufields->getFieldsOrdering(1);

        //get user tickets for right widget
        if($ticketdetails[0]->uid > 0){

            //count all ticket of user
            $query = "SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE `uid` = ".$ticketdetails[0]->uid;
            $db->setQuery($query);
            $result[12] = $db->loadResult();


            //get user tickets for right widget
            $inquery = " WHERE ticket.id != " . $id . " AND ticket.uid = " . $ticketdetails[0]->uid;
            if(!$user->getIsAdmin() && $this->getJSModel('staff')->isUserStaff()){
                $allowed = $user->checkUserPermission('All Tickets');
                if($allowed != true){
                    $staffid = $user->getStaffid();
                    $inquery .= " AND (ticket.staffid = $staffid OR ticket.departmentid IN (SELECT dept.departmentid FROM `#__js_ticket_acl_user_access_departments` AS dept WHERE dept.staffid = $staffid))";
                }
            }

            $query = "SELECT ticket.id,ticket.subject,ticket.status,ticket.lock,ticket.isoverdue,priority.priority AS priority,priority.prioritycolour AS prioritycolour,department.departmentname AS departmentname
                    FROM `#__js_ticket_tickets` AS ticket
                    LEFT JOIN `#__js_ticket_priorities` AS priority ON ticket.priorityid = priority.id
                    LEFT JOIN `#__js_ticket_departments` AS department ON ticket.departmentid = department.id";
            $query .= $inquery . " LIMIT 3 ";
            $db->setQuery($query);
            $result[13] = $db->loadObjectList();
        }
        //attachment data
        $query = "SELECT published,isvisitorpublished
                    FROM `#__js_ticket_fieldsordering` WHERE field = 'attachments'";
        $db->setQuery($query);
        $result['publishedInfo'] = $db->loadObject();

        if($tickethistory) $result[9] = $tickethistory;
        return $result;
    }

    function ticketClose($ticketid ,$created,$banemailandcloseticket = false) { // cron flag is to check whether current call is cron call
        if (!is_numeric($ticketid))
            return false;
        $eventtype = JText::_('Close Ticket');
        $user = JSSupportticketCurrentUser::getInstance();
        if($user->getIsAdmin()){
            $msg1 = JText::_('Admin');
        }else{
            if($user->getIsStaff()){
                if($banemailandcloseticket == false)
                    $per = $user->checkUserPermission('Close Ticket');
                else
                    $per = $user->checkUserPermission('Ban Email And Close Ticket');
                if ($per == false)
                    return PERMISSION_ERROR;
                $msg1 = JText::_('Staff');
            }elseif(!$user->getIsGuest()){
                $email = $user->getEmail();
                $userTicket = $this->isUserTicket($ticketid, $email);
                $msg1 = JText::_('User');
                if (!$userTicket) {
                    $msg = JText::_('YOU are not allowed');
                    $message = JText::_('User') . " ( " . $user->getName() . " ) " . $msg;
                    $this->activity_log->storeActivityLog($ticketid,1,$eventtype,$message,'Error');
                    return OTHER_USER_TASK;
                }
            }else{ // in case of guest
                $msg1 = JText::_('Guest');
            }
        }

        $row = $this->getTable('tickets');
        $row->load($ticketid);
        // $row->reopened = '';
        $row->status = 4;
        $row->closed = $created;
        $row->update = $created;
        $row->isoverdue = 0;
        if (!$row->store()) {
            $this->getJSModel('systemerrors')->updateSystemErrors($row->getError());
            $this->setError($row->getError());
            $return_value = false;
        }
        if (isset($return_value) && $return_value == false) {
            $message = $row->getError();
            $this->activity_log->storeActivityLog($ticketid,1,$eventtype,$message,'Error');
            return TICKET_ACTION_ERROR;
        }
        $referenceid = $row->id;
        $msg = JText::_('Ticket is closed by');
        $message = $msg . " " . $user->getName() . " ( " . $msg1 . " ) ";
        $result = $this->activity_log->storeActivityLog($referenceid,1,$eventtype,$message,'Sucessfull');

        $this->getJSModel('email')->sendMail(1,2,$ticketid); // Mailfor,Close Ticket,Ticketid

        // on ticket close make remove credentails data and show messsage on retrive.
        $this->getJSModel('privatecredentials')->deleteCredentialsOnCloseTicket($ticketid);

        return TICKET_ACTION_OK;
    }

    function reopenTicket($ticketid, $lastreply) {
        if (!is_numeric($ticketid))
            return false;
        $eventtype = JText::_('Reopen Ticket');
        $user = JSSupportticketCurrentUser::getInstance();
        if($user->getIsAdmin()){
            $msg1 = JText::_('Admin');
        }else{
            $canreopen = $this->checkCanReopenTicket($ticketid, $lastreply);
            if ($canreopen == false) {
                $msg = JText::_('Ticket reopen time limit end');
                $message = $msg . " " . $user->getName() . " (" . $msg1 . ") ";
                $this->activity_log->storeActivityLog($ticketid,1,$eventtype,$message,'Error');
                return TIME_LIMIT_END;
            }

            if($user->getIsStaff()){
                $per = $user->checkUserPermission('Reopen Ticket');
                if ($per == false)
                    return PERMISSION_ERROR;
                $msg1 = JText::_('Staff');
            }elseif(!$user->getIsGuest()){
                $email = $user->getEmail();
                $userTicket = $this->isUserTicket($ticketid, $email);
                $msg1 = JText::_('User');
                if (!$userTicket) {
                    $msg = JText::_('You are not allowed');
                    $message = JText::_('User') . " ( " . $user->getName() . " ) " . $msg;
                    $this->activity_log->storeActivityLog($ticketid,1,$eventtype,$message,'Error');
                    return OTHER_USER_TASK;
                }
            }else{ //User Guest
                $msg1 = JText::_('Guest');
            }
        }

        $row = $this->getTable('tickets');
        $row->load($ticketid);
        $row->status = 0;
        $row->reopened = date('Y-m-d H:i:s');
        $row->update = date('Y-m-d H:i:s');
        $row->lastreply = date('Y-m-d H:i:s');
        if (!$row->store()) {
            $this->getJSModel('systemerrors')->updateSystemErrors($row->getError());
            $this->setError($row->getError());
            $return_value = false;
        }
        if (isset($return_value) && $return_value == false) {
            $message = $row->getError();
            $this->activity_log->storeActivityLog($ticketid,1,$eventtype,$message,'Error');
            return TICKET_ACTION_ERROR;
        }
        $referenceid = $row->id;
        $msg = JText::_('Ticket is reopened by');
        $message = $msg . " " . $user->getName() . " (" . $msg1 . ") ";
        $this->activity_log->storeActivityLog($referenceid,1,$eventtype,$message,'Sucessfull');

        return TICKET_ACTION_OK;
    }

    function changeTicketPriority($ticketid, $priorityid, $created) {
        if (!is_numeric($ticketid))
            return false;
        if (!is_numeric($priorityid))
            return false;
        $eventtype = JText::_('Change ticket priority');
        $user = JSSupportticketCurrentUser::getInstance();
        if($user->getIsAdmin()){
            $msg1 = JText::_('Admin');
        }else{
            if($user->getIsStaff()){
                $per = $user->checkUserPermission('Change Ticket Priority');
                if ($per == false)
                    return PERMISSION_ERROR;
                $msg1 = JText::_('Staff');
            }elseif($user->getIsGuest()){
                $email = $user->email;
                $userTicket = $this->isUserTicket($ticketid, $email);
                $msg1 = JText::_('User');
                if (!$userTicket) {
                    $msg = JText::_('You are not allowed');
                    $message = JText::_('User') . " ( " . $user->getName() . " ) " . $msg;
                    $this->activity_log->storeActivityLog($ticketid,1,$eventtype,$message,'Error');
                    return OTHER_USER_TASK;
                }
            }else return OTHER_USER_TASK;
        }
        $row = $this->getTable('tickets');
        $row->load($ticketid);
        $row->priorityid = $priorityid;
        $row->update = $created;
        if (!$row->store()) {
            $this->getJSModel('systemerrors')->updateSystemErrors($row->getError());
            $this->setError($row->getError());
            $return_value = false;
        }
        if (isset($return_value) && $return_value == false) {
            $message = $row->getError();
            $result = $this->activity_log->storeActivityLog($ticketid,1,$eventtype,$message,'Error');
            return PRIORITY_CHANGE_ERROR;
        }
        $referenceid = $row->id;
        $msg = JText::_('Ticket priority is changed by');
        $message = $msg . " " . $user->getName() . " (" . $msg1 . ") ";
        $result = $this->activity_log->storeActivityLog($referenceid,1,$eventtype,$message,'Sucessfull');
        $this->getJSModel('email')->sendMail(1,11,$ticketid); // Mailfor,priority change,Ticketid
        return PRIORITY_CHANGED;
    }

    function ticketMarkInprogress($ticketid,$created) {
        if (!is_numeric($ticketid))
            return false;
        $eventtype = JText::_('Mark in Progress');
        $user = JSSupportticketCurrentUser::getInstance();
        if($user->getIsAdmin()){
            $msg1 = JText::_('Admin');
        }else{
            $per = $user->checkUserPermission('Mark In Progress');
            if ($per == false)
                return PERMISSION_ERROR;
            $msg1 = JText::_('Staff');
        }

        $row = $this->getTable('tickets');
        $row->load($ticketid);
        $row->status = 2;
        $row->update = $created;
        if (!$row->store()) {
            $this->setError($row->getError());
            $this->getJSModel('systemerrors')->updateSystemErrors($row->getError());
            $return_value = false;
        }
        if (isset($return_value) && $return_value == false) {
            $message = $row->getError();
            $this->activity_log->storeActivityLog($ticketid,1,$eventtype,$message,'Error');
            return TICKET_ACTION_ERROR;
        }

        $referenceid = $row->id;
        $msg = JText::_('Ticket is marked as in progress by');
        $message = $msg . " " . $user->getName() . " (" . $msg1 . ") ";
        $this->activity_log->storeActivityLog($referenceid,1,$eventtype,$message,'Sucessfull');
        $this->getJSModel('email')->sendMail(1,9,$ticketid); // Mailfor,inprogress tickert,Ticketid
        return TICKET_ACTION_OK;
    }

    function markOverDueTicket($ticketid,$created,$cron_flag = 0) {
        if (!is_numeric($ticketid))
            return false;
        $eventtype = JText::_('Mark overdue');
        $user = JSSupportticketCurrentUser::getInstance();
        $msg1 = "System";
        if($cron_flag == 0){
            if($user->getIsAdmin()){
                $msg1 = JText::_('Admin');
            }else{
                $per = $user->checkUserPermission('Mark Overdue');
                if ($per == false)
                    return PERMISSION_ERROR;
                $msg1 = JText::_('Staff');
            }
        }

        $row = $this->getTable('tickets');
        $row->load($ticketid);
        $row->isoverdue = 1;
        $row->update = $created;
        if (!$row->store()) {
            $this->getJSModel('systemerrors')->updateSystemErrors($row->getError());
            $this->setError($row->getError());
            $return_value = false;
        }
        if (isset($return_value) && $return_value == false) {
            $message = $row->getError();
            $this->activity_log->storeActivityLog($ticketid,1,$eventtype,$message,'Error');
            return TICKET_ACTION_ERROR;
        }
        $referenceid = $row->id;
        $msg = JText::_('Ticket is marked as overdue by');
        $message = $msg . " " . $user->getName() . " ( " . $msg1 . " ) ";
        $this->activity_log->storeActivityLog($referenceid,1,$eventtype,$message,'Sucessfull');
        $this->getJSModel('email')->sendMail(1,8,$ticketid); // Mailfor,over due Ticket,Ticketid
        return TICKET_ACTION_OK;
    }

    function unMarkOverDueTicket($ticketid,$created) {
        if (!is_numeric($ticketid))
            return false;
        $eventtype = JText::_('Mark overdue');
        $user = JSSupportticketCurrentUser::getInstance();
        if($user->getIsAdmin()){
            $msg1 = JText::_('Admin');
        }else{
            $per = $user->checkUserPermission('Mark Overdue');
            if ($per == false)
                return PERMISSION_ERROR;
            $msg1 = JText::_('Staff');
        }

        $row = $this->getTable('tickets');
        $row->load($ticketid);
        $row->isoverdue = 0;
        $row->update = $created;
        if (!$row->store()) {
            $this->getJSModel('systemerrors')->updateSystemErrors($row->getError());
            $this->setError($row->getError());
            $return_value = false;
        }
        if (isset($return_value) && $return_value == false) {
            $message = $row->getError();
            $this->activity_log->storeActivityLog($ticketid,1,$eventtype,$message,'Error');
            return TICKET_ACTION_ERROR;
        }
        $referenceid = $row->id;
        $msg = JText::_('Ticket is unmarked as overdue by');
        $message = $msg . " " . $user->getName() . " ( " . $msg1 . " ) ";
        $this->activity_log->storeActivityLog($referenceid,1,$eventtype,$message,'Sucessfull');
        //$this->getJSModel('email')->sendMail(1,8,$ticketid); // Mailfor,over due Ticket,Ticketid
        return TICKET_ACTION_OK;
    }

    function lockTicket($id) {
        if (!is_numeric($id))
            return false;
        $eventtype = JText::_('Lock ticket');
        $user = JSSupportticketCurrentUser::getInstance();
        if($user->getIsAdmin()){
            $msg1 = JText::_('Admin');
        }else{
            $per = $user->checkUserPermission('Lock Ticket');
            if ($per == false)
                return PERMISSION_ERROR;
            $msg1 = JText::_('Staff');
        }

        $db = $this->getDbo();
        $query = "UPDATE `#__js_ticket_tickets` AS ticket set ticket.lock = 1 WHERE id =" . $id;
        $db->setQuery($query);
        if (!$db->execute()) {
            $message = $row->getError();
            $this->activity_log->storeActivityLog($id,1,$eventtype,$message,'Error');
            return TICKET_ACTION_ERROR;
        }
        $msg = JText::_('Ticket is locked by');
        $message = $msg . " " . $user->getName() . " ( " . $msg1 . " ) ";
        $this->activity_log->storeActivityLog($id,1,$eventtype,$message,'Sucessfull');
        $this->getJSModel('email')->sendMail(1,6,$id); // Mailfor,lock,Ticketid

        return TICKET_ACTION_OK;
    }

    function unlockTicket($id) {
        if (!is_numeric($id))
            return false;
        $eventtype = JText::_('Unlock ticket');
        $user = JSSupportticketCurrentUser::getInstance();
        if($user->getIsAdmin()){
            $msg1 = JText::_('Admin');
        }else{
            $per = $user->checkUserPermission('Lock Ticket');
            if ($per == false){
                return PERMISSION_ERROR;
            }
            $msg1 = JText::_('Staff');
        }
        $db = $this->getDbo();
        $query = "UPDATE `#__js_ticket_tickets` AS ticket set ticket.lock = 0 WHERE ticket.id =" . $id;
        $db->setQuery($query);
        if (!$db->execute()) {
            $message = $row->getError();
            $this->activity_log->storeActivityLog($id,1,$eventtype,$message,'Error');
            return TICKET_ACTION_ERROR;
        }
        $msg = JText::_('Ticket is unlocked by');
        $message = $msg . " " . $user->getName() . " ( " . $msg1 . " ) ";
        $this->activity_log->storeActivityLog($id,1,$eventtype,$message,'Sucessfull');
        $this->getJSModel('email')->sendMail(1,7,$id); // Mailfor,unlock,Ticketid
        return TICKET_ACTION_OK;
    }

    function storeTicketInternalNote($ticketid, $staffid, $internalnote, $created, $internalnotetitle) {
        if (!is_numeric($ticketid))
            return false;
        if(!JFactory::getApplication()->isClient('administrator')){
            if (!is_numeric($staffid))
                return false;
        }
        $user = JSSupportticketCurrentUser::getInstance();
        $row = $this->getTable('notes');
        $data['ticketid'] = $ticketid;
        //$data['staffid'] = $staffid;
        $data['staffid'] = $user->getStaffId();
        $data['title'] = $internalnotetitle;
        $data['note'] = $internalnote;
        $data['status'] = 1;
        $data['created'] = $created;
        $data['from'] = 0;
        if (!$row->bind($data)) {
            $this->setError($row->getError());
            return false;
        }
        if (!$row->check()) {
            $this->setError($row->getError());
            return false;
        }
        if (!$row->store()) {
            $this->getJSModel('systemerrors')->updateSystemErrors($row->getError());
            $this->setError($row->getError());
            return false;
        }
        return true;
    }

    function storeTicket_InternalNote($ticketid,$title, $note, $created, $data2) {//when ticket open for reply
        if (!is_numeric($ticketid))
            return false;
        $name ="";
        $staffid=0;
        $eventtype = JText::_('Post Internal Note');
        $user = JSSupportticketCurrentUser::getInstance();
        if($user->getIsAdmin()){
            $msg1 = JText::_('Admin');
            $name = $user->getName();
        }else{
            $per = $user->checkUserPermission('Post Internal Note');
            if ($per == false)
                return PERMISSION_ERROR;
            $msg1 = JText::_('Staff');
            $staffid=$data2['staffid'];

        }

        $row = $this->getTable('notes');
        $data['ticketid'] = $ticketid;
        $data['staffid'] = $staffid;
        $data['from'] = $name;
        $data['title'] = $title;
        $data['note'] = $note;
        $data['status'] = 1;
        $data['created'] = $created;

        $return_value = true;
        if (!$row->bind($data)) {
            $this->setError($row->getError());
            $return_value = false;
        }
        if (!$row->check()) {
            $this->setError($row->getError());
            $return_value = false;
        }
        if (!$row->store()) {
            $this->getJSModel('systemerrors')->updateSystemErrors($row->getError());
            $this->setError($row->getError());
            $return_value = false;
        }
        if(!$user->getIsAdmin() && $return_value != false){// store time
            $data2['ticketid'] = $ticketid;
            $data2['timer_edit_desc'] = JFactory::getApplication()->input->get('timer_edit_desc', '', 'raw');
            $this->getJSModel('staff')->storeTimeTaken($data2,$row->id,2);
        }
        $config = $this->getJSModel('config')->getConfigByFor('default');
        $filesize = $config['filesize'];
        $model_note = $this->getJSModel('note');
        if ($_FILES['noteattachment']['name'] != '') {
            if ($_FILES['noteattachment']['size'] > 0) {
                $uploadfilesize = $_FILES['noteattachment']['size'];
                $uploadfilesize = $uploadfilesize / 1024; //kb
                if ($uploadfilesize > $filesize) { // filename
                    return FILE_SIZE_ERROR;
                }
                $file_name = str_replace(' ', '_', $_FILES['noteattachment']['name']);
                $result = $model_note->checkExtension($file_name);
				if ($result == 'N') { // filename
					return FILE_EXTENTION_ERROR;
				}
				$res = $model_note->uploadAttchment($ticketid);
				if ($res == true) {
					$row->filename = $file_name;
					$row->filesize = $uploadfilesize;
					$row->store();
				}
			}
		}

        //for ticket close
        if (isset($data2['internalnotestatus']) && $data2['internalnotestatus'] == 4) {
            $result = $this->updateTicketStatus($ticketid, $data2['internalnotestatus'], $data2['created']);
            if ($result == false)
                return POST_ERROR;
        }
        if (isset($return_value) && $return_value == false) {
            $message = $row->getError();
            $this->activity_log->storeActivityLog($ticketid,1,$eventtype,$message,'Error');
            return POST_ERROR;
        }

        $msg = JText::_('Internal note is posted by');
        $message = $msg . " " . $user->getName() . " ( " . $msg1 . " ) ";
        $this->activity_log->storeActivityLog($ticketid,1,$eventtype,$message,'Sucessfull');

        return POSTED;
    }

    function storeTicketReplies($ticketid, $message, $created, $data2) {
        if (!is_numeric($ticketid))
            return false;

        //validate reply for break down
        $ticketrandomid   = $data2['ticketid'];
        $hash = $data2['hash'];
        $db = $this->getDBo();
        $query = "SELECT id FROM `#__js_ticket_tickets` WHERE ticketid='$ticketrandomid' AND IF(hash is NULL,true,hash='$hash')";
        $db->setQuery($query);
        $res = $db->loadResult();
        if($res != $data2['id']){
            return false;
        }//end

        /*$ticketviaemailstaffid = 0;
        // set in ticket via email
        if(isset($data2['staffid'])){
            $ticketviaemailstaffid = $data2['staffid'];
            unset($data2['staffid']);
        }*/
        if(isset($data['ticketviaemail']) && $data['ticketviaemail'] == 1){
            $eventtype = JText::_('Reply ticket via email');
        }else{
            $eventtype = JText::_('Reply ticket');
        }

        $user = JSSupportticketCurrentUser::getInstance();
        $uname = $user->getName();

        if($user->getIsStaff()){
            $per = $user->checkUserPermission('Reply Ticket');
            if ($per == false)
                return PERMISSION_ERROR;
        }

        if(isset($data2['appendsignature']) && $data2['appendsignature'] != 3){
            $id = $data2['id'];
            $appendSignature = $data2['appendsignature'];
            if ($appendSignature == 1) {
                $signature = $this->getJSModel('staff')->getStaffMemberSignature($user->getId());
            } elseif ($appendSignature == 2) {
                $signature = $this->getJSModel('department')->getDepartmentSignature($id);
            }
            $signature = str_replace(Chr(13), '<br>', $signature);
            $message .= '<br/><br/>' . $signature;
        }

        if($user->getIsAdmin()){
            $msg1 = JText::_('Admin');
            $status = 3;
        }elseif($user->getIsStaff()){
            $msg1 = JText::_('Staff');
            $status = 3;
        }else{
            $status = 1;
            $msg1 = JText::_('User');
            if($user->getIsGuest()){
                $msg1 = JText::_('Guest');
                $uname = $this->getReplierName($ticketid);
            }
        }
        if(isset($data2['ticketviaemail']) && $data2['ticketviaemail'] == 1){ // overwrite if ticket via email
            $status = $data2['status'];
            // check this ticket is not assign to any one
            if( $this->isTicketAssigned($data2['ticketid']) == false){
                // if not assigned then assign to me
                $data2['assigntomyself'] = 1;
            }

        }
        $config = $this->getJSModel('config')->getConfigByFor('default');

        if(!$user->getIsStaff() && !$user->getIsAdmin() && $config['reply_to_closed_ticket'] != 1){ // to hanlde closed ticket reply confiration for user
            $row = $this->getTable('tickets');
            $row->load($ticketid);
            $closed = $row->status;
            if($closed == 4){
                $this->getJSModel('email')->sendMail(1,14,$ticketid); // sned to email to user when he tries to reply to a colsed ticket (email is sent to handle ticket via email case)
                return POST_ERROR;
            }
        }

        $res = $this->updateTicketStatus($ticketid,$status);
        if(!$res) return POST_ERROR;

        $row = $this->getTable('replies');
        $data['ticketid'] = $ticketid;
        $data['staffid'] = isset($data2['staffid']) ? $data2['staffid'] : '';
        $data['name'] = $uname;
        $data['message'] = $message;
        $data['status'] = 1;
        $data['created'] = $created;
        //utf auto switch
        if($this->getJSModel('config')->getConfigurationByName('read_utf_ticket_via_email') == 1){
            $data['name'] = iconv_mime_decode($data['name'],0,"UTF-8");
        }

        $return_value = true;
        if (!$row->bind($data)) {
            $this->setError($row->getError());
            $return_value = false;
        }
        if (!$row->check()) {
            $this->setError($row->getError());
            return MESSAGE_EMPTY;
        }
        if (!$row->store()) {
            $this->getJSModel('systemerrors')->updateSystemErrors($row->getError());
            $this->setError($row->getError());
            $return_value = false;
        }
        if (isset($return_value) && $return_value == false) {
            $message = $row->getError();
            $this->activity_log->storeActivityLog($ticketid,1,$eventtype,$message,'Error');
            return POST_ERROR;
        }
        $replyattachmentid = $row->id;
         if(!$user->getIsAdmin() && $return_value != false){// store time
            $data2['ticketid'] = $ticketid;
            $data2['timer_edit_desc'] = JFactory::getApplication()->input->get('timer_edit_desc', '', 'raw');
            $this->getJSModel('staff')->storeTimeTaken($data2,$row->id,1);
        }
        $isanswered = 0;
        if($status == 3)
            $isanswered = 1;
        $res = $this->updateIsAnswered($ticketid,$isanswered);
        if(!$res) return POST_ERROR;

        $res = $this->updateTicketLastReply($ticketid,$created);
        if(!$res) return POST_ERROR;

        if (isset($data2['assigntomyself'])){
            $res = $this->updateTicketAssignToMyself($ticketid, $user->getStaffid());
            if(!$res) return POST_ERROR;
        }

        $total = 0;
        if(isset($_FILES['filename']))
            $total = count($_FILES['filename']['name']);
        if ($total > 0) {
            $config = $this->getJSModel('config')->getConfigByFor('default');
            $filesize = $config['filesize'];
            $model_attachment = $this->getJSModel('attachments');
            for ($i = 0; $i < $total; $i++) {
                if ($_FILES['filename']['name'][$i] != '') {
                    if ($_FILES['filename']['size'][$i] > 0) {
                        $uploadfilesize = $_FILES['filename']['size'][$i];
                        $uploadfilesize = $uploadfilesize / 1024; //kb
                        if ($uploadfilesize > $filesize) { // filename
                            return FILE_SIZE_ERROR;
                        }
                        $file_name = str_replace(' ', '_', $_FILES['filename']['name'][$i]);
                        $result = $model_attachment->checkExtension($file_name);
                        if ($result == 'N') { // filename
                            return FILE_EXTENTION_ERROR;
                        }
                        $res = $model_attachment->uploadAttchments($i, $ticketid, 1, 0, 'ticket');
                        if ($res == true) {
                            $result = $this->storeTicketAttachment($ticketid, $uploadfilesize, $file_name, $replyattachmentid);
                        }
                    }
                }
            }
        }
        //for ticket close
        if (isset($data2['replystatus']) && $data2['replystatus'] == 4) {
            $result = $this->updateTicketStatus($ticketid, $data2['replystatus'], $data2['created']);
            if ($result == false)
                return POST_ERROR;
            $referenceid = $ticketid;
            $msg = JText::_('Ticket is closed by');
            $message = $msg . " " . $user->getName() . " ( " . $msg1 . " ) ";
            $this->activity_log->storeActivityLog($referenceid, 1, $eventtype, $message, 'Sucessfull');
        }

        $referenceid = $ticketid;
        if(isset($data['ticketviaemail']) && $data['ticketviaemail'] == 1){
            $msg = JText::_('Ticket view email is replied by');
            $msg1 = $this->getJSModel('ticketviaemail')->getTicketEmailById($data['ticketviaemail_id']);
            $message = $msg . " ( " . $msg1 . " ) ";
        }else{
            $msg = JText::_('Ticket is replied by');
            $message = $msg . " " . $user->getName() . " ( " . $msg1 . " ) ";
        }


        $this->activity_log->storeActivityLog($referenceid, 1, $eventtype, $message, 'Sucessfull');

        $replyfromadminstaff = 0;
        if($user->getIsAdmin() || $user->getIsStaff()) $replyfromadminstaff = 1;

        if(isset($data2['ticketviaemail']) && $data2['ticketviaemail'] == 1){ // overwrite if ticket via email
            if($data2['status'] == 3) $replyfromadminstaff = 1;
        }
        if($replyfromadminstaff == 1){
            $this->getJSModel('email')->sendMail(1,4,$ticketid); // Mailfor,reply,Ticketid [admin/staffmember]
        }else{
            $this->getJSModel('email')->sendMail(1,5,$ticketid); // Mailfor,reply,Ticketid [user reply]

        }
        return POSTED;
    }

    function storeUserReplies() {
        $data = JFactory::getApplication()->input->post->getArray();
        $ticketid = $data['ticketid'];

        // update status that reply from the USER
        $row = $this->getTable('tickets');
        $row->load($ticketid);
        $row->status = 1;

        if($data['isreopen'] == 1) {
            $result = $this->reopenTicket($data['ticketid'], $data['lastreply']);
            if($result == TICKET_ACTION_ERROR)
                return SENT_ERROR;
            elseif($result != TICKET_ACTION_OK)
                return $result;
        }

        if (!$row->store()) {
            $this->getJSModel('systemerrors')->updateSystemErrors($row->getError());
            $this->setError($row->getError());
            return SENT_ERROR;
        }

        $user = JSSupportticketCurrentUser::getInstance();
        $eventtype = JText::_('Reply ticket');
        $row = $this->getTable('replies');
        $name = $this->getReplierName($ticketid);
        $data['name'] = $name;
        $data['staffid'] = 0; //Front end reply clue
        $data['status'] = 1; //Front end reply clue
        $messagedata = JFactory::getApplication()->input->get('message', '', 'raw');
        if ($messagedata == "") { // ticket is autoclose and close and reopen to handel editor show correctly change id message to messages
            $messagedata = JFactory::getApplication()->input->get('messages', '', 'raw');
        }
        $data['message'] = $messagedata;

        if (!$row->bind($data)) {
            $this->setError($row->getError());
            $return_value = false;
        }
        if (!$row->check()) {
            $this->setError($row->getError());
            $return_value = false;
        }
        if (!$row->store()) {
            $this->getJSModel('systemerrors')->updateSystemErrors($row->getError());
            $this->setError($row->getError());
            $return_value = false;
        }
        if (isset($return_value) && $return_value == false) {
            $message = $row->getError();
            $result = $this->activity_log->storeActivityLog($ticketid, 1,$eventtype,$message,'Error');
            return SENT_ERROR;
        }
        $msg = JText::_('Ticket is replied by');
        $msg1 = JText::_('User');
        $message = $msg . " " . $user->getName() . " (" . $msg1 . ") ";
        $result = $this->activity_log->storeActivityLog($ticketid, 1,$eventtype,$message,'Sucessfull');


        $config = $this->getJSModel('config')->getConfigByFor('default');
        $ticketid = $row->ticketid;
        $replyattachmentid = $row->id;
        $filesize = $config['filesize'];
        $total = count((isset($_FILES['filename']['name'])) ? $_FILES['filename']['name'] : 0);
        if ($total > 0) {
            $model_attachment = $this->getJSModel('attachments');
            for ($i = 0; $i < $total; $i++) {
                if ($_FILES['filename']['name'][$i] != '') {
                    if ($_FILES['filename']['size'][$i] > 0) {
                        $uploadfilesize = $_FILES['filename']['size'][$i];
                        $uploadfilesize = $uploadfilesize / 1024; //kb
                        if ($uploadfilesize > $filesize) { // filename
                            return FILE_SIZE_ERROR;
                        }
                        $file_name = str_replace(' ', '_', $_FILES['filename']['name'][$i]);
                        $result = $model_attachment->checkExtension($file_name);
                        if ($result == 'N') { // filename
                            return FILE_EXTENTION_ERROR;
                        }
                        $res = $model_attachment->uploadAttchments($i, $ticketid, 1, 0, 'ticket');
                        if ($res == true) {
                            $result = $this->storeReplyAttachment($ticketid, $replyattachmentid, $uploadfilesize, $file_name);
                        }
                    }
                }
            }
        }

        $this->getJSModel('email')->sendMail(1,5,$ticketid); // Mailfor,reply,Ticketid [ticket owner reply]
        //die('reply owner');
        return SENT;
    }

    function ticketDepartmentTransfer($ticketid,$departmentid, $note, $created, $data2) {
        if (!is_numeric($ticketid))
            return false;
        $eventtype = JText::_('Ticket department transfer');
        $user = JSSupportticketCurrentUser::getInstance();
        if($user->getIsAdmin()){
            $msg1 = JText::_('Admin');
        }else{
            $per = $user->checkUserPermission('Ticket Department Transfer');
            if ($per == false)
                return PERMISSION_ERROR;
            $msg1 = JText::_('Staff');
        }
		
		if($created == "") $created = date("Y-m-d H:i:s");
        $row = $this->getTable('tickets');
        $row->load($ticketid);
        $row->departmentid = $departmentid;
        $row->update = $created;
        if (!$row->store()) {
            $this->getJSModel('systemerrors')->updateSystemErrors($row->getError());
            $this->setError($row->getError());
            $return_value = false;
        }
        if (isset($return_value) && $return_value == false) {
            $message = $row->getError();
            $this->activity_log->storeActivityLog($ticketid,1,$eventtype,$message,'Error');
            return TICKET_TRANSFER_ERROR;
        }

        //for internal note
        $result = $this->storeTicketInternalNote($ticketid, $data2['staffid'], $note, $created, '');
        if ($result == false)
            return TICKET_TRANSFER_ERROR;

        $referenceid = $row->id;
        $msg = JText::_('Department is transfered by');
        $message = $msg . " " . $user->getName() . " ( " . $msg1 . " ) ";
        $this->activity_log->storeActivityLog($referenceid,1,$eventtype,$message,'Sucessfull');

        $this->getJSModel('email')->sendMail(1,12,$ticketid); // Mailfor,department transwer,Ticketid
        return TICKET_TRANSFERED;
    }

    function ticketStaffTransfer($ticketid,$staffid, $note, $created, $data2) {
        if (!is_numeric($ticketid))
            return false;
        $eventtype = JText::_('Assign ticket to staff');
        $user = JSSupportticketCurrentUser::getInstance();
        if($user->getIsAdmin()){
            $msg1 = JText::_('Admin');
        }else{
            $per = $user->checkUserPermission('Assign Ticket To Staff');
            if ($per == false)
                return PERMISSION_ERROR;
            $msg1 = JText::_('Staff');
        }
		if($created == "") $created = date("Y-m-d H:i:s");
        $row = $this->getTable('tickets');
        $row->load($ticketid);
        $row->staffid = $staffid;
        $row->update = $created;
        if (!$row->store()) {
            $this->getJSModel('systemerrors')->updateSystemErrors($row->getError());
            $this->setError($row->getError());
            $return_value = false;
        }
        if (isset($return_value) && $return_value == false) {
            $message = $row->getError();
            $this->activity_log->storeActivityLog($ticketid,1,$eventtype,$message,'Error');
            return TICKET_TRANSFER_ERROR;
        }

        //for internal note
        if($note){
            $result = $this->storeTicketInternalNote($ticketid, $staffid, $note, $created, '');
            if ($result == false)
                return TICKET_TRANSFER_ERROR;
        }
        $referenceid = $row->id;
        $msg = JText::_('Ticket is assigned to staff by');
        $message = $msg . " " . $user->getName() . " ( " . $msg1 . " ) ";
        $this->activity_log->storeActivityLog($referenceid,1,$eventtype,$message,'Sucessfull');

        $this->getJSModel('email')->sendMail(1,13,$ticketid); // Mailfor,staff transwer,Ticketid
        return TICKET_TRANSFERED;
    }

    function checkCanReopenTicket($ticketid, $lastreply) {
        if (!is_numeric($ticketid))
            return false;
        $config_ticket = $this->getJSModel('config')->getConfigByFor('ticket');
        $days = $config_ticket['ticket_reopen_within_days'];
        if (!$lastreply)
            $lastreply = date('Y-m-d H:i:s');
        $date = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s", strtotime($lastreply)) . " +" . $days . " day"));
        if ($date < date('Y-m-d H:i:s'))
            return false;
        else
            return true;
    }

    function banEmailAndCloseTicket($ticketid, $created, $email) {
        if (!is_numeric($ticketid))
            return false;
        $user = JSSupportticketCurrentUser::getInstance();
        if($user->getIsAdmin()){
            $msg1 = JText::_('Admin');
        }else{
            $per = $user->checkUserPermission('Ban Email And Close Ticket');
            if ($per == false){
                $msg = JSSupportticketMessage::getMessage(PERMISSION_ERROR,'CLOSE');
                return $msg;
            }
        }
		
		if($created == "") $created = date("Y-m-d H:i:s");
        $result = $this->getJSModel('emailbanlist')->banEmailTicket($email,$created, $ticketid, 1);
        $msg1 = JSSupportticketMessage::getMessage($result,'BAN_EMAIL');
        $result = $this->ticketClose($ticketid, $created,true);
        $msg2 = JSSupportticketMessage::getMessage($result,'CLOSE');

        if($msg1 == $msg2){
            $msg = $msg1;
        }else{
            $msg = $msg1.'<br/>'.$msg2;
        }

        $this->getJSModel('email')->sendMail(2,1,$ticketid,"js_ticket_tickets"); // Mailfor,banemail and closeticket ,Ticketid
        $this->getJSModel('email')->sendMail(1,2,$ticketid); // Mailfor,banemail and closeticket ,Ticketid
        return $msg;
    }

    function storeTicketIssueSummary($ticketid, $uid, $name, $issuesummary, $created) {
        if (!is_numeric($ticketid))
            return false;
        if (!is_numeric($uid))
            return false;
		
		if($created == "") $created = date("Y-m-d H:i:s");
        $row = $this->getTable('replies');
        $data['ticketid'] = $ticketid;
        $data['staffid'] = $uid;
        $data['name'] = $name;
        $data['message'] = $issuesummary;
        $data['status'] = 1;
        $data['created'] = $created;

        if (!$row->bind($data)) {
            $this->setError($row->getError());
            return false;
        }
        if (!$row->check()) {
            $this->setError($row->getError());
            return false;
        }
        if (!$row->store()) {
            $this->getJSModel('systemerrors')->updateSystemErrors($row->getError());
            $this->setError($row->getError());
            return false;
        }
        return true;
    }

    function storeTicketAttachment($ticketid, $filesize, $filename, $replyattachmentid = 0) {
        if (!is_numeric($ticketid))
            return false;
        $row = $this->getTable('attachments');
        $data['ticketid'] = $ticketid;
        $data['replyattachmentid'] = $replyattachmentid; // this should set to zero when new ticket created
        $data['filename'] = $filename;
        $data['filesize'] = $filesize;
        $data['created'] = $curdate = date('Y-m-d H:i:s');

        if (!$row->bind($data)) {
            $this->setError($row->getError());
            return false;
        }
        if (!$row->check()) {
            $this->setError($row->getError());
            return false;
        }
        if (!$row->store()) {
            $this->getJSModel('systemerrors')->updateSystemErrors($row->getError());
            $this->setError($row->getError());
            return false;
        }
        return true;
    }

    function getReplierName($ticketid) {
        if (!is_numeric($ticketid))
            return false;
        $db = $this->getDbo();
        $query = "SELECT ticket.name From `#__js_ticket_tickets` AS ticket WHERE ticket.id = " . $ticketid;
        $db->setQuery($query);
        $name = $db->loadResult();
        return $name;
    }

    function storeReplyAttachment($ticketid, $replyattachmentid, $filesize, $filename) {
        if (!is_numeric($ticketid))
            return false;
        if (!is_numeric($replyattachmentid))
            return false;
        $row = $this->getTable('attachments');
        $data['ticketid'] = $ticketid;
        $data['replyattachmentid'] = $replyattachmentid;
        $data['filename'] = $filename;
        $data['filesize'] = $filesize;
        $data['created'] = $curdate = date('Y-m-d H:i:s');

        if (!$row->bind($data)) {
            $this->setError($row->getError());
            return false;
        }
        if (!$row->check()) {
            $this->setError($row->getError());
            return false;
        }
        if (!$row->store()) {
            $this->getJSModel('systemerrors')->updateSystemErrors($row->getError());
            $this->setError($row->getError());
            echo $row->getError();
            return false;
        }
        return true;
    }

    function checkForNewMessageSetting($id) {
        if (!is_numeric($id))
            return false;
        $db = JFactory::getDBO();
        $query = "SELECT dep.messageautoresponce
                    FROM `#__js_ticket_tickets` AS ticket
                    JOIN `#__js_ticket_departments` AS dep ON dep.id = ticket.departmentid
                    WHERE ticket.id = " . $id;
        $db->setQuery($query);
        $depsetting = $db->loadResult();
        return $depsetting;
    }

    function getAttachmentByTicketId($id){
        if(!is_numeric($id)) return false;
        $db = JFactory::getDBO();
        $query = "SELECT attachment.filename , ticket.attachmentdir
                    FROM `#__js_ticket_attachments` AS attachment
                    JOIN `#__js_ticket_tickets` AS ticket ON ticket.id = attachment.ticketid AND ticket.id =".$id. " AND attachment.replyattachmentid = 0 ";
        $db->setQuery($query);
        $attachments = $db->loadObjectList();
        return $attachments;
    }

    function getTicketIdForEmail($id) {
        if (!is_numeric($id))
            return false;
        $db = $this->getDbo();
        $query = "Select ticketid,email from `#__js_ticket_tickets` where id = " . $id;
        $db->setQuery($query);
        $ticket = $db->loadObject();
        return $ticket;
    }

    function delete_Ticket($id) {
        if (!is_numeric($id))
            return false;
        $user = JSSupportticketCurrentUser::getInstance();
        $eventtype = JText::_('Delete ticket');
        if($user->getIsAdmin()){
            $msg1 = JText::_('Admin');
        }else{
            $per = $user->checkUserPermission('Delete Ticket');
            if ($per == false)
                return PERMISSION_ERROR;
            $msg1 = JText::_('Staff');
        }

        $row = $this->getTable('tickets');
        //for email get ticketid first
        $ticket = $this->getTicketIdForEmail($id);

        if ($this->ticketCanDelete($id) == true) {
            if (!$row->delete($id)) {
                $message = $row->getError();
                $this->activity_log->storeActivityLog($id,1,$eventtype,$message,'Error');
                return TICKET_ACTION_ERROR;
            }
            //for email to sure ticket is deleted
            $this->getJSModel('email')->sendMail(1,3,$id); // Mailfor,Delete Ticket,Ticketid
        }
        return TICKET_ACTION_OK;
    }

    function getTicketAttachmentDir($id){
        if(!is_numeric($id))
            return false;
        $db = $this->getDBO();
        $query = "SELECT attachmentdir FROM `#__js_ticket_tickets` WHERE id = $id";
        $db->setQuery($query);
        $result = $db->loadResult();
        return $result;
    }

    function enforcedeleteTicket() {
        $id = JFactory::getApplication()->input->get('cid');
        if (!is_numeric($id))
            return false;
        $db = $this->getDBO();

        $dir = $this->getTicketAttachmentDir($id);
        $query = "DELETE ticket,reply,attach,notes
                        FROM `#__js_ticket_tickets` AS ticket
                        LEFT JOIN `#__js_ticket_replies` AS reply ON reply.ticketid = ticket.id
                        LEFT JOIN `#__js_ticket_attachments` AS attach ON attach.ticketid = ticket.id
                        LEFT JOIN `#__js_ticket_notes` AS notes ON notes.ticketid = ticket.id
                        WHERE ticket.id = " . $id;
        $db->setQuery($query);
        if (!$db->execute()) {
            return DELETE_ERROR;
        } else {
            $this->getJSModel('attachments')->removeTicketAttachments( $dir );
            return DELETED;
        }
    }

    function deleteTicket() {
        $id = JFactory::getApplication()->input->get('cid');
        if (!is_numeric($id))
            return false;
        $dir = $this->getTicketAttachmentDir($id);
        if($this->canDeleteTicket($id)){
            $db = $this->getDBO();
            $query = "DELETE ticket,attach
                            FROM `#__js_ticket_tickets` AS ticket
                            LEFT JOIN `#__js_ticket_attachments` AS attach ON attach.ticketid = ticket.id
                            WHERE ticket.id = " . $id;
            $db->setQuery($query);
            if (!$db->execute()) {
                return DELETE_ERROR;
            } else {
                $this->getJSModel('attachments')->removeTicketAttachments( $dir );
                return DELETED;
            }
        }else{
            return IN_USE;
        }
    }

    function canDeleteTicket($id){
        if(!is_numeric($id)) return false;
        $db = JFactory::getDbo();
        $query = "SELECT COUNT(reply.id) FROM `#__js_ticket_replies` AS reply WHERE reply.ticketid = $id";
        $db->setQuery($query);
        $result = $db->loadResult();
        if($result == 0)
            return true;
        else
            return false;
    }

    function getEmailAndTicketIdById($id) {
        if(!is_numeric($id)) return false;
        $db = $this->getDBO();
        $query = "SELECT ticketid,email,subject,staffid FROM `#__js_ticket_tickets` WHERE id =" . $db->quote($id);
        $db->setQuery($query);
        $result = $db->loadObject();
        return $result;
    }

    function checkEmailAndTicketID($email, $ticketid) {
        $db = $this->getDBO();
        $query = "SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE email =" . $db->quote($email) . " AND ticketid =" . $db->quote($ticketid);
        $user = JFactory::getUser();
		if(!$user->guest){
			$query .= " AND uid = ".$user->id;
		}
        $db->setQuery($query);
        $result = $db->loadResult();
        return $result;
    }

    function getIdFromTrackingId($ticketid) {
        $db = $this->getDBO();
        $query = "SELECT id FROM `#__js_ticket_tickets` WHERE ticketid =" . $db->quote($ticketid);
        $db->setQuery($query);
        $result = $db->loadResult();
        return $result;
    }

    function isUserTicket($ticketid, $email) {

        if (!is_numeric($ticketid))
            return false;
        $db = $this->getDBO();
        $query = "SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE email =" . $db->quote($email) . " AND id=" . $ticketid;
        $db->setQuery($query);
        $result = $db->loadResult();
        if ($result > 0)
            return true;
        return false;
    }

    function checkCanAddTicket($email) {
        if(empty($email)) return false;
        $db = $this->getDbo();
        $user = JFactory::getUser();

        $config_ticket = $this->getJSModel('config')->getConfigByFor('default');
        $maxticketinterval = $config_ticket['maximum_ticket_interval_time'];
        switch($maxticketinterval){
            case "1": // maximum ticket in a day
                $checkdate = " AND date(created) = " . $db->quote(date('Y-m-d'));
            break;
            case "2": // maximum ticket in month
                $checkdate = " AND MONTH(created) = " . $db->quote(date('m',strtotime(date('Y-m-d'))));
            break;
            case "3": // maximum ticket in year
                $checkdate = " AND YEAR(created) = " . $db->quote(date('Y',strtotime(date('Y-m-d'))));
            break;
            case "4": // maximum ticket in life time
                $checkdate = "";
            break;
        }

        if($user->guest){
            $query = "SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE email = " . $db->quote($email); // ticket not answer and not closed
        }else{
            $query = "SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE uid = " . $user->id; // ticket not answer and not closed
        }
        $db->setQuery($query);
        $total = $db->loadResult();
        $ticketperemail = $config_ticket['maximum_ticket'];
        if ($total >= $ticketperemail) {
            return false;
        }
        return true;
    }

    function checkMaxOpenTickets($email) {
        if(empty($email)) return false;
        $db = $this->getDbo();
        $user = JFactory::getUser();
        if($user->guest){
            $query = "SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE status != 4 AND email = " . $db->quote($email); // ticket not answer and not closed
        }else{
            $query = "SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE status != 4 AND uid = " . $user->id; // ticket not answer and not closed
        }
        $db->setQuery($query);
        $total = $db->loadResult();
        $config_ticket = $this->getJSModel('config')->getConfigByFor('ticket');
        $ticketperemail = $config_ticket['ticket_per_email'];
        if ($total >= $ticketperemail) {
            return false;
        }
        return true;
    }

    function checkIsTicketDuplicate($subject,$email){
        if(empty($subject)) return false;
        if(empty($email)) return false;

        $curdate = date('Y-m-d H:i:s');
        $db = $this->getDbo();
        $subject = filter_var($subject, FILTER_SANITIZE_STRING);
        $query = "SELECT created FROM `#__js_ticket_tickets` WHERE email = " . $db->quote($email) . " AND subject = '" . $subject . "' ORDER BY created DESC LIMIT 1";
        $db->setQuery($query);
        $datetime = $db->loadResult();
        if($datetime){
            $diff = strtotime($curdate) - strtotime($datetime);
            if($diff <= 15){
                return false;
            }
        }
        return true;
    }

    function getTicketId() {
        $db = $this->getDBO();
        $query = "SELECT ticketid FROM `#__js_ticket_tickets`";
        $ticketid_sequence = $this->getJSModel('config')->getConfigurationByName('ticketid_sequence');
        $match = '';
        $ticketid = "";
        do {
            if($ticketid_sequence == 1){ // Random ticketid
                $ticketid = "";
                $length = 13;
                $possible = "2346789bcdfghjkmnpqrtvwxyzBCDFGHJKLMNPQRTVWXYZ";
                $maxlength = strlen($possible);
                if ($length > $maxlength) {
                    $length = $maxlength;
                }
                $i = 0;
                while ($i < $length) {
                    $char = substr($possible, mt_rand(0, $maxlength - 1), 1);
                    if (!strstr($ticketid, $char)) {
                        if ($i == 0) {
                            if (ctype_alpha($char)) {
                                $ticketid .= $char;
                                $i++;
                            }
                        } else {
                            $ticketid .= $char;
                            $i++;
                        }
                    }
                }
            }else{ // Sequential ticketid
                if($ticketid == ""){
                    $ticketid = 0; // by default its set to zero
                }
                $maxquery = "SELECT max(convert(ticketid, SIGNED INTEGER)) FROM `#__js_ticket_tickets`";
                $db->setQuery($maxquery);
                $maxticketid = $db->loadResult();
                if(is_numeric($maxticketid)){
                    $ticketid = $maxticketid + 1;
                }else{
                    $ticketid = $ticketid + 1;
                }
            }
            $db->setQuery($query);
            $rows = $db->loadObjectList();
            foreach ($rows as $row) {
                if ($ticketid == $row->ticketid){
                    $match = 'Y';
                    break;
                }else{
                    $match = 'N';
                }
            }
        }while ($match == 'Y');

        return $ticketid;
    }

    function getPriorities() {

        $db = $this->getDBO();
        $user = JSSupportTicketCurrentUser::getInstance();
        if($user->getIsStaff() || JFactory::getApplication()->isClient('administrator')){
            $query = "SELECT * FROM `#__js_ticket_priorities`";
        }else{
            $query = "SELECT * FROM `#__js_ticket_priorities` WHERE ispublic = 1";
        }

        $priorities = array();
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        $priorities[] = array('value' => null, 'text' => JText::_('Select Priority'));
        foreach ($rows as $row) {
            $priorities[] = array('value' => $row->id, 'text' => JText::_($row->priority));
        }

        return $priorities;
    }

    function getAttachmentByReplyId($id){
        if(!is_numeric($id)) return false;
        $db = $this->getDBO();
        $query = "SELECT attachment.filename , ticket.attachmentdir
                    FROM `#__js_ticket_attachments` AS attachment
                    JOIN `#__js_ticket_tickets` AS ticket ON ticket.id = attachment.ticketid AND attachment.replyattachmentid = ".$id ;
        $db->setQuery($query);
        $replyattachments = $db->loadObjectList();
        return $replyattachments;
    }


    private function getTicketHistory($id) {
        if(!is_numeric($id)) return false;
        $db = $this->getDBO();

        $query = "SELECT al.id,al.message,al.datetime,al.uid,al.level
        from `#__js_ticket_activity_log`  AS al
        join `#__js_ticket_tickets` AS tic on al.referenceid=tic.id
        where al.referenceid=" . $id . " AND al.eventfor=1 ORDER BY al.datetime DESC ";
        $db->setQuery($query);
        $result = $db->loadObjectList();
        return $result;
    }

    function updateTicketStatus($ticketid,$status,$created = false) {
        if (!is_numeric($ticketid) || (!is_numeric($status)))
            return false;
        $db = $this->getDbo();
        $inquery = '';
        if($status == 4 && $created)
            $inquery = ", ticket.closed = ".$db->quote($created)." , ticket.update = ".$db->quote($created);
        $query = "UPDATE `#__js_ticket_tickets` AS ticket SET ticket.status = $status ".$inquery." WHERE ticket.id = " . $ticketid;
        $db->setQuery($query);
        if (!$db->execute())
            return false;
        else
            return true;
    }

    function updateIsAnswered($ticketid,$isanswered) {
        if (!is_numeric($ticketid) || (!is_numeric($isanswered)))
            return false;
        $db = $this->getDbo();
        $query = "UPDATE `#__js_ticket_tickets` set isanswered = $isanswered WHERE id = " . $ticketid;
        $db->setQuery($query);
        if (!$db->execute())
            return false;
        else
            return true;
    }

    function updateTicketLastReply($ticketid, $created) {
        if (!is_numeric($ticketid))
            return false;
        $db = $this->getDbo();
        $query = "UPDATE `#__js_ticket_tickets` set lastreply = " . $db->quote($created) . " WHERE id = " . $ticketid;
        $db->setQuery($query);
        if (!$db->execute()) {
            return false;
        } else {
            return true;
        }
    }

    function updateTicketAssignToMyself($ticketid, $staffid) {
        if (!is_numeric($ticketid))
            return false;
        if($staffid){
            $db = $this->getDbo();
            $query = "UPDATE `#__js_ticket_tickets` set staffid = " .$staffid. " WHERE id = " . $ticketid;
            $db->setQuery($query);
            if (!$db->execute()) {
                return false;
            } else {
                return true;
            }
        }
    }

    function isTicketAssigned($ticketid){
        if (!is_numeric($ticketid))
            return false;
        $query = "SELECT staffid FROM `#__js_ticket_tickets` WHERE id=".$ticketid;
        $db = $this->getDbo();
        $db->setQuery($query);
        $staffid = $db->loadResult();
        if($staffid > 0)
            return true;
        return false;
    }

    private function performChecks() {
        // $request = JFactory::getApplication()->input->get();
        $request = JFactory::getApplication()->input->post->getArray();
        $session = JFactory::getApplication()->getSession();
        $type_calc = true;
        if ($type_calc) {
            if ($session->get('jsticket_rot13', null, 'jsticket_checkspamcalc') == 1) {
                $spamcheckresult = base64_decode(str_rot13($session->get('jsticket_spamcheckresult', null, 'jsticket_checkspamcalc')));
            } else {
                $spamcheckresult = base64_decode($session->get('jsticket_spamcheckresult', null, 'jsticket_checkspamcalc'));
            }

            $spamcheck = JFactory::getApplication()->input->getInt($session->get('jsticket_spamcheckid', null, 'jsticket_checkspamcalc'), '', 'post');

            $session->clear('jsticket_rot13', 'jsticket_checkspamcalc');
            $session->clear('jsticket_spamcheckid', 'jsticket_checkspamcalc');
            $session->clear('jsticket_spamcheckresult', 'jsticket_checkspamcalc');

            if (!is_numeric($spamcheckresult) || $spamcheckresult != $spamcheck) {
                return false; // Failed
            }
        }

        // Hidden field
        $type_hidden = 0;
        if ($type_hidden) {
            $hidden_field = $session->get('hidden_field', null, 'checkspamcalc');
            $session->clear('hidden_field', 'checkspamcalc');

            if (JFactory::getApplication()->input->get($hidden_field, '', 'post')) {
                return false; // Hidden field was filled out - failed
            }
        }

        // Time lock
        $type_time = 0;
        if ($type_time) {
            $time = $session->get('time', null, 'checkspamcalc');
            $session->clear('time', 'checkspamcalc');

            if (time() - $this->params->get('type_time_sec') <= $time) {
                return false; // Submitted too fast - failed
            }
        }

        // Own Question
        // Conversion to lower case
        $session->clear('ip', 'jsticket_checkspamcalc');
        $session->clear('saved_data', 'jsticket_checkspamcalc');

        return true;
    }

    function getLatestReplyByTicketId($id) {
        if (!is_numeric($id))
            return false;
        $db = JFactory::getDBO();
        $query = "SELECT reply.message FROM `#__js_ticket_replies` AS reply WHERE reply.ticketid = " . $id . " ORDER BY reply.created DESC LIMIT 1";
        $db->setQuery($query);
        $message = $db->loadResult();

        return $message;
    }

    function getFileSizeAndExtensions() {
        $file = array();
        $db = $this->getDBO();
        $query = "SELECT * FROM `#__js_ticket_config` WHERE configname ='filesize' OR configname='fileextension'";
        $db->setQuery($query);
        $result = $db->loadObjectList();
        foreach ($result AS $res) {
            if ($res->configname == 'filesize') {
                $file['filesize'] = $res->configvalue;
            } elseif ($res->configname == 'fileextension') {
                $file['fileextension'] = $res->configvalue;
            }
        }
        return json_encode($file);
    }


    function saveResponceAJAX($id,$responce){
        if($id) if(!is_numeric($id)) return false;

        $user = JSSupportticketCurrentUser::getInstance();
        $per = $user->checkUserPermission('Edit Ticket');
        if ($per == false) return PERMISSION_ERROR;
        $row = $this->getTable('replies');
        $data['id'] = $id;
        //$data['message'] = JFactory::getApplication()->input->get('message', '', 'raw');
        $data['message'] = $responce;

        if (!$row->bind($data)){
            $this->setError($row->getError());
            return SENT_ERROR;
        }
        if (!$row->check()){
            $this->setError($row->getError());
            return SENT_ERROR;
        }
        if (!$row->store()){
            $this->setError($row->getError());
            $this->getJSModel('systemerrors')->updateSystemErrors($row->getError());
            return SENT_ERROR;
        }
        return SENT;
    }

    function editResponceAJAX($id){
        $db = $this->getDBO();
        if($id) if(!is_numeric($id)) return false;

        $query = "SELECT message FROM `#__js_ticket_replies` WHERE id = ".$id;
        $db->setQuery( $query );
        $row = $db->loadObject();
        $editor = JFactory::getConfig()->get('editor');
	$editor = JEditor::getInstance($editor);
        if(isset($row)){
            //$return_value = $editor->display("editor_responce_$id", $row->message, '550', '300', '60', '20', false);
            $return_value =  $editor->display("editor_responce_$id", $row->message, "600", "400", "80", "15", 1, null, null, null, array('mode' => 'advanced'));
        }else{
            $return_value = $editor->display('editor_responce_'.$id, '', '550', '300', '60', '20', false);
        }

        $return_value .= '<br />
        <input type="button" class="tk_dft_btn" value="'.JText::_('Post Reply').'" onclick="saveResponce('.$id.')">
        <input type="button" class="tk_dft_btn" value="'.JText::_('Close').'" onclick="closeResponce('.$id.')">';
        return $return_value;
    }

    function deleteResponceAJAX($id){
        if($id) if(!is_numeric($id)) return false;
        $user = JSSupportticketCurrentUser::getInstance();
        $per = $user->checkUserPermission('Delete Ticket');
        if ($per == false) return PERMISSION_ERROR;
        $row = $this->getTable('replies');
        if (!$row->delete($id)){
            $this->setError($row->getErrorMsg());
            return SENT_ERROR;
        }
        return SENT;
    }

    function getDownloadAttachmentById($id){
        if(!is_numeric($id)) return false;
        $db = JFactory::getDbo();
        $query = "SELECT ticket.id AS ticketid,attach.filename,ticket.attachmentdir AS foldername  "
                . " FROM `#__js_ticket_attachments` AS attach "
                . " JOIN `#__js_ticket_tickets` AS ticket ON ticket.id = attach.ticketid "
                . " WHERE attach.id = $id";
        $db->setQuery($query);
        $object = $db->loadObject();
        $ticketid = $object->ticketid;
        $filename = $object->filename;
        $foldername = $object->foldername;
        $download = false;
        $user = JFactory::getUser();
        if(!$user->guest){
            if(JFactory::getApplication()->isClient('administrator')){
                $download = true;
            }else{
                if($this->getJSModel('staff')->isUserStaff()){
                    $download = true;
                }else{
                    if($this->getJSModel('ticket')->validateTicketDetailForUser($ticketid)){
                        $download = true;
                    }
                }
            }
        }else{ // user is visitor
            $download = $this->getJSModel('ticket')->validateTicketDetailForVisitor($ticketid);
        }
        if($download == true){
            $datadirectory = $this->getJSModel('config')->getConfigurationByName('data_directory');
            $base = JPATH_BASE;
            if(JFactory::getApplication()->isClient('administrator')){
                $base = substr($base, 0, strlen($base) - 14); //remove administrator
            }
            $path = $base.'/'.$datadirectory;
            $path = $path . '/attachmentdata';
            $path = $path . '/ticket/' . $foldername;
            $file = $path . '/' . $filename;
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . basename($file));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            ob_clean();
            flush();
            readfile($file);
            exit();
        }else{
            throw new Exception(JText::_('Page not found'),404);
            exit;
        }
    }
    function getAllDownloadFiles() {
        $downloadid = JFactory::getApplication()->input->get('id');
        $ticketattachment = $this->getAttachmentByTicketId($downloadid);
        if(!class_exists('PclZip')){
            require_once('administrator/components/com_jssupportticket/include/lib/pclzip.lib.php');
        }
        $config = $this->getJSModel('config')->getConfigs();
        $path = JPATH_BASE.'/'.$config['data_directory'];
        $path .= '/zipdownloads';
        $this->getJSModel('attachments')->makeDir($path);
        $randomfolder = $this->getRandomFolderName($path);
        $path .= '/' . $randomfolder;
        $this->getJSModel('attachments')->makeDir($path);
        $archive = new PclZip($path . '/alldownloads.zip');
        $arr = array();
        foreach ($ticketattachment AS $ticketattachments) {
            $directory = JPATH_BASE .'/'. $config['data_directory'] . '/attachmentdata/ticket/' . $ticketattachments->attachmentdir . '/';
            // $scanned_directory = array_diff(scandir($directory), array('..', '.'));
            array_push($scanned_directory,$ticketattachments->filename);
            $arr[] = $ticketattachments->filename;
        }
        $scanned_directory = array_diff(scandir($directory), array('..', '.'));
        $scanned_directory = array_diff(scandir($directory), array('..', '.','index.html'));
        $filelist = '';
        foreach ($scanned_directory AS $file) {
            if(in_array($file,$arr)){
                $filelist .= $directory . '/' . $file . ',';
            }
        }
        $filelist = substr($filelist, 0, strlen($filelist) - 1);
        $v_list = $archive->create($filelist, PCLZIP_OPT_REMOVE_PATH, $directory);
        if ($v_list == 0) {
            die("Error : '" . $archive->errorInfo() . "'");
        }
        $file = $path . '/alldownloads.zip';
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename($file));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        ob_clean();
        flush();
        readfile($file);
        @unlink($file);
        /*$path = jssupportticket::$_path;
        $path .= 'zipdownloads';
        $path .= '/' . $randomfolder;*/
        @unlink($path . '/index.html');
        rmdir($path);
        exit();
    }
     function getAllReplyDownloadsFiles() {
        $downloadid = JFactory::getApplication()->input->get('id');
        $replyattachment = $this->getAttachmentByReplyId($downloadid);
        if(!class_exists('PclZip')){
            require_once('administrator/components/com_jssupportticket/include/lib/pclzip.lib.php');
        }
        $config = $this->getJSModel('config')->getConfigs();
        $path = JPATH_BASE.'/'.$config['data_directory'];
        $path .= '/zipdownloads';
        $this->getJSModel('attachments')->makeDir($path);
        $randomfolder = $this->getRandomFolderName($path);
        $path .= '/' . $randomfolder;
        $this->getJSModel('attachments')->makeDir($path);
        $archive = new PclZip($path . '/alldownloads.zip');
        $arr=array();
        foreach ($replyattachment AS $replyattachments) {
            $directory = JPATH_BASE .'/'. $config['data_directory'] . '/attachmentdata/ticket/' . $replyattachments->attachmentdir . '/';
            // $scanned_directory = array_diff(scandir($directory), array('..', '.'));
            array_push($scanned_directory,$replyattachments->filename);
            $arr[] = $replyattachments->filename;
        }
        $scanned_directory = array_diff(scandir($directory), array('..', '.'));
        $scanned_directory = array_diff(scandir($directory), array('..', '.','index.html'));
        $filelist = '';
        foreach ($scanned_directory AS $file) {
            if(in_array($file,$arr)){
                $filelist .= $directory . '/' . $file . ',';
            }
        }
        $filelist = substr($filelist, 0, strlen($filelist) - 1);
        $v_list = $archive->create($filelist, PCLZIP_OPT_REMOVE_PATH, $directory);
        if ($v_list == 0) {
            die("Error : '" . $archive->errorInfo() . "'");
        }
        $file = $path . '/alldownloads.zip';
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename($file));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        ob_clean();
        flush();
        readfile($file);
        @unlink($file);
        /*$path = jssupportticket::$_path;
        $path .= 'zipdownloads';
        $path .= '/' . $randomfolder;*/
        @unlink($path . '/index.html');
        rmdir($path);
        exit();
    }

    function getDownloadAttachmentByName($file_name,$id){
        if(empty($file_name)) return false;
        if(!is_numeric($id)) return false;
        $db = JFactory::getDbo();
		$file_name = basename($file_name);
        $filename = str_replace(' ', '_',$file_name);
        $query = "SELECT attachmentdir FROM `#__js_ticket_tickets` WHERE id = ".$id;
        $db->setQuery($query);
        $foldername = $db->loadResult();

        $datadirectory = $this->getJSModel('config')->getConfigurationByName('data_directory');
        $base = JPATH_BASE;
        if(JFactory::getApplication()->isClient('administrator')){
            $base = substr($base, 0, strlen($base) - 14); //remove administrator
        }
        $path = $base.'/'.$datadirectory;
        $path = $path . '/attachmentdata';
        $path = $path . '/ticket/' . $foldername;
        $file = $path . '/' . $filename;

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename($file));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        //ob_clean();
        flush();
        readfile($file);
        exit();
        exit;
    }

    function validateTicketDetailForUser($id) {
        if (!is_numeric($id))
            return false;
        $session = JFactory::getApplication()->getSession();
        $db = JFactory::getDbo();
        $query = "SELECT uid,email FROM `#__js_ticket_tickets` WHERE id = " . $id;
        $db->setQuery($query);
        $ticket = $db->loadObject();
        $user_id = JFactory::getUser();
        $user = JSSupportticketCurrentUser::getInstance();
        if(!empty($ticket->uid)){
            if (($ticket->uid == $user_id->id) || ( $ticket->uid == 0 && $ticket->email == $user->getEmail() ) )  {// seoncd check to handle tickets created as visitor using email of a logged in member
                return true;
            }else{
                $session->set('ticketuserid',$ticket->uid);
                return false;
            }
        }
    }

    function validateTicketDetailForVisitor($id) {
        $session = JFactory::getApplication()->getSession();
        $ticketid = $session->get('userticketid');
        $ticketid = $this->getJSModel('ticket')->getIdFromTrackingId($ticketid);
        if ($ticketid == $id) {
            return true;
        } else {
            return false;
        }
    }

    function validateTicketDetailForStaff($ticketid) {
        if (!is_numeric($ticketid))
            return false;
        // check in assign department
        $user = JSSupportticketCurrentUser::getInstance();
        $db = JFactory::getDbo();
        $query = "SELECT ticket.id FROM `#__js_ticket_tickets` AS ticket
                    JOIN `#__js_ticket_acl_user_access_departments` AS dept ON ticket.departmentid = dept.departmentid
                    JOIN `#__js_ticket_staff` AS staff ON dept.staffid = staff.id AND staff.uid = " . $user->getId() . "
                     WHERE ticket.id = " . $ticketid;
        $db->setQuery($query);
        $id = $db->loadResult();

        if ($id) {
            return true;
        } else {
            // check in assign ticket
            $query = "SELECT ticket.id FROM `#__js_ticket_tickets` AS ticket
                    JOIN `#__js_ticket_staff` AS staff ON ticket.staffid = staff.id AND staff.uid = " . $user->getId(). "
                    WHERE ticket.id = " . $ticketid;
            $db->setQuery($query);
            $id = $db->loadResult();
            if ($id)
                return true;
            else
                return false;
        }
    }

    // new
    function uploadFileCustom($id,$field){

        if(! is_numeric($id))
            return;

        $db = JFactory::getDbo();

        $config = $this->getJSModel('config')->getConfigByFor('default');
        $model_attachment = $this->getJSModel('attachments');

        if ($_FILES[$field]['size'] > 0) {
            $file_name = str_replace(' ', '_', $_FILES[$field]['name']);
            $file_tmp = $_FILES[$field]['tmp_name']; // actual location
        }else{
            return;
        }
        $file_size = $config['filesize'];
        if($_FILES[$field]['size'] > ($file_size * 1024)){
            return;
        }
        if ($file_name != "" AND $file_tmp != "") {
            $is_allow = $model_attachment->checkExtension($file_name);
            if($is_allow == 'N'){
                return;
            }
        }
        $datadirectory = $config['data_directory'];
        $base = JPATH_BASE;
        if(JFactory::getApplication()->isClient('administrator')){
            $base = substr($base, 0, strlen($base) - 14); //remove administrator
        }
        $path = $base.'/'.$datadirectory;
        if (!file_exists($path)){ // create user directory
            $model_attachment->makeDir($path);
        }
        $path = $path . '/attachmentdata';
        if (!file_exists($path)){ // create user directory
            $model_attachment->makeDir($path);
        }
        $path = $path . '/ticket';
        if (!file_exists($path)){ // create user directory
            $model_attachment->makeDir($path);
        }

        $query = "SELECT attachmentdir FROM `#__js_ticket_tickets` WHERE id = ".$id;
        $db->setQuery($query);
        $foldername = $db->loadResult();
        $userpath = $path . '/' . $foldername;
        if (!file_exists($userpath)) { // create user directory
            $model_attachment->makeDir($userpath);
        }
        move_uploaded_file($file_tmp, $userpath . '/' . $file_name);
        /*
        //Override the record and delete the old file if exists
        $query = "SELECT params FROM `#__js_ticket_tickets` WHERE id = ".$id;
        $db->setQuery($query);
        $params = $db->loadResult();
        $p_array = json_decode($params,true);
        //Remove old file if exists
        $old_file = $p_array[$field];
        if(file_exists($userpath . '/' . $old_file)){
            unlink($userpath . '/' . $old_file);
        }
        //--------------------------
        $p_array[$field] = $file_name;
        $params = json_encode($p_array);
        $query = "UPDATE `#__js_ticket_tickets` SET params = '".$params."' WHERE id = ".$id;
        $db->setQuery($query);
        $db->execute();
        */
        return;
    }

    function removeFileCustom($id, $key){
        $filename = str_replace(' ', '_', $key);

        if(! is_numeric($id))
            return;

        $db = JFactory::getDbo();
        $config = $this->getJSModel('config')->getConfigByFor('default');
        $datadirectory = $config['data_directory'];

        $base = JPATH_BASE;
        if(JFactory::getApplication()->isClient('administrator')){
            $base = substr($base, 0, strlen($base) - 14); //remove administrator
        }

        $path = $base . '/' . $datadirectory. '/attachmentdata/ticket';

        $query = "SELECT attachmentdir FROM `#__js_ticket_tickets` WHERE id = ".$id;
        $db->setQuery($query);
        $foldername = $db->loadResult();
        $userpath = $path . '/' . $foldername.'/'.$filename;
        unlink($userpath);
        return;
    }

    /// ...
    function getReplyDataByID() {
        $db = JFactory::getDbo();
        $replyid = JFactory::getApplication()->input->get('val');
        if(!is_numeric($replyid)) return false;
        $query = "SELECT reply.id AS replyid, reply.message AS message
                    FROM `#__js_ticket_replies` AS reply
                    WHERE reply.id =  " . $replyid ;
        $db->setQuery($query);
        $lastreply = $db->loadObject();

        return json_encode($lastreply);
    }

    function editReply($data) {
        $db = JFactory::getDbo();
        if (empty($data))
            return false;
        //$desc = JFactory::getApplication()->input->get( 'jsticket_replytext', '', 'post','string', JREQUEST_ALLOWHTML ); // use jsticket_message to avoid conflict
        $desc = JFactory::getApplication()->input->get('jsticket_replytext', '', 'raw');
        $query = "UPDATE `#__js_ticket_replies` SET message = " . $db->Quote($desc) . "  WHERE id = " . $data['reply-replyid'];        $db->setQuery($query);
        $db->execute();
        return REPLY_EDITED;
    }

    function getTimeByReplyID() {
        $db = JFactory::getDbo();
        $replyid = JFactory::getApplication()->input->get('val');
        if(!is_numeric($replyid)) return false;
        $query = "SELECT time.usertime, time.conflict, time.description,time.systemtime
                    FROM `#__js_ticket_staff_time` AS time
                    WHERE time.referencefor = 1 AND time.referenceid =  " . $replyid ;
        $db->setQuery($query);
        $stime = $db->loadObject();
        $result['time'] = '';
        $result['desc'] = '';
        $result['conflict'] = '';
        $result['systemtime'] = '';
        if(!empty($stime)){
            $hours = floor($stime->usertime / 3600);
            $mins = floor($stime->usertime / 60 % 60);
            $secs = floor($stime->usertime % 60);

            $shours = floor($stime->systemtime / 3600);
            $smins = floor($stime->systemtime / 60 % 60);
            $ssecs = floor($stime->systemtime % 60);

            $result['time'] =  sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
            $result['desc'] =  $stime->description == '' ? ' ' : $stime->description ;
            $result['conflict'] =  $stime->conflict;
            $result['systemtime'] =  sprintf('%02d:%02d:%02d', $shours, $smins, $ssecs);
        }
        return json_encode($result);
    }

    function editTime($data) {// for reply
        $db = JFactory::getDbo();
        if (empty($data))
            return false;
        // confilct resolution handling
        if($data['time-confilct'] == 1){
            if($data['time-confilct-combo'] == 1){
                $up_query = ' , conflict = 0';
            }
        }else{
            $up_query = '';
        }

        //$data['edit_reason'] = JFactory::getApplication()->input->get( 'edit_reason', '', 'post','string', JREQUEST_ALLOWHTML ); // use jsticket_message to avoid conflict
        $data['edit_reason'] = JFactory::getApplication()->input->get('edit_reason', '', 'raw');
        $query = "SELECT id FROM `#__js_ticket_staff_time`  WHERE referencefor = 1 AND  referenceid = " . $data['reply-replyid'];
        $db->setQuery($query);
        $id = $db->loadResult();
        $edited_time = $data['edited_time'];
        $timearray = explode(':', $edited_time);

        if(!isset($timearray[0]) || !isset($timearray[1]) || !isset($timearray[2])){
            $seconds = 0;
        }else{
            if(is_numeric($timearray[0]) && is_numeric($timearray[1]) && is_numeric($timearray[2])){
                $seconds = ($timearray[0] * 3600) + ($timearray[1] * 60) + $timearray[2];
            }else{
                return;
            }
        }
        if($seconds < 0){
            return;
        }

        if($id > 0){
            $query = "UPDATE `#__js_ticket_staff_time` SET usertime = " . $seconds .$up_query . ",description = '".$data['edit_reason']."'  WHERE referencefor = 1 AND  referenceid = " . $data['reply-replyid'];
            $db->setQuery($query);
            $db->execute($query);
        }else{
            $query = "SELECT staffid,ticketid FROM `#__js_ticket_replies`  WHERE  id = " . $data['reply-replyid'];
            $db->setQuery($query);
            $reply = $db->loadObject();
            $created = date('Y-m-d H:i:s');
            $row = $this->getTable('stafftime');
            $data2['ticketid'] =  $reply->ticketid;
            $data2['staffid'] =  $reply->staffid;
            $data2['referencefor'] =  1;
            $data2['referenceid'] =  $data['reply-replyid'];
            $data2['usertime'] =  $seconds;
            $data2['systemtime'] =  0;
            $data2['conflict'] =  0;
            $data2['description'] =  $data['edit_reason'];
            $data2['status'] =  1;
            $data2['created'] =  $created;
            if (!$row->bind($data2)) {
                $this->setError($row->getError());
                return;
            }
            if (!$row->check()) {
                $this->setError($row->getError());
                return;
            }
            if (!$row->store()) {
                $this->getJSModel('systemerrors')->updateSystemErrors($row->getError());
                $this->setError($row->getError());
                return;
            }
        }
        return TIME_EDITED;
    }

    function editTimeForNote($data) {// for Note
        $db = JFactory::getDbo();
        if (empty($data))
            return false;
        // confilct resolution handling
        if($data['time-confilct'] == 1){
            if($data['time-confilct-combo'] == 1){
                $up_query = ' , conflict = 0';
            }
        }else{
            $up_query = '';
        }

        //$data['edit_reason'] = JFactory::getApplication()->input->get( 't_desc', '', 'post','string', JREQUEST_ALLOWHTML ); // use jsticket_message to avoid conflict
        $data['edit_reason'] = JFactory::getApplication()->input->get('t_desc', '', 'raw');
        $query = "SELECT id FROM `#__js_ticket_staff_time`  WHERE referencefor = 2 AND  referenceid = " . $data['note-noteid'];
        $db->setQuery($query);
        $id = $db->loadResult();
        $edited_time = $data['edited_time'];
        $timearray = explode(':', $edited_time);

        if(!isset($timearray[0]) || !isset($timearray[1]) || !isset($timearray[2])){
            $seconds = 0;
        }else{
            if(is_numeric($timearray[0]) && is_numeric($timearray[1]) && is_numeric($timearray[2])){
                $seconds = ($timearray[0] * 3600) + ($timearray[1] * 60) + $timearray[2];
            }else{
                return;
            }
        }
        if($seconds < 0){
            return;
        }

        if($id > 0){
            $query = "UPDATE `#__js_ticket_staff_time` SET usertime = " . $seconds .$up_query . ",description = '".$data['edit_reason']."'  WHERE referencefor = 2 AND  referenceid = " . $data['note-noteid'];
            $db->setQuery($query);
            $db->execute($query);
        }else{
            $query = "SELECT staffid,ticketid FROM `#__js_ticket_notes`  WHERE  id = " . $data['note-noteid'];
            $db->setQuery($query);
            $reply = $db->loadObject();
            $created = date('Y-m-d H:i:s');
            $row = $this->getTable('stafftime');
            $data2['ticketid'] =  $reply->ticketid;
            $data2['staffid'] =  $reply->staffid;
            $data2['referencefor'] =  2;
            $data2['referenceid'] =  $data['note-noteid'];
            $data2['usertime'] =  $seconds;
            $data2['systemtime'] =  0;
            $data2['conflict'] =  0;
            $data2['description'] =  $data['edit_reason'];
            $data2['status'] =  1;
            $data2['created'] =  $created;
            if (!$row->bind($data2)) {
                $this->setError($row->getError());
                return;
            }
            if (!$row->check()) {
                $this->setError($row->getError());
                return;
            }
            if (!$row->store()) {
                $this->getJSModel('systemerrors')->updateSystemErrors($row->getError());
                $this->setError($row->getError());
                return;
            }
        }
        return TIME_EDITED;
    }

    function getTicketsForMerging(){

        $db = JFactory::getDbo();
        $user = JSSupportticketCurrentUser::getInstance();
        $ticketlimit = JFactory::getApplication()->input->get('ticketlimit', null, 0);
        $id = JFactory::getApplication()->input->get('ticketid');
        $name = JFactory::getApplication()->input->getString('name');
        $email = JFactory::getApplication()->input->getString('email');
        $maxrecorded = 4;
        $wherequery = '';
        if (strlen($name) > 1) {
            $name = trim($name);
            $wherequery .= " AND ticket.subject LIKE ".$db->quote('%'.$name.'%');
        }
        if (strlen($email) > 1) {
            $email = trim($email);
            $wherequery .= " AND ticket.email LIKE ".$db->quote('%'.$email.'%');
        }

        $status = 1;
        $tickets = array();
        if($user->getIsGuest())
            $status = 0;
        if($status == 1){

            $query = "SELECT ticket.id, ticket.subject, ticket.ticketid,department.departmentname AS departmentname,ticket.name, ticket.created,ticket.uid,ticket.email,priority.priority as priorityname, priority.prioritycolour,staff.photo as staffphoto, staff.id as staffid
                        FROM `#__js_ticket_tickets` AS ticket
                        LEFT JOIN `#__js_ticket_departments` AS department ON ticket.departmentid = department.id
                        INNER JOIN `#__js_ticket_priorities` AS priority ON ticket.priorityid = priority.id
                        LEFT JOIN `#__js_ticket_staff` AS staff ON staff.uid = ticket.uid
                        WHERE ticket.id=" .$id;
            $db->setQuery($query);
            $ticketdetail = $db->loadObject();
            if($ticketdetail->uid != 0){
                $wherequery .= " AND ticket.uid = " .$ticketdetail->uid;
            }else{
                $wherequery .= " AND ticket.email = '" .$ticketdetail->email ."'";
            }

            $query = "SELECT COUNT(ticket.id)
                        FROM `#__js_ticket_tickets` AS ticket
                        LEFT JOIN `#__js_ticket_departments` AS department ON ticket.departmentid = department.id
                        INNER JOIN `#__js_ticket_priorities` AS priority ON ticket.priorityid = priority.id";
            $query .= " WHERE ticket.status != 4 AND ticket.mergestatus != 1 AND ticket.id !=" .$id;
            $query .= $wherequery;
            $db->setQuery($query);
            $total = $db->loadResult();
            $limit = $ticketlimit * $maxrecorded;
            if ($limit >= $total) {
                $limit = 0;
            }
            $query = "SELECT ticket.id, ticket.ticketid, ticket.subject, ticket.email, department.departmentname AS departmentname, ticket.name as username, ticket.created,priority.priority as priorityname,priority.prioritycolour, staff.photo as staffphoto, staff.id as staffid
                        FROM `#__js_ticket_tickets` AS ticket
                        LEFT JOIN `#__js_ticket_departments` AS department ON ticket.departmentid = department.id
                        INNER JOIN `#__js_ticket_priorities` AS priority ON ticket.priorityid = priority.id
                        LEFT JOIN `#__js_ticket_staff` AS staff ON staff.uid = ticket.uid";
            $query .= " WHERE ticket.id !=" .$id;
            $query .= " AND ticket.status != 4 AND ticket.mergestatus != 1";
            $query .= $wherequery;
            $query .= " LIMIT $limit, $maxrecorded";
            $db->setQuery($query);
            $tickets = $db->loadObjectList();
        }
        $html = $this->makeTicketList($tickets, $total, $maxrecorded, $ticketlimit,$ticketdetail,$email,$name);
        return array("status"=>$status,"data"=>$html);
    }

    function getLatestReplyForMerging(){
        $user = JSSupportticketCurrentUser::getInstance();
        $secondaryid = JFactory::getApplication()->input->get('secondaryid');
        $primaryid = JFactory::getApplication()->input->get('primaryid');

        $primaryticket = $this->getTicketNameById($primaryid);
        $secondaryticket = $this->getTicketNameById($secondaryid);
        $latestreply = strip_tags($this->getLatestReplyByTicketId($secondaryid));
        $secondarymessage = JText::_('Ticket Has Been Closed Due To Merged With').' '.$primaryticket;
        $primarymessage =   JText::_('Ticket').' '.$secondaryticket.' '.JText::_('Has Been merged into').' '.$primaryticket;
        $secondaryticketdata = $this->getTicketDataForMerge($secondaryid);
        $primaryticketdata = $this->getTicketDataForMerge($primaryid);
        $html = $this->makeTicketMergeView($latestreply,$primaryid,$secondaryid,$primarymessage,$secondarymessage,$secondaryticketdata,$primaryticketdata);
        return $html;
    }

    function getTicketDataForMerge($id){
        if(!is_numeric($id)) return false;
        $db = JFactory::getDbo();
        $query = "SELECT ticket.id, ticket.subject, ticket.ticketid,department.departmentname AS departmentname,ticket.name, ticket.created,ticket.uid,ticket.email,priority.priority as priorityname, priority.prioritycolour,staff.photo as staffphoto, staff.id as staffid
                    FROM `#__js_ticket_tickets` AS ticket
                    LEFT JOIN `#__js_ticket_departments` AS department ON ticket.departmentid = department.id
                    INNER JOIN `#__js_ticket_priorities` AS priority ON ticket.priorityid = priority.id
                    LEFT JOIN `#__js_ticket_staff` AS staff ON staff.uid = ticket.uid
                    WHERE ticket.id=" .$id;
        $db->setQuery($query);
        $ticketdata=$db->loadObject();

        return $ticketdata;
    }

    function makeTicketList($tickets, $total, $maxrecorded, $ticketlimit,$ticketdata,$email,$name) {
        $datadirectory = $this->getJSModel('config')->getConfigurationByName('data_directory');
                $html ='
                    <div class="jsst-popup-header">
                       <div class="popup-header-text">
                            '.JText::_("Merge Ticket").'
                        </div>
                        <div class="popup-header-close-img" id="close-pop"></div>
                    </div>
                    <div id="js-ticket-merge-ticket-wrp">
                    <div class="js-ticket-merge-ticket-wrapper">
                        <div class="js-col-xs-12 js-col-md-12 js-ticket-wrapper js-ticket-merge-white-bg">
                            <div class="js-col-xs-12 js-col-md-12 js-ticket-toparea">
                                <div class="js-col-md-2 js-col-xs-12 js-ticket-pic">';
                                    if ($ticketdata->staffphoto){
                                        $html .='<img class="js-ticket-staff-img" src=" '. JURI::root(). $datadirectory . "/staffdata/staff_" . $ticketdata->staffid . "/" . $ticketdata->staffphoto .' ">';
                                    }else {
                                        $html .='<img class="js-ticket-staff-img" src="' . JURI::root().'components/com_jssupportticket/include/images/user.png" />';
                                    }; $html .='
                                </div>
                                <div class="js-col-md-6 js-col-xs-6 js-ticket-data js-nullpadding">
                                    <div class="js-col-md-12 js-col-xs-12 js-ticket-padding-xs js-ticket-body-data-elipses subject">
                                        <span class="js-ticket-title">
                                           '.JText::_('Subject').JText::_(':').'&nbsp;:&nbsp
                                        </span>
                                        <a class="js-ticket-merge-ticket-title">' .$ticketdata->subject.' </a>
                                    </div>
                                    <div class="js-col-md-12 js-col-xs-12 js-ticket-padding-xs js-ticket-body-data-elipses">
                                        <span class="js-ticket-title">' .JText::_('From').JText::_(':').'&nbsp;:&nbsp;</span>
                                        <span class="js-ticket-value" style="cursor:pointer;">'. $ticketdata->name.'</span>
                                    </div>
                                    <div class="js-col-md-12 js-col-xs-12 js-ticket-padding-xs js-ticket-body-data-elipses">
                                        <span class="js-ticket-title">' .JText::_('Department').JText::_(':').'&nbsp;:&nbsp;</span>
                                        <span class="js-ticket-value" style="cursor:pointer;">'.$ticketdata->departmentname.'</span>
                                    </div>
                                </div>
                                <div class="js-col-md-4 js-col-xs-4 js-ticket-data1 js-ticket-padding-left-xs">
                                    <div class="js-row">
                                        <div class="js-col-md-6 js-col-xs-3">'.JText::_('Ticket ID').JText::_(' : # ').'</div>
                                        <div class="js-col-md-6 js-col-xs-6">'. $ticketdata->id.'</div>
                                    </div>
                                    <div class="js-row">
                                        <div class="js-col-md-6 js-col-xs-3">'.JText::_('Created').JText::_(' : ').'</div>
                                        <div class="js-col-md-6 js-col-xs-6">'.date( "Y-m-d", strtotime($ticketdata->created)).'</div>
                                    </div>
                                    <div class="js-row">
                                        <div class="js-col-md-6 js-col-xs-3">'.JText::_('Priority').JText::_(' : ').'</div>
                                        <div class="js-col-md-6 js-col-xs-6"><span class="js-ticket-wrapper-textcolor" style="background:' .$ticketdata->prioritycolour.'">'.JText::_($ticketdata->priorityname).'</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="js-tickets-list-wrp">
                        <form id="ticketpopupsearch" class="js-popup-search">
                            <div class="js-col-md-12 js-form-wrapper jsst-form-wrapper">
                                <div class="js-col-md-12 js-form-title js-merge-form-title js-bold-text">'. JText::_('Search Ticket to merge into') . '</div>
                                <div class="js-merge-form-wrp">
                                    <div class="js-form-value js-merge-form-value"><input class="inputbox js-merge-field" id="name" type="text" name="name" placeholder='.JText::_("Username").'/></div>
                                    <div class="js-form-value js-merge-form-value"><input class="inputbox js-merge-field" id="email" type="text" name="email" placeholder='.JText::_("Email").' /></div>
                                </div>
                                <div class="js-merge-form-btn-wrp">
                                    <span class="js-merge-btn"><input type="submit" value=' . JText::_('Search') . ' class="button js-merge-button js-search" /></span>
                                    <span class="js-merge-btn"><input type="submit" value=' . JText::_('Reset') . ' onclick="formField()"  class="button js-merge-button js-cancel" /></span>
                                </div>
                            </div>
                        </form>
                        <div class="js-col-md-12 js-view-tickets">';
                            if (!empty($tickets)) {
                                if (is_array($tickets)) {
                                    foreach ($tickets AS $ticket) {
                                        $html .= '
                                        <div class="js-col-xs-12 js-col-md-12 js-ticket-wrapper js-merge-ticket-overlay js-ticket-merge-white-bg">
                                            <div class="js-col-xs-12 js-col-md-12 js-ticket-toparea">
                                               <div class="js-col-xs-2 js-col-md-2 js-ticket-pic">';
                                                    if ($ticket->staffphoto){
                                                        $html .='<img class="js-ticket-staff-img" src=" '. JURI::root(). $datadirectory . "/staffdata/staff_" . $ticket->staffid . "/" . $ticket->staffphoto .' ">';
                                                    }else {
                                                        $html .='<img class="js-ticket-staff-img" src="' . JURI::root().'components/com_jssupportticket/include/images/user.png" />';
                                                    };
                                                $html .='</div>
                                                <div class="js-col-xs-6 js-col-md-6 js-col-xs-6 js-ticket-data js-nullpadding">
                                                    <div class="js-col-xs-12 js-col-md-12 js-ticket-padding-xs js-ticket-body-data-elipses subject">
                                                        <span class="js-ticket-title">
                                                            '.JText::_('Subject').':&nbsp;:&nbsp
                                                        </span>
                                                        <a class="js-ticket-merge-ticket-title">' .$ticket->subject.' </a>
                                                    </div>
                                                    <div class="js-col-xs-12 js-col-md-12 js-ticket-padding-xs js-ticket-body-data-elipses">
                                                        <span class="js-ticket-title">' .JText::_('From').':&nbsp;:&nbsp;</span>
                                                        <span class="js-ticket-value" style="cursor:pointer;">'. $ticket->username.'</span>
                                                    </div>
                                                    <div class="js-col-xs-12 js-col-md-12 js-ticket-padding-xs js-ticket-body-data-elipses">
                                                        <span class="js-ticket-title">' .JText::_('Department').':&nbsp;:&nbsp;</span>
                                                        <span class="js-ticket-value" style="cursor:pointer;">'.$ticket->departmentname.'</span>
                                                    </div>
                                                </div>
                                                <div class="js-col-xs-4 js-col-md-4 js-ticket-data1 js-ticket-padding-left-xs">
                                                    <div class="js-row">
                                                        <div class="js-col-xs-6 js-col-md-6 js-col-xs-3">'.JText::_("Ticket ID").JText::_(" : ").'</div>
                                                        <div class="js-col-xs-6 js-col-md-6 js-col-xs-6">'. $ticket->id.'</div>
                                                    </div>
                                                    <div class="js-row">
                                                        <div class="js-col-xs-6 js-col-md-6 js-col-xs-3">'.JText::_('Created').JText::_(': ').'</div>
                                                        <div class="js-col-xs-6 js-col-md-6 js-col-xs-6">'.date( "Y-m-d", strtotime($ticket->created)).'</div>
                                                    </div>
                                                    <div class="js-row">
                                                        <div class="js-col-xs-6 js-col-md-6 js-col-xs-3">'.JText::_('Priority').JText::_(': ').'</div>
                                                        <div class="js-col-xs-6 js-col-md-6 js-col-xs-6"><span class="js-ticket-wrapper-textcolor" style="background:' .$ticket->prioritycolour.'">'. JText::_($ticket->priorityname).'</span></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="js-over-lay">
                                                <a href="#" class="js-merge-btn" onclick=getmergeticketid('.$ticketdata->id.','.$ticket->id.')>'. JText::_("Select").'</a>
                                            </div>
                                        </div>';
                                    }
                                }
                            }else {
                                $html .= messageslayout::getRecordNotFound(1);
                                }
                        $html .= '</div>';
                            $num_of_pages = ceil($total / $maxrecorded);
                            $num_of_pages = ($num_of_pages > 0) ? ceil($num_of_pages) : floor($num_of_pages);
                            if($num_of_pages > 0){
                                $page_html = '';
                                $prev = $ticketlimit;
                                if($prev > 0){
                                    $page_html .= '<a class="jsst_userlink" href="#" onclick="updateticketlist('.($prev - 1).','.$ticketdata->id.');">'.JText::_('Previous').'</a>';
                                }
                                for($i = 0; $i < $num_of_pages; $i++){
                                    if($i == $ticketlimit)
                                        $page_html .= '<span class="jsst_userlink selected" >'.($i + 1).'</span>';
                                    else
                                        $page_html .= '<a class="jsst_userlink" href="#" onclick="updateticketlist('.$i.','.$ticketdata->id.');">'.($i + 1).'</a>';

                                }
                                $next = $ticketlimit + 1;
                                if($next < $num_of_pages){
                                    $page_html .= '<a class="jsst_userlink js-text-align-right" href="#" onclick="updateticketlist('.$next.','.$ticketdata->id.');">'.JText::_('Next').'</a>';
                                }
                                if($page_html != ''){
                                    $html .= '<div class="jsst_userpages">'.$page_html.'</div>';
                                }
                            }
                    $html .='</div>
                    <div class="js-col-md-12 js-form-button-wrapper js-form-button-wrapper-merge">
                        <input id="close-pop" type="button" onclick="closePopup()" value=' . JText::_('Cancel') . ' class="button js-merge-cancel-btn" />
                        <input type="hidden" id="ticketidformerge" value="'.$ticketdata->id.'" />
                    </div>
                    </div>';
        return $html;
    }

    function makeTicketMergeView($latestmessage,$primaryid,$secondaryid,$primarymessage,$secondarymessage,$secondaryticketdata,$primarywithticketdata){
                if (!empty($secondaryticketdata)) {
                    $html ='
                    <form id="jsst-ticket-merge-form" method="post">
                        <div class="js-ticket-merge-ticket-wrapper">
                            <div class="js-col-xs-12 js-col-md-12 js-ticket-wrapper js-ticket-merge-white-bg">
                                <div class="js-col-xs-12 js-col-md-12 js-ticket-toparea">
                                    <div class="js-col-md-2 js-col-xs-12 js-ticket-pic">';
                                        if ($secondaryticketdata->staffphoto){
                                            $html .='<img class="js-ticket-staff-img" src=" '. JURI::root(). $datadirectory . "/staffdata/staff_" . $secondaryticketdata->staffid . "/" . $secondaryticketdata->staffphoto .' ">';
                                        }else {
                                            $html .='<img class="js-ticket-staff-img" src="' . JURI::root().'components/com_jssupportticket/include/images/user.png" />';
                                        }; $html .='
                                    </div>
                                    <div class="js-col-md-6 js-col-xs-6 js-ticket-data js-nullpadding">
                                        <div class="js-col-md-12 js-col-xs-12 js-ticket-padding-xs js-ticket-body-data-elipses subject">
                                            <span class="js-ticket-title">
                                               '.JText::_('Subject').':&nbsp;:&nbsp
                                            </span>
                                            <a class="js-ticket-merge-ticket-title">' .$secondaryticketdata->subject.' </a>
                                        </div>
                                        <div class="js-col-md-12 js-col-xs-12 js-ticket-padding-xs js-ticket-body-data-elipses">
                                            <span class="js-ticket-title">' .JText::_('From').':&nbsp;:&nbsp;</span>
                                            <span class="js-ticket-value" style="cursor:pointer;">'. $secondaryticketdata->name.'</span>
                                        </div>
                                        <div class="js-col-md-12 js-col-xs-12 js-ticket-padding-xs js-ticket-body-data-elipses">
                                            <span class="js-ticket-title">' .JText::_('Department').':&nbsp;:&nbsp;</span>
                                            <span class="js-ticket-value" style="cursor:pointer;">'.$secondaryticketdata->departmentname.'</span>
                                        </div>
                                    </div>
                                    <div class="js-col-md-4 js-col-xs-4 js-ticket-data1 js-ticket-padding-left-xs">
                                        <div class="js-row">
                                            <div class="js-col-md-6 js-col-xs-6">'.JText::_('Ticket ID').' : # '.'</div>
                                            <div class="js-col-md-6 js-col-xs-6">'. $secondaryticketdata->id.'</div>
                                        </div>
                                        <div class="js-row">
                                            <div class="js-col-md-6 js-col-xs-6">'.JText::_('Created').': '.'</div>
                                            <div class="js-col-md-6 js-col-xs-6">'.date( "Y-m-d", strtotime($secondaryticketdata->created)).'</div>
                                        </div>
                                        <div class="js-row">
                                            <div class="js-col-md-6 js-col-xs-6">'.JText::_('Priority').': '.'</div>
                                            <div class="js-col-md-6 js-col-xs-6"><span class="js-ticket-wrapper-textcolor" style="background:' .$secondaryticketdata->prioritycolour.'">'. JText::_($secondaryticketdata->priorityname).'</span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="js-edit-msg-field-wrp">
                                <span class="js-edit-msg-heading">' .JText::_('Edit Last Reply'). '</span>
                                <textarea class="inputbox js-merge-field" id="mergeticketid" name="secondarymessage" cols="" rows="3" >'.$secondarymessage.'</textarea>
                            </div>
                        </div>';
                }
                        if (!empty($primarywithticketdata)) {
                            $html .='
                                <div class="js-col-md-12 js-view-tickets js-view-last-tickets">
                                    <span class="js-heading js-heading-text">'. JText::_('Merge Ticket Latest Reply') . '</span>
                                    <div class="js-col-xs-12 js-col-md-12 js-ticket-wrapper js-ticket-merge-white-bg">
                                        <div class="js-col-xs-12 js-col-md-12 js-ticket-toparea">
                                            <div class="js-col-xs-2 js-col-md-2 js-ticket-pic">';
                                                if ($primarywithticketdata->staffphoto){;
                                                        $html .='<img class="js-ticket-staff-img" src=" '. JURI::root(). $datadirectory . "/staffdata/staff_" . $primarywithticketdata->staffid . "/" . $primarywithticketdata->staffphoto .' ">';
                                                    }else {
                                                        $html .='<img class="js-ticket-staff-img" src="' . JURI::root().'components/com_jssupportticket/include/images/user.png" />';
                                                    };
                                            $html .='</div>
                                            <div class="js-col-md-6 js-col-xs-6 js-ticket-data js-nullpadding">
                                                <div class="js-col-md-12 js-col-xs-12 js-ticket-padding-xs js-ticket-body-data-elipses subject">
                                                    <span class="js-ticket-title">
                                                       '.JText::_('Subject').':&nbsp;:&nbsp
                                                    </span>
                                                    <a class="js-ticket-merge-ticket-title">' .$primarywithticketdata->subject.' </a>
                                                </div>
                                                <div class="js-col-md-12 js-col-xs-12 js-ticket-padding-xs js-ticket-body-data-elipses">
                                                    <span class="js-ticket-title">' .JText::_('From').':&nbsp;:&nbsp;</span>
                                                    <span class="js-ticket-value" style="cursor:pointer;">'. $primarywithticketdata->name.'</span>
                                                </div>
                                                <div class="js-col-md-12 js-col-xs-12 js-ticket-padding-xs js-ticket-body-data-elipses">
                                                    <span class="js-ticket-title">' .JText::_('Department').':&nbsp;:&nbsp;</span>
                                                    <span class="js-ticket-value" style="cursor:pointer;">'.$primarywithticketdata->departmentname.'</span>
                                                </div>
                                            </div>
                                            <div class="js-col-md-4 js-col-xs-4 js-ticket-data1 js-ticket-padding-left-xs">
                                                <div class="js-row">
                                                    <div class="js-col-md-6 js-col-xs-6">'.JText::_('Ticket ID').' : # '.'</div>
                                                    <div class="js-col-md-6 js-col-xs-6">'. $primarywithticketdata->id.'</div>
                                                </div>
                                                <div class="js-row">
                                                    <div class="js-col-md-6 js-col-xs-6">'.JText::_('Created').': '.'</div>
                                                    <div class="js-col-md-6 js-col-xs-6">'.date( "Y-m-d", strtotime($primarywithticketdata->created)).'</div>
                                                </div>
                                                <div class="js-row">
                                                    <div class="js-col-md-6 js-col-xs-6">'.JText::_('Priority').': '.'</div>
                                                    <div class="js-col-md-6 js-col-xs-6"><span class="js-ticket-wrapper-textcolor" style="background:' .$primarywithticketdata->prioritycolour.'">'. JText::_($primarywithticketdata->priorityname).'</span></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="js-edit-msg-field-wrp">
                                        <span class="js-edit-msg-heading">' .JText::_('Edit Last Reply'). '</span>
                                        <textarea class="inputbox js-merge-field" id="primaryid" name="primarymessage" cols="" rows="3" >'.$primarymessage."\r\n\r\n".$latestmessage.'</textarea>
                                    </div>';
                                $html .=' </div>';
                        }
                        $html .='
                            <div class="js-col-md-12 js-form-button-wrapper js-form-button-wrapper-merge">
                                <input type="submit" value=' . JText::_('Merge') . ' class="button js-merge-save-btn"/>
                            </div>
                            <input type="hidden" name="c" id="c" value="ticket" />
                            <input type="hidden" name="view" id="view" value="ticket" />
                            <input type="hidden" name="layout" id="layout" value="ticketdetail" />
                            <input type="hidden" name="task" id="task" value="mergeticket" />
                            <input type="hidden" name="primaryticket" id="primaryticket" value="'.$primaryid.'" />
                            <input type="hidden" name="secondaryticket" id="secondaryticket" value="'.$secondaryid.'" />
                            '.JHtml::_('form.token').'
                    </form>';
        return $html;
    }

    function storeMergeTicket($arraydata){
        if(empty($arraydata))
            return false;
        $db = JFactory::getDbo();
        $data = array();
        $user = JSSupportticketCurrentUser::getInstance();
        $staffid = $user->getStaffId();
        if($arraydata['primaryticket'] == $arraydata['secondaryticket']){
            return SAVE_ERROR;
        }

        $query = "SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE mergestatus = 1 AND (status = 5 OR status = 4) AND id=" .$arraydata['secondaryticket'];
        $db->setQuery($query);
        $result = $db->loadResult();
        if($result == 0){
            $curdate = date('Y-m-d H:i:s');
            $user = JSSupportticketCurrentUser::getInstance();
            $uname = $user->getName();
            $multimerge = array($arraydata['secondaryticket']);
            $query = "SELECT multimergeparams FROM `#__js_ticket_tickets` WHERE id =" .$arraydata['primaryticket'];
            $db->setQuery($query);
            $array = $db->loadResult();
            if(empty($array)){
                $mergeticketvalues[] = array("uid" => $user->getId(), "mergedate" => $curdate, "ticketid" => $arraydata['secondaryticket']);
                $array = json_encode($mergeticketvalues);
            }else{
                $mergeticketvalues = array("uid" => $user->getId(), "mergedate" => $curdate, "ticketid" => $arraydata['secondaryticket']);
                $array = json_decode($array);
                array_push($array, $mergeticketvalues);
                $array = json_encode($array);
            }

            $data['id'] = $arraydata['secondaryticket'];
            $data['mergestatus'] = 1;
            $data['mergedate'] = $curdate;
            $data['mergewith'] = $arraydata['primaryticket'];
            $data['status'] = 5;
            $data['mergeuid'] = $user->getId();
            $data['update'] = $curdate;
            $data['overdue'] = 0;

            $status = 1;
            $row = $this->getTable('tickets');
            if (!$row->bind($data)) {
                $this->setError($row->getError());
                die($row->getError());
                $status = 0;
            }
            if(!$data['id'])
            if (!$row->check($data)) {
                $this->setError($row->getError());
                die($row->getError());
            }
            if (!$row->store()) {
                $this->getJSModel('systemerrors')->updateSystemErrors($row->getError());
                $this->setError($row->getError());
                $status = 0;
            }

            $data = array();
            $data['id'] = $arraydata['primaryticket'];
            $data['multimergeparams'] = $array;
            $data['update'] = $curdate;
            $row = $this->getTable('tickets');
            if (!$row->bind($data)) {
                $this->setError($row->getError());
                $status = 0;
            }
            if(!$data['id'])
            if (!$row->check()) {
                $this->setError($row->getError());
                $status = 0;
            }
            if (!$row->store()) {
                $this->getJSModel('systemerrors')->updateSystemErrors($row->getError());
                $this->setError($row->getError());
                $status = 0;
            }

            $secondaryurl = JText::_('<a class="js-ticket-merge-link" href="index.php?option=com_jssupportticket&c=ticket&layout=ticketdetail&id='.$arraydata['secondaryticket'].'">#'.$arraydata['secondaryticket'].'</a>');
            $primaryurl = JText::_('<a class="js-ticket-merge-link" href="index.php?option=com_jssupportticket&c=ticket&layout=ticketdetail&id='.$arraydata['primaryticket'].'">#'.$arraydata['primaryticket'].'</a>');
            $secondarymessage = $arraydata['secondarymessage'].'('.$primaryurl.')';
            $primarymessage = $arraydata['primarymessage'].'('.$secondaryurl.')';
            $status = $this->storeMergeTicketReplies(htmlspecialchars($secondarymessage),$arraydata['secondaryticket'],$staffid);
            $status = $this->storeMergeTicketReplies(htmlspecialchars($primarymessage),$arraydata['primaryticket'],$staffid);
        }else{
            $status = 0;
        }
        if($status == 1){
            return SAVED;
        }elseif($status == 0){
            return SAVE_ERROR;
        }
    }

    function storeMergeTicketReplies($reply,$ticketid,$staffid){
        if(!is_string($reply))
            return false;
        $user = JSSupportticketCurrentUser::getInstance();
        $uname = $user->getName();
        $row = $this->getTable('replies');
        $data['ticketid'] = $ticketid;
        $data['staffid'] = $staffid;
        $data['name'] = $uname;
        $data['message'] = $reply;
        $data['status'] = 1;
        $data['created'] = date('Y-m-d H:i:s');
        $data['mergemessage'] = 1;
        $return_value = 1;
        if (!$row->bind($data)) {
            $this->setError($row->getError());
            $return_value = 0;
        }
        if (!$row->check()) {
            $this->setError($row->getError());
            $return_value = 0;
        }
        if (!$row->store()) {
            $this->getJSModel('systemerrors')->updateSystemErrors($row->getError());
            $this->setError($row->getError());
            $return_value = 0;
        }
        return $return_value;
    }

    function removeTicketReplies($ticketid) {
        if(!is_numeric($ticketid)) return false;
        $db = $this->getDBo();
        $query = "DELETE FROM `#__js_ticket_replies` WHERE ticketid = ".$ticketid;
        $db->setQuery($query);
        $db->execute($query);
        return;
    }

    static function generateHash($id){
        if(!is_numeric($id))
            return null;
        return base64_encode(json_encode(base64_encode($id)));
    }

    function getUserMyTicketsForCP() {

        $db = $this->getDBO();
        $user = JSSupportticketCurrentUser::getInstance();
        if($user->getIsGuest())
            return false;
        $query = "SELECT ticket.id AS ticketid,ticket.subject,ticket.name,ticket.status,ticket.created,dep.departmentname AS departmentname, priority.priority AS priority, priority.prioritycolour AS prioritycolour,
                    concat(staff.firstname,' ',staff.lastname) AS staffname
                        FROM `#__js_ticket_tickets` AS ticket
                        JOIN `#__js_ticket_priorities` AS priority ON ticket.priorityid = priority.id
                        LEFT JOIN `#__js_ticket_departments` AS dep ON ticket.departmentid = dep.id
                        LEFT JOIN `#__js_ticket_staff` AS staff ON ticket.staffid = staff.id
                        WHERE ticket.uid = " .$user->getId();
        $query .= " ORDER BY ticket.created DESC";
        $db->setQuery($query,0,4);
        $result = $db->loadObjectList(); //Tickets
        return $result;
    }

    function getDefaultTicketSorting($value=2){ // 2 for query
        $ticketsorting = $this->getJSModel('config')->getConfigurationByName('tickets_sorting');
        if($ticketsorting == 1){
            $sort = "ASC";
        }else{
            $sort = "DESC";
        }
        if($value == 1) // 1 for showing value in html
            $sort = strtolower($sort);
        return $sort;
    }

    function getUserRemainMaxticket(){
        $uid = $this->_jinput->post->get('uid' , 0);
        if($uid == 0){
            $user = JSSupportticketCurrentUser::getInstance();
            if($user->getIsStaff())
                return false;
            $uid = $user->getId();

        }
        if(!is_numeric($uid))
            return false;
        $db = $this->getDbo();

        $config_ticket = $this->getJSModel('config')->getConfigByFor('default');
        $maxticketinterval = $config_ticket['maximum_ticket_interval_time'];
        switch($maxticketinterval){
            case "1": // maximum ticket in a day
                $checkdate = " AND date(created) = " . $db->quote(date('Y-m-d'));
                $msg = JText::_("The maximum remaining ticket(s) in a day is %s .");
            break;
            case "2": // maximum ticket in month
                $checkdate = " AND MONTH(created) = " . $db->quote(date('m',strtotime(date('Y-m-d'))));
                $msg = JText::_("The maximum remaining ticket(s) in a month is %s .");
            break;
            case "3": // maximum ticket in year
                $checkdate = " AND YEAR(created) = " . $db->quote(date('Y',strtotime(date('Y-m-d'))));
                $msg = JText::_("The maximum remaining ticket(s) in a year is %s .");
            break;
            case "4": // maximum ticket in life time
                $checkdate = "";
                $msg = JText::_("The maximum remaining ticket(s) are %s .");
            break;
        }

        $query = "SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE uid = " . $uid . $checkdate; // ticket not answer and not closed
        $db->setQuery($query);
        $total = $db->loadResult();
        $ticketperemail = $config_ticket['maximum_ticket'];
        if ($total >= $ticketperemail) {
            $remaining = 0;
        }else{
            $remaining = $ticketperemail - $total;
        }
        $msg = sprintf($msg , $remaining);
        $html = '
        <div class="alert alert-success">
            <a class="close" data-dismiss="alert">×</a>
            <h4 class="alert-heading">Message</h4>
            <div>
                <div class="alert-message">'. $msg .'</div>
            </div>
        </div>
        ';
        return $html;
    }
}
?>
